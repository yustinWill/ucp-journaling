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
                            ErrorCodes::VALIDATION_ERROR,
                            'Please setup revenue settlement config for all products before settling the invoice'
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

            return $paymentId;
        });
    }
}
