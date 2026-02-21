<?php

namespace App\Http\Controllers\MasterData;

use App\Domains\PaymentTerm\Services\PaymentTermService;
use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Enums\SuccessCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentTermController extends Controller
{
    private $table_name = 'm_payment_terms';

    public function index()
    {
        // Summary data
        $totalPaymentTerms = DB::table($this->table_name)->count();
        $activePaymentTerms = DB::table($this->table_name)->where('status', 'active')->count();
        $defaultPaymentTerm = DB::table($this->table_name)->where('is_default', true)->first();

        $statuses = [
            ['value' => 'active', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'inactive', 'label' => 'INACTIVE - Tidak Aktif'],
        ];

        $types = [
            ['value' => 'net', 'label' => 'NET - Net Payment'],
            ['value' => 'immediate', 'label' => 'IMMEDIATE - Immediate Payment'],
            ['value' => 'cod', 'label' => 'COD - Cash on Delivery'],
            ['value' => 'custom', 'label' => 'CUSTOM - Custom Terms'],
        ];

        // Check permissions
        $canAddPaymentTerm = true;
        $canImportPaymentTerm = true;
        $canExportPaymentTerm = true;

        $data = [
            'totalPaymentTerms' => $totalPaymentTerms,
            'activePaymentTerms' => $activePaymentTerms,
            'defaultPaymentTerm' => $defaultPaymentTerm ? $defaultPaymentTerm->name : 'Not Set',
            'statuses' => $statuses,
            'types' => $types,
            'canAddPaymentTerm' => $canAddPaymentTerm,
            'canImportPaymentTerm' => $canImportPaymentTerm,
            'canExportPaymentTerm' => $canExportPaymentTerm,
        ];

        $permissionData = PermissionHelper::getViewPermissions('master_data_payment_term');
        return view('pages.master_data.payment_term.index', array_merge($data, $permissionData));
    }

    public function indexDataTable(Request $request)
    {
        $data = app(PaymentTermService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif'],
        ];

        $types = [
            ['value' => 'NET', 'label' => 'NET - Net Payment'],
            ['value' => 'IMMEDIATE', 'label' => 'IMMEDIATE - Immediate Payment'],
            ['value' => 'COD', 'label' => 'COD - Cash on Delivery'],
            ['value' => 'CUSTOM', 'label' => 'CUSTOM - Custom Terms'],
        ];

        return view('pages.master_data.payment_term.add', [
            'statuses' => $statuses,
            'types' => $types,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:20|unique:m_payment_terms,code',
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'days' => 'required|integer|min:0',
                'type' => 'required|in:NET,IMMEDIATE,COD,CUSTOM',
                'status' => 'required|in:ACTIVE,INACTIVE',
                // 'is_default' => 'boolean',
            ], [], [
                'code' => 'Kode Syarat Pembayaran',
                'name' => 'Nama Syarat Pembayaran',
                'description' => 'Deskripsi Syarat Pembayaran',
                'days' => 'Jumlah Hari',
                'type' => 'Tipe Syarat Pembayaran',
                'status' => 'Status Syarat Pembayaran',
                // 'is_default' => 'Default Syarat Pembayaran',
            ]);

            DB::transaction(function () use ($validated) {
                // If setting as default, remove default from others
                if ($validated['is_default'] ?? false) {
                    DB::table($this->table_name)->update(['is_default' => false]);
                }

                DB::table($this->table_name)->insert([
                    'code' => strtoupper($validated['code']),
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'days' => $validated['days'],
                    'type' => $validated['type'],
                    'status' => $validated['status'],
                    'is_default' => $validated['is_default'] ?? false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            return response()->json([
                'message' => 'Syarat Pembayaran berhasil disimpan',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Kesalahan validasi: '.implode(', ', array_map(function ($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'error' => 'VALIDATION_ERROR',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => 'INTERNAL_SERVER_ERROR',
            ], 500);
        }
    }

    public function detail($id)
    {
        $paymentTerm = DB::table($this->table_name)->where('id', $id)->first();

        if (! $paymentTerm) {
            abort(404, 'Syarat Pembayaran tidak ditemukan.');
        }

        $data = [
            'paymentTerm' => $paymentTerm,
            'canEditPaymentTerm' => true,
            'canDeletePaymentTerm' => true,
        ];

        return view('pages.master_data.payment_term.detail', array_merge($data));
    }

    public function edit($id)
    {
        $paymentTerm = DB::table($this->table_name)->where('id', $id)->first();

        if (! $paymentTerm) {
            abort(404, 'Syarat Pembayaran tidak ditemukan.');
        }

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif'],
        ];

        $types = [
            ['value' => 'NET', 'label' => 'NET - Net Payment'],
            ['value' => 'IMMEDIATE', 'label' => 'IMMEDIATE - Immediate Payment'],
            ['value' => 'COD', 'label' => 'COD - Cash on Delivery'],
            ['value' => 'CUSTOM', 'label' => 'CUSTOM - Custom Terms'],
        ];

        $data = [
            'paymentTerm' => $paymentTerm,
            'statuses' => $statuses,
            'types' => $types,
            'canWrite' => true,
        ];

        return view('pages.master_data.payment_term.edit', array_merge($data));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:m_payment_terms,id',
                'code' => 'required|string|max:20|unique:m_payment_terms,code,'.$request->id,
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'days' => 'required|integer|min:0',
                'type' => 'required|in:NET,IMMEDIATE,COD,CUSTOM',
                'status' => 'required|in:ACTIVE,INACTIVE',
                // 'is_default' => 'boolean',
            ], [], [
                'id' => 'ID Syarat Pembayaran',
                'code' => 'Kode Syarat Pembayaran',
                'name' => 'Nama Syarat Pembayaran',
                'description' => 'Deskripsi Syarat Pembayaran',
                'days' => 'Jumlah Hari',
                'type' => 'Tipe Syarat Pembayaran',
                'status' => 'Status Syarat Pembayaran',
                // 'is_default' => 'Default Syarat Pembayaran',
            ]);

            DB::transaction(function () use ($validated) {
                // If setting as default, remove default from others
                if ($validated['is_default'] ?? false) {
                    DB::table($this->table_name)->where('id', '!=', $validated['id'])->update(['is_default' => false]);
                }

                DB::table($this->table_name)->where('id', $validated['id'])->update([
                    'code' => strtoupper($validated['code']),
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'days' => $validated['days'],
                    'type' => $validated['type'],
                    'status' => $validated['status'],
                    'is_default' => $validated['is_default'] ?? false,
                    'updated_at' => now(),
                ]);
            });

            return response()->json([
                'message' => 'Syarat Pembayaran berhasil diperbarui',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Kesalahan validasi: '.implode(', ', array_map(function ($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'error' => 'VALIDATION_ERROR',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => 'INTERNAL_SERVER_ERROR',
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:m_payment_terms,id',
            ]);

            $paymentTerm = DB::table($this->table_name)->where('id', $validated['id'])->first();

            if ($paymentTerm->is_default) {
                return response()->json([
                    'message' => 'Tidak dapat menghapus Syarat Pembayaran default.',
                    'error' => 'CANNOT_DELETE_DEFAULT',
                ], 422);
            }

            DB::table($this->table_name)->where('id', $validated['id'])->delete();

            return response()->json([
                'message' => 'Syarat Pembayaran berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error' => 'INTERNAL_SERVER_ERROR',
            ], 500);
        }
    }
}
