<?php

namespace App\Domains\SalesInvoice\Actions;

use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use App\Domains\SalesInvoice\Services\SalesInvoicePaymentService;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Services\SalesOrderRevenueSplitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\ErrorCodes;
use App\Exceptions\AppException;

class PaymentSalesInvoiceAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Handle file upload

            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['attachment']->store('sales_invoice', 'public');
                $data['attachment'] = $path;
            }

            // Get sales order info
            $invoice = app(SalesInvoiceService::class)->findOne($data['sales_invoice_id']);
            $salesOrderId = $invoice->f_sales_order_id;

            // Check if revenue split exists or can be created
            $products = app(SalesOrderService::class)->findProductLines($salesOrderId, true);
            $products = is_array($products) ? $products : json_decode(json_encode($products), true);

            // Check each product - must have either existing split OR revenue split config
            foreach ($products as $product) {
                $existingSplit = DB::table('t_sales_order_revenue_splits')
                    ->where('f_sales_order_id', $salesOrderId)
                    ->where('f_product_id', $product['f_product_id'])
                    ->first();

                if (!$existingSplit) {
                    // Check if product has revenue split configuration
                    $config = DB::table('m_product_revenue_split_configs')
                        ->where('company_code', session('user_company_code'))
                        ->where('f_product_id', $product['f_product_id'])
                        ->where('status', 'ACTIVE')
                        ->first();

                    if (!$config) {
                        // No split exists and no config available - cannot settle
                        throw new AppException(
                            ErrorCodes::FORM_VALIDATION_ERROR,
                            'Please setup revenue settlement config'
                        );
                    }
                }
            }

            // Create sales invoice payment record
            $paymentId = app(SalesInvoicePaymentService::class)->create($data);

            app(SalesInvoiceService::class)->updateStatusIfFullyPaid($data['sales_invoice_id']);
            // Create audit log
            app(SalesInvoiceAuditLogService::class)->create($data["sales_invoice_id"], 'UPDATE', 'Sales Invoice Pay', $data);
            $data['sales_order_id'] = $salesOrderId;
            $data['products'] = $products;

            foreach ($data['products'] as $product) {
                // Check if revenue split already exists for this sales order and product
                $existingSplit = DB::table('t_sales_order_revenue_splits')
                    ->where('f_sales_order_id', $data['sales_order_id'])
                    ->where('f_product_id', $product['f_product_id'])
                    ->first();

                // Only create revenue split if:
                // 1. It doesn't exist yet, OR
                // 2. It exists but is not a custom split (allow auto-creation for non-custom)
                // Do NOT overwrite custom splits entered by user
                if (!$existingSplit) {
                    $product['product_id'] = $product['f_product_id'];
                    $product['quantity'] = $product['quantity'];
                    $product['id'] = $product['id'];
                    app(SalesOrderRevenueSplitService::class)->createRevenueSplitForProduct(
                        $data['sales_order_id'],
                        $product,
                        session('user_company_code'),
                        Auth::id()
                    );
                }
                // If existing split exists and is custom, leave it untouched
            }

            // After revenue splits are created/verified, automatically allocate top-up balance
            $this->allocateTopUpBalanceToSalesOrder($data['sales_order_id']);

            return $paymentId;
        });
    }

    /**
     * Automatically allocate top-up remaining balance to Sales Order using FIFO
     */
    private function allocateTopUpBalanceToSalesOrder(int $salesOrderId): void
    {
        $companyCode = session('user_company_code');

        // Get revenue splits for this SO
        $revenueSplits = DB::table('t_sales_order_revenue_splits')
            ->where('f_sales_order_id', $salesOrderId)
            ->where('company_code', $companyCode)
            ->get();

        if ($revenueSplits->isEmpty()) {
            return;
        }

        // Process KINGDOM entity type (porsi_perusahaan)
        $this->allocateTopUpForEntityType($salesOrderId, 'KINGDOM', $revenueSplits);

        // Process OWNER entity type (porsi_owner)
        $this->allocateTopUpForEntityType($salesOrderId, 'OWNER', $revenueSplits);
    }

    /**
     * Allocate top-up balance for a specific entity type
     */
    private function allocateTopUpForEntityType(int $salesOrderId, string $entityType, $revenueSplits): void
    {
        $companyCode = session('user_company_code');

        // Determine which fields to use based on entity type
        $porsiField = $entityType === 'KINGDOM' ? 'porsi_perusahaan' : 'porsi_owner';
        $isPaidField = $entityType === 'KINGDOM' ? 'is_paid_perusahaan' : 'is_paid_owner';

        // Calculate total unpaid amount for this entity type
        $totalUnpaidAmount = $revenueSplits->sum(function ($split) use ($isPaidField, $porsiField) {
            return $split->$isPaidField ? 0 : $split->$porsiField;
        });

        if ($totalUnpaidAmount <= 0) {
            return; // Nothing to allocate
        }

        // Get available top-ups with remaining balance (FIFO order)
        $availableTopUps = DB::table('t_topup')
            ->where('company_code', $companyCode)
            ->where('entity_type', $entityType)
            ->where('remaining_balance', '>', 0)
            ->orderBy('topup_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        if ($availableTopUps->isEmpty()) {
            return; // No available top-ups
        }

        // FIFO allocation
        $remainingToAllocate = $totalUnpaidAmount;
        $allocationSequence = 1;

        foreach ($availableTopUps as $topup) {
            if ($remainingToAllocate <= 0) {
                break;
            }

            // Calculate how much we can allocate from this top-up
            $allocatedAmount = min($topup->remaining_balance, $remainingToAllocate);

            // Get current SO remaining amount (before allocation)
            $soRemainingBefore = $revenueSplits->sum(function ($split) use ($isPaidField, $porsiField) {
                return $split->$isPaidField ? 0 : $split->$porsiField;
            });

            // Create allocation record
            DB::table('t_topup_sales_order')->insert([
                'company_code' => $companyCode,
                'topup_id' => $topup->id,
                'sales_order_id' => $salesOrderId,
                'allocated_amount' => $allocatedAmount,
                'so_total_amount' => $totalUnpaidAmount,
                'so_remaining_before' => $soRemainingBefore,
                'so_remaining_after' => max(0, $soRemainingBefore - $allocatedAmount),
                'allocation_sequence' => $allocationSequence,
                'notes' => "Auto-allocated from Top Up {$topup->topup_number} for {$entityType}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update top-up remaining balance
            $newRemainingBalance = $topup->remaining_balance - $allocatedAmount;
            $newAllocatedAmount = $topup->allocated_amount + $allocatedAmount;

            // Determine new status
            $newStatus = 'OPEN';
            if ($newRemainingBalance <= 0) {
                $newStatus = 'PAID'; // Fully allocated
            } elseif ($newAllocatedAmount > 0) {
                $newStatus = 'PARTIAL'; // Partially allocated
            }

            DB::table('t_topup')
                ->where('id', $topup->id)
                ->update([
                    'allocated_amount' => $newAllocatedAmount,
                    'remaining_balance' => $newRemainingBalance,
                    'status' => $newStatus,
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            $remainingToAllocate -= $allocatedAmount;
            $allocationSequence++;
        }

        // If we allocated the full amount, mark revenue splits as paid
        if ($remainingToAllocate <= 0) {
            DB::table('t_sales_order_revenue_splits')
                ->where('f_sales_order_id', $salesOrderId)
                ->where('company_code', $companyCode)
                ->update([
                    $isPaidField => true,
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);
        }
    }
}
