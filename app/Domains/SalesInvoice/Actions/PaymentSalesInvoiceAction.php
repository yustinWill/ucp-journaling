<?php

namespace App\Domains\SalesInvoice\Actions;

use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use App\Domains\SalesInvoice\Services\SalesInvoicePaymentService;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Services\SalesOrderRevenueSplitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

            // Create sales invoice record
            $paymentId = app(SalesInvoicePaymentService::class)->create($data);

            app(SalesInvoiceService::class)->updateStatusIfFullyPaid($data['sales_invoice_id']);
            // Create audit log
            app(SalesInvoiceAuditLogService::class)->create($data["sales_invoice_id"], 'UPDATE', 'Sales Invoice Pay', $data);
            $data['sales_order_id'] = app(SalesInvoiceService::class)->findOne($data['sales_invoice_id'])->f_sales_order_id;
            $products = app(SalesOrderService::class)->findProductLines($data['sales_order_id'],true);
            $data['products'] = is_array($products) ? $products : json_decode(json_encode($products), true);

            foreach ($data['products'] as $product) {
                // Check if revenue split already exists for this sales order and product
                $existingSplit = DB::table('t_sales_order_revenue_splits')
                    ->where('f_sales_order_id', $data['sales_order_id'])
                    ->where('f_product_id', $product['f_product_id'])
                    ->exists();
                // Only create revenue split if it doesn't exist yet
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
            }

            return $paymentId;
        });
    }
}
