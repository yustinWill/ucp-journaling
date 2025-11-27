<?php

namespace App\Helpers;

use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use Illuminate\Support\Facades\View;

class PdfHelper
{
    /**
     * Generate PDF for different template types
     *
     * @param string $templateType - Type of template (sales_invoice, sales_delivery, etc.)
     * @param int $id - ID of the record
     * @return string - HTML content for PDF
     */
    public static function generatePdf(string $templateType, int $id): string
    {
        switch ($templateType) {
            case 'sales_invoice':
                return self::generateSalesInvoicePdf($id);

            case 'sales_delivery':
                return self::generateSalesDeliveryPdf($id);

            default:
                throw new \Exception("Template type '{$templateType}' tidak didukung.");
        }
    }

    /**
     * Generate Sales Invoice PDF
     *
     * @param int $id - Sales Invoice ID
     * @return string - HTML content
     */
    private static function generateSalesInvoicePdf(int $id): string
    {
        $invoice_data = app(SalesInvoiceService::class)->findOne($id);

        if (!$invoice_data) {
            throw new \Exception("Sales Invoice dengan ID {$id} tidak ditemukan.");
        }

        $invoice_products = app(SalesInvoiceService::class)->findAllProducts($id);

        $data = [
            'invoice_data' => $invoice_data,
            'invoice_products' => $invoice_products,
        ];

        return View::make('pdf.sales_invoice', $data)->render();
    }

    /**
     * Generate Sales Delivery PDF
     *
     * @param int $id - Sales Delivery ID
     * @return string - HTML content
     */
    private static function generateSalesDeliveryPdf(int $id): string
    {
        // Check if SalesDeliveryService exists
        if (!class_exists('App\Domains\SalesDelivery\Services\SalesDeliveryService')) {
            throw new \Exception("Sales Delivery Service tidak ditemukan.");
        }

        $delivery_data = app(SalesDeliveryService::class)->findOne($id);

        if (!$delivery_data) {
            throw new \Exception("Sales Delivery dengan ID {$id} tidak ditemukan.");
        }

        $delivery_products = app(SalesDeliveryService::class)->findAllProducts($id);

        $data = [
            'delivery_data' => $delivery_data,
            'delivery_products' => $delivery_products,
        ];

        return View::make('pdf.sales_delivery', $data)->render();
    }

    /**
     * Get printable HTML for opening in new tab
     *
     * @param string $templateType
     * @param int $id
     * @return string
     */
    public static function getPrintableHtml(string $templateType, int $id): string
    {
        $content = self::generatePdf($templateType, $id);

        // Wrap content with print-friendly HTML
        return "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Print Document</title>
            <style>
                @media print {
                    @page {
                        size: A4;
                        margin: 1cm;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                    .no-print {
                        display: none;
                    }
                }
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.6;
                    color: #333;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 10px 0;
                }
                table th, table td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                table th {
                    background-color: #f5f5f5;
                    font-weight: bold;
                }
                .text-right {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .info-section {
                    margin: 15px 0;
                }
                .row {
                    display: flex;
                    margin: 5px 0;
                }
                .col {
                    flex: 1;
                    padding: 5px;
                }
                .fw-bold {
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class='no-print' style='text-align: center; padding: 10px; background: #f5f5f5; margin-bottom: 20px;'>
                <button onclick='window.print()' style='padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px;'>
                    Print Document
                </button>
                <button onclick='window.close()' style='padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 4px; margin-left: 10px;'>
                    Close
                </button>
            </div>
            {$content}
        </body>
        </html>
        ";
    }
}
