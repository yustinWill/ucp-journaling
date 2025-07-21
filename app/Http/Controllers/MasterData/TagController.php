<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Domains\Tag\Services\TagColorService;
use Illuminate\Http\Request;
use App\Domains\Tag\Actions\CreateTagAction;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Helpers\ValidationHelper;

class TagController extends Controller
{
    private $availableStatus = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif']
    ];

    public function index()
    {
        // Available colors for filter dropdown
        $availableColors = app(TagColorService::class)->findAll();

        // Available status options
        $availableStatus = $this->availableStatus;

        return view('pages.master_data.tag.index', compact('availableColors', 'availableStatus'));
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "code" => "PENJUALAN",
                    "name" => "Transaksi Penjualan",
                    "color" => "success",
                    "description" => "Tag untuk semua transaksi penjualan produk RIYO Water",
                    "status" => "ACTIVE",
                    "usage_count" => 145,
                    "created_by" => "Admin System",
                    "created_at" => "2025-01-15 08:00:00",
                ],
                [
                    "id" => 2,
                    "code" => "PEMBELIAN",
                    "name" => "Transaksi Pembelian",
                    "color" => "info",
                    "description" => "Tag untuk semua transaksi pembelian bahan baku dan operasional",
                    "status" => "ACTIVE",
                    "usage_count" => 89,
                    "created_by" => "Admin System",
                    "created_at" => "2025-01-15 08:05:00",
                ],
                [
                    "id" => 3,
                    "code" => "OPERASIONAL",
                    "name" => "Biaya Operasional",
                    "color" => "warning",
                    "description" => "Tag untuk biaya operasional harian seperti listrik, gaji, maintenance",
                    "status" => "ACTIVE",
                    "usage_count" => 67,
                    "created_by" => "Finance Manager",
                    "created_at" => "2025-01-15 09:10:00",
                ],
                [
                    "id" => 4,
                    "code" => "INVESTASI",
                    "name" => "Investasi & Aset",
                    "color" => "primary",
                    "description" => "Tag untuk transaksi investasi dan pembelian aset perusahaan",
                    "status" => "ACTIVE",
                    "usage_count" => 23,
                    "created_by" => "Finance Manager",
                    "created_at" => "2025-01-15 10:15:00",
                ],
                [
                    "id" => 5,
                    "code" => "KEUANGAN",
                    "name" => "Transaksi Keuangan",
                    "color" => "secondary",
                    "description" => "Tag untuk transaksi keuangan umum dan transfer dana",
                    "status" => "ACTIVE",
                    "usage_count" => 112,
                    "created_by" => "Finance Manager",
                    "created_at" => "2025-01-15 11:20:00",
                ],
                [
                    "id" => 6,
                    "code" => "PAJAK",
                    "name" => "Transaksi Pajak",
                    "color" => "danger",
                    "description" => "Tag untuk semua transaksi terkait pajak dan kewajiban negara",
                    "status" => "ACTIVE",
                    "usage_count" => 34,
                    "created_by" => "Accounting Staff",
                    "created_at" => "2025-01-15 14:25:00",
                ],
                [
                    "id" => 7,
                    "code" => "KOREKSI",
                    "name" => "Koreksi & Penyesuaian",
                    "color" => "dark",
                    "description" => "Tag untuk transaksi koreksi dan penyesuaian jurnal",
                    "status" => "ACTIVE",
                    "usage_count" => 12,
                    "created_by" => "Accounting Staff",
                    "created_at" => "2025-01-15 15:30:00",
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        $availableColors = app(TagColorService::class)->findAll();
        $availableStatus = $this->availableStatus;
        return view('pages.master_data.tag.add', compact('availableColors', 'availableStatus'));
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255|unique:m_tags,code',
                'status' => [
                    'required',
                    Rule::in(array_column($this->availableStatus, 'value'))
                ],
                'f_tag_color_id' => 'required|exists:m_tag_colors,id',
                'description' => 'nullable|string|max:255',
            ]);

            app(CreateTagAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_TAG_SUCCESS->message()
            ], SuccessCodes::CREATE_TAG_SUCCESS->code());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], 500);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function edit($id)
    {
        // Sample tag data for editing
        $tag = [
            'id' => $id,
            'code' => 'PENJUALAN',
            'name' => 'Transaksi Penjualan',
            'color' => 'success',
            'description' => 'Tag untuk semua transaksi penjualan produk RIYO Water kepada distributor dan retailer. Termasuk penjualan tunai maupun kredit.',
            'status' => 'ACTIVE',
            'usage_count' => 145
        ];

        $availableColors = app(TagColorService::class)->findAll();

        return view('pages.master_data.tag.edit', compact('tag', 'availableColors'));
    }

    public function detail($id)
    {
        // Sample tag data for detail view
        $tag = [
            'id' => $id,
            'code' => 'PENJUALAN',
            'name' => 'Transaksi Penjualan',
            'color' => 'success',
            'description' => 'Tag untuk semua transaksi penjualan produk RIYO Water kepada distributor dan retailer. Termasuk penjualan tunai maupun kredit.',
            'status' => 'ACTIVE',
            'usage_count' => 145,
            'created_by' => 'Admin System',
            'created_at' => '2025-01-15 08:00:00',
            'updated_by' => 'Finance Manager',
            'updated_at' => '2025-01-18 14:30:00',
            'recent_transactions' => [
                [
                    'id' => 'JU-2025-001',
                    'module' => 'Journal',
                    'description' => 'Penjualan RIYO Water 330ml - CV Berkah Sentosa',
                    'amount' => 'Rp 12,500,000',
                    'date' => '2025-01-20',
                    'created_by' => 'Finance Manager'
                ],
                [
                    'id' => 'SO-2025-005',
                    'module' => 'Sales Order',
                    'description' => 'Penjualan RIYO Water 600ml - Supermarket Maju Jaya',
                    'amount' => 'Rp 8,750,000',
                    'date' => '2025-01-19',
                    'created_by' => 'Sales Manager'
                ],
                [
                    'id' => 'SI-2025-012',
                    'module' => 'Sales Invoice',
                    'description' => 'Penjualan RIYO Water 1500ml - Hotel Santika',
                    'amount' => 'Rp 5,200,000',
                    'date' => '2025-01-18',
                    'created_by' => 'Sales Staff'
                ]
            ],
            'audit_logs' => [
                [
                    'created_at' => '2025-01-18 14:30:25',
                    'user_id' => 'USR-002',
                    'user_name' => 'Finance Manager',
                    'action_type' => 'UPDATE',
                    'description' => 'Memperbarui deskripsi tag untuk memperjelas cakupan penggunaan'
                ],
                [
                    'created_at' => '2025-01-16 09:15:10',
                    'user_id' => 'USR-001',
                    'user_name' => 'Admin System',
                    'action_type' => 'UPDATE',
                    'description' => 'Mengubah warna badge dari primary menjadi success'
                ],
                [
                    'created_at' => '2025-01-15 08:00:00',
                    'user_id' => 'USR-001',
                    'user_name' => 'Admin System',
                    'action_type' => 'CREATE',
                    'description' => 'Membuat tag baru untuk kategori transaksi penjualan'
                ]
            ]
        ];

        return view('pages.master_data.tag.detail', compact('tag'));
    }
}
