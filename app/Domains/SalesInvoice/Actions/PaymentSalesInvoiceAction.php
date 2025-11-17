<?php

namespace App\Domains\SalesInvoice\Actions;

use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use App\Domains\SalesInvoice\Services\SalesInvoicePaymentService;
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


            // Create audit log
            app(SalesInvoiceAuditLogService::class)->create($data["sales_invoice_id"], 'UPDATE', 'Sales Invoice Pay', $data);

            // foreach ($data['products'] as $product) {
            //     $product['f_product_id'] = $product['product_id'];
            //     $product['quantity'] = $product['invoice_qty'];
            //     $product['id'] = $product['sales_order_product_id'];
            //     app(SalesOrderRevenueSplitService::class)->createRevenueSplitForProduct(
            //         $data['sales_order_id'],
            //         $product,
            //         session('user_company_code'),
            //         Auth::id()
            //     );
            // }

            return $paymentId;
        });
    }
}
