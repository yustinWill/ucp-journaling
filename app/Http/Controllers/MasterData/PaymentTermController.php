<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentTermController extends Controller
{
    private $table_name = 'm_payment_terms';

    public function index()
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('master_data_payment_term', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('master_data_payment_term');

        // Summary data
        $totalPaymentTerms = DB::table($this->table_name)->count();
        $activePaymentTerms = DB::table($this->table_name)->where('status', 'active')->count();
        $defaultPaymentTerm = DB::table($this->table_name)->where('is_default', true)->first();

        $statuses = [
            ['value' => 'active', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'inactive', 'label' => 'INACTIVE - Tidak Aktif']
        ];

        $types = [
            ['value' => 'net', 'label' => 'NET - Net Payment'],
            ['value' => 'immediate', 'label' => 'IMMEDIATE - Immediate Payment'],
            ['value' => 'cod', 'label' => 'COD - Cash on Delivery'],
            ['value' => 'custom', 'label' => 'CUSTOM - Custom Terms']
        ];

        // Check permissions
        $canAddPaymentTerm = PermissionHelper::canWrite('master_data_payment_term');
        $canImportPaymentTerm = PermissionHelper::canWrite('master_data_payment_term');
        $canExportPaymentTerm = PermissionHelper::canRead('master_data_payment_term');

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

        return view('pages.master_data.payment_term.index', array_merge($data, $permissionData));
    }

    public function indexDataTable(Request $request)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('master_data_payment_term', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = DB::table($this->table_name)->select([
            'id', 'code', 'name', 'description', 'days', 'type', 'status', 'is_default', 'created_at'
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $paymentTerms = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $paymentTerms], 200);
    }

    public function add()
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('master_data_payment_term', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        $statuses = [
            ['value' => 'active', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'inactive', 'label' => 'INACTIVE - Tidak Aktif']
        ];

        $types = [
            ['value' => 'net', 'label' => 'NET - Net Payment'],
            ['value' => 'immediate', 'label' => 'IMMEDIATE - Immediate Payment'],
            ['value' => 'cod', 'label' => 'COD - Cash on Delivery'],
            ['value' => 'custom', 'label' => 'CUSTOM - Custom Terms']
        ];

        return view('pages.master_data.payment_term.add', [
            'statuses' => $statuses,
            'types' => $types
        ]);
    }

    public function store(Request $request)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('master_data_payment_term', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menambah data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'code' => 'required|string|max:20|unique:m_payment_terms,code',
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'days' => 'required|integer|min:0',
                'type' => 'required|in:net,immediate,cod,custom',
                'status' => 'required|in:active,inactive',
                'is_default' => 'boolean'
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
                'message' => 'Payment Term berhasil disimpan'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Kesalahan validasi: ' . implode(', ', array_map(function($errors) {
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
        // Check read permission
        if (!PermissionHelper::hasPermission('master_data_payment_term', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail.');
        }

        $paymentTerm = DB::table($this->table_name)->where('id', $id)->first();
        
        if (!$paymentTerm) {
            abort(404, 'Payment Term tidak ditemukan.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('master_data_payment_term');

        $data = [
            'paymentTerm' => $paymentTerm,
            'canEditPaymentTerm' => PermissionHelper::canWrite('master_data_payment_term'),
            'canDeletePaymentTerm' => PermissionHelper::canDelete('master_data_payment_term'),
        ];

        return view('pages.master_data.payment_term.detail', array_merge($data, $permissionData));
    }

    public function edit($id)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('master_data_payment_term', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        $paymentTerm = DB::table($this->table_name)->where('id', $id)->first();
        
        if (!$paymentTerm) {
            abort(404, 'Payment Term tidak ditemukan.');
        }

        $statuses = [
            ['value' => 'active', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'inactive', 'label' => 'INACTIVE - Tidak Aktif']
        ];

        $types = [
            ['value' => 'net', 'label' => 'NET - Net Payment'],
            ['value' => 'immediate', 'label' => 'IMMEDIATE - Immediate Payment'],
            ['value' => 'cod', 'label' => 'COD - Cash on Delivery'],
            ['value' => 'custom', 'label' => 'CUSTOM - Custom Terms']
        ];

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('master_data_payment_term');

        $data = [
            'paymentTerm' => $paymentTerm,
            'statuses' => $statuses,
            'types' => $types,
            'canWrite' => PermissionHelper::canWrite('master_data_payment_term'),
        ];

        return view('pages.master_data.payment_term.edit', array_merge($data, $permissionData));
    }

    public function update(Request $request)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('master_data_payment_term', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengupdate data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:m_payment_terms,id',
                'code' => 'required|string|max:20|unique:m_payment_terms,code,' . $request->id,
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'days' => 'required|integer|min:0',
                'type' => 'required|in:net,immediate,cod,custom',
                'status' => 'required|in:active,inactive',
                'is_default' => 'boolean'
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
                'message' => 'Payment Term berhasil diperbarui'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Kesalahan validasi: ' . implode(', ', array_map(function($errors) {
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
        // Check delete permission
        if (!PermissionHelper::hasPermission('master_data_payment_term', 'delete')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:m_payment_terms,id'
            ]);

            $paymentTerm = DB::table($this->table_name)->where('id', $validated['id'])->first();
            
            if ($paymentTerm->is_default) {
                return response()->json([
                    'message' => 'Tidak dapat menghapus payment term default.',
                    'error' => 'CANNOT_DELETE_DEFAULT',
                ], 422);
            }

            DB::table($this->table_name)->where('id', $validated['id'])->delete();

            return response()->json([
                'message' => 'Payment Term berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error' => 'INTERNAL_SERVER_ERROR',
            ], 500);
        }
    }
}