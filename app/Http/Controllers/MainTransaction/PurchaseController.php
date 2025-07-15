<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('pages.main_transaction.purchase.index', []);
    }

    public function index_data_table(Request $request)
    {
        $tab = $request->get('tab', 'pesanan');
        
        switch ($tab) {
            case 'pesanan':
                return $this->getPesananData();
            case 'pengiriman':
                return $this->getPengirimanData();
            case 'faktur':
                return $this->getFakturData();
            default:
                return $this->getPesananData();
        }
    }

    private function getPesananData()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-01",
                    "reference_no" => "PO-2025-001",
                    "supplier" => "PT. Sumber Makmur",
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dibayar",
                    "down_payment" => 3000000,
                    "total" => 15000000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "PO-2025-002",
                    "supplier" => "CV. Berkah Jaya",
                    "due_date" => "2025-07-16",
                    "status" => "Menunggu Pembayaran",
                    "down_payment" => 1700000,
                    "total" => 8500000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "PO-2025-003",
                    "supplier" => "PT. Mandiri Sejahtera",
                    "due_date" => "2025-07-17",
                    "status" => "Dibayar",
                    "down_payment" => 5000000,
                    "total" => 25000000,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "PO-2025-004",
                    "supplier" => "CV. Maju Bersama",
                    "due_date" => "2025-07-18",
                    "status" => "Dibayar Sebagian",
                    "down_payment" => 2400000,
                    "total" => 12000000,
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    private function getPengirimanData()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-02",
                    "reference_no" => "PD-2025-001",
                    "supplier" => "PT. Sumber Makmur",
                    "status" => "Belum Ditagih",
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-03",
                    "reference_no" => "PD-2025-002",
                    "supplier" => "CV. Berkah Jaya",
                    "status" => "Selesai",
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-04",
                    "reference_no" => "PD-2025-003",
                    "supplier" => "PT. Mandiri Sejahtera",
                    "status" => "Selesai",
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    private function getFakturData()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-01",
                    "reference_no" => "PI-2025-001",
                    "supplier" => "PT. Sumber Makmur",
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dibayar",
                    "remaining_bill" => 15000000,
                    "total" => 15000000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "PI-2025-002",
                    "supplier" => "CV. Berkah Jaya",
                    "due_date" => "2025-07-16",
                    "status" => "Menunggu Pembayaran",
                    "remaining_bill" => 8500000,
                    "total" => 8500000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "PI-2025-003",
                    "supplier" => "PT. Mandiri Sejahtera",
                    "due_date" => "2025-07-17",
                    "status" => "Dibayar",
                    "remaining_bill" => 0,
                    "total" => 25000000,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "PI-2025-004",
                    "supplier" => "CV. Maju Bersama",
                    "due_date" => "2025-07-18",
                    "status" => "Dibayar Sebagian",
                    "remaining_bill" => 7200000,
                    "total" => 12000000,
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function purchaseOrderAdd()
    {
        // Get all required options for the Purchase Order form
        $suppliers = [
            ['value' => '1', 'label' => 'PT. Sumber Makmur'],
            ['value' => '2', 'label' => 'CV. Berkah Jaya'],
            ['value' => '3', 'label' => 'PT. Mandiri Sejahtera'],
            ['value' => '4', 'label' => 'CV. Maju Bersama']
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
            ['value' => 'Cash on Delivery', 'label' => 'Cash on Delivery'],
            ['value' => 'Immediate', 'label' => 'Immediate']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Utama'],
            ['value' => '2', 'label' => 'Gudang Cabang A'],
            ['value' => '3', 'label' => 'Gudang Cabang B']
        ];

        $products = [
            ['value' => '1', 'label' => 'ALBUM SVT'],
            ['value' => '2', 'label' => 'Buku Tulis'],
            ['value' => '3', 'label' => 'Pensil'],
            ['value' => '4', 'label' => 'Penghapus']
        ];

        $taxOptions = [
            ['value' => 'PPN', 'label' => 'PPN'],
            ['value' => 'No Tax', 'label' => 'Tidak Ada Pajak']
        ];

        return view('pages.main_transaction.purchase.po_add', [
            'suppliers' => $suppliers,
            'paymentTerms' => $paymentTerms,
            'warehouses' => $warehouses,
            'products' => $products,
            'taxOptions' => $taxOptions
        ]);
    }

    public function purchaseDeliveryAdd()
    {
        // Get required options for the Purchase Delivery form
        $suppliers = [
            ['value' => '1', 'label' => 'PT. Sumber Makmur'],
            ['value' => '2', 'label' => 'CV. Berkah Jaya'],
            ['value' => '3', 'label' => 'PT. Mandiri Sejahtera'],
            ['value' => '4', 'label' => 'CV. Maju Bersama']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Utama'],
            ['value' => '2', 'label' => 'Gudang Cabang A'],
            ['value' => '3', 'label' => 'Gudang Cabang B']
        ];

        return view('pages.main_transaction.purchase.pd_add', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses
        ]);
    }

    public function purchaseInvoiceAdd()
    {
        // Get required options for the Purchase Invoice form
        $purchaseDeliveries = [
            ['value' => 'PD-2025-001', 'label' => 'PD-2025-001 - PT. Sumber Makmur'],
            ['value' => 'PD-2025-002', 'label' => 'PD-2025-002 - CV. Berkah Jaya'],
            ['value' => 'PD-2025-003', 'label' => 'PD-2025-003 - PT. Mandiri Sejahtera'],
            ['value' => 'PD-2025-004', 'label' => 'PD-2025-004 - CV. Maju Bersama']
        ];

        $currencies = [
            ['value' => 'IDR', 'label' => 'IDR - Rupiah'],
            ['value' => 'USD', 'label' => 'USD - Dollar'],
            ['value' => 'EUR', 'label' => 'EUR - Euro']
        ];

        $paymentMethods = [
            ['value' => 'Transfer Bank', 'label' => 'Transfer Bank'],
            ['value' => 'Cash', 'label' => 'Cash'],
            ['value' => 'Cek', 'label' => 'Cek'],
            ['value' => 'Giro', 'label' => 'Giro']
        ];

        $taxOptions = [
            ['value' => 'PPN', 'label' => 'PPN 11%'],
            ['value' => 'No Tax', 'label' => 'Tidak Ada Pajak']
        ];

        $costTypes = [
            ['value' => 'Biaya Kirim', 'label' => 'Biaya Kirim'],
            ['value' => 'Biaya Asuransi', 'label' => 'Biaya Asuransi'],
            ['value' => 'Biaya Bongkar', 'label' => 'Biaya Bongkar'],
            ['value' => 'Biaya Lainnya', 'label' => 'Biaya Lainnya']
        ];

        return view('pages.main_transaction.purchase.pi_add', [
            'purchaseDeliveries' => $purchaseDeliveries,
            'currencies' => $currencies,
            'paymentMethods' => $paymentMethods,
            'taxOptions' => $taxOptions,
            'costTypes' => $costTypes
        ]);
    }

    public function handleImport(Request $request)
    {
        $type = $request->get('type', 'pesanan');
        
        switch ($type) {
            case 'pesanan':
                // Handle Purchase Order import
                return $this->importPurchaseOrder($request);
            case 'pengiriman':
                // Handle Purchase Delivery import
                return $this->importPurchaseDelivery($request);
            case 'faktur':
                // Handle Purchase Invoice import
                return $this->importPurchaseInvoice($request);
            default:
                return response()->json(['error' => 'Invalid import type'], 400);
        }
    }

    public function handleExport(Request $request)
    {
        $type = $request->get('type', 'pesanan');
        
        switch ($type) {
            case 'pesanan':
                // Handle Purchase Order export
                return $this->exportPurchaseOrder($request);
            case 'pengiriman':
                // Handle Purchase Delivery export
                return $this->exportPurchaseDelivery($request);
            case 'faktur':
                // Handle Purchase Invoice export
                return $this->exportPurchaseInvoice($request);
            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }
    }

    private function importPurchaseOrder(Request $request)
    {
        // TODO: Implement Purchase Order import logic
        return response()->json(['message' => 'Purchase Order import functionality will be implemented']);
    }

    private function importPurchaseDelivery(Request $request)
    {
        // TODO: Implement Purchase Delivery import logic
        return response()->json(['message' => 'Purchase Delivery import functionality will be implemented']);
    }

    private function importPurchaseInvoice(Request $request)
    {
        // TODO: Implement Purchase Invoice import logic
        return response()->json(['message' => 'Purchase Invoice import functionality will be implemented']);
    }

    private function exportPurchaseOrder(Request $request)
    {
        // TODO: Implement Purchase Order export logic
        return response()->json(['message' => 'Purchase Order export functionality will be implemented']);
    }

    private function exportPurchaseDelivery(Request $request)
    {
        // TODO: Implement Purchase Delivery export logic
        return response()->json(['message' => 'Purchase Delivery export functionality will be implemented']);
    }

    private function exportPurchaseInvoice(Request $request)
    {
        // TODO: Implement Purchase Invoice export logic
        return response()->json(['message' => 'Purchase Invoice export functionality will be implemented']);
    }
}