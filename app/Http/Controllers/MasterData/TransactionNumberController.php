<?php

namespace App\Http\Controllers\MasterData;

use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Services\TransactionNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionNumberController extends Controller
{
    private $transactionNumberService;

    private $table_name = 'm_transaction_numbers';

    public function __construct(TransactionNumberService $transactionNumberService)
    {
        $this->transactionNumberService = $transactionNumberService;
    }

    public function index()
    {
        $permissionData = PermissionHelper::getViewPermissions('master_data_transaction_number');

        return view('pages.master_data.transaction_number.index', $permissionData);
    }

    public function indexDataTable()
    {
        $data = DB::table($this->table_name)
            ->select([
                'id',
                'module_name',
                'module_display_name',
                'module_initial',
                'format_template',
                'current_counter',
                'counter_padding',
                'reset_frequency',
                'status',
                'updated_at',
            ])
            ->orderBy('module_display_name')
            ->get();

        $result = [];
        foreach ($data as $item) {
            // Generate preview of next number
            $nextCounter = $item->current_counter + 1;
            $preview = $this->transactionNumberService->previewFormat(
                $item->format_template,
                $item->module_initial,
                $item->module_name,
                $nextCounter,
                $item->counter_padding
            );

            $result[] = [
                'id' => $item->id,
                'module_display_name' => $item->module_display_name,
                'format_template' => $item->format_template,
                'current_counter' => $item->current_counter,
                'next_preview' => $preview,
                'reset_frequency' => ucfirst($item->reset_frequency),
                'status' => $item->status,
                'updated_at' => $item->updated_at,
            ];
        }

        return response()->json(['data' => $result]);
    }

    public function detail($id)
    {
        $transactionNumber = DB::table($this->table_name)->where('id', $id)->first();

        if (! $transactionNumber) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Generate preview examples
        $previews = $this->generatePreviewExamples($transactionNumber);
        $placeholders = $this->transactionNumberService->getAvailablePlaceholders();
        $permissionData = PermissionHelper::getViewPermissions('master_data_transaction_number');

        // Map permission variables for blade template compatibility
        $canShowEditButton = $permissionData['canEditTransactionNumber'] ?? false;
        $canShowDeleteButton = $permissionData['canDeleteTransactionNumber'] ?? false;

        return view('pages.master_data.transaction_number.detail', array_merge([
            'transactionNumber' => $transactionNumber,
            'previews' => $previews,
            'placeholders' => $placeholders,
            'canEdit' => $canShowEditButton,
            'canShowEditButton' => $canShowEditButton,
            'canShowDeleteButton' => $canShowDeleteButton,
        ], $permissionData));
    }

    public function edit($id)
    {
        $transactionNumber = DB::table($this->table_name)->where('id', $id)->first();

        if (! $transactionNumber) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $placeholders = $this->transactionNumberService->getAvailablePlaceholders();
        $resetFrequencies = [
            ['value' => 'never', 'label' => 'Never'],
            ['value' => 'daily', 'label' => 'Daily'],
            ['value' => 'monthly', 'label' => 'Monthly'],
            ['value' => 'yearly', 'label' => 'Yearly'],
        ];
        $permissionData = PermissionHelper::getViewPermissions('master_data_transaction_number');

        // Map permission for blade compatibility
        $canWrite = $permissionData['canEditTransactionNumber'] ?? false;

        return view('pages.master_data.transaction_number.edit', array_merge([
            'transactionNumber' => $transactionNumber,
            'placeholders' => $placeholders,
            'resetFrequencies' => $resetFrequencies,
            'canWrite' => $canWrite,
        ], $permissionData));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:m_transaction_numbers,id',
            'format_template' => 'required|string|max:500',
            'current_counter' => 'required|integer',
            'counter_padding' => 'required|integer|max:10',
            'reset_frequency' => 'required|in:never,daily,monthly,yearly',
        ]);

        // Validate template
        $validationErrors = $this->transactionNumberService->validateTemplate($request->format_template);
        if (! empty($validationErrors)) {
            return response()->json([
                'message' => 'Template format tidak valid.',
                'errors' => $validationErrors,
            ], 422);
        }

        try {
            DB::beginTransaction();

            $result = $this->transactionNumberService->updateConfig($request->id, [
                'format_template' => $request->format_template,
                'current_counter' => $request->current_counter,
                'counter_padding' => $request->counter_padding,
                'reset_frequency' => $request->reset_frequency,
            ]);

            if ($result) {
                DB::commit();

                return response()->json(['message' => 'Konfigurasi berhasil diperbarui.']);
            } else {
                DB::rollBack();

                return response()->json(['message' => 'Gagal memperbarui konfigurasi.'], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function previewFormat(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'module_initial' => 'required|string|max:2',
            'module_name' => 'required|string',
            'counter' => 'required|integer',
            'padding' => 'required|integer|max:10',
        ]);

        // Validate template
        $validationErrors = $this->transactionNumberService->validateTemplate($request->template);
        if (! empty($validationErrors)) {
            return response()->json([
                'success' => false,
                'errors' => $validationErrors,
            ]);
        }

        try {
            $preview = $this->transactionNumberService->previewFormat(
                $request->template,
                $request->module_initial,
                $request->module_name,
                $request->counter,
                $request->padding
            );

            return response()->json([
                'success' => true,
                'preview' => $preview,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Terjadi kesalahan: '.$e->getMessage()],
            ]);
        }
    }

    public function generateNumber(Request $request, $module)
    {
        try {
            // A form shows the next number with preview=1; the number is taken when the document is saved.
            $number = $request->boolean('preview')
                ? $this->transactionNumberService->getNextNumberPreview($module)
                : $this->transactionNumberService->generateNumber($module);

            return response()->json([
                'success' => true,
                'number' => $number,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkDuplicate(Request $request, $module)
    {
        $request->validate([
            'number' => 'required|string',
        ]);

        $exists = $this->transactionNumberService->checkDuplicate($module, $request->number);

        return response()->json([
            'exists' => $exists,
        ]);
    }

    public function resetCounter(Request $request, $id)
    {
        $request->validate([
            'counter' => 'required|integer',
        ]);

        try {
            $result = $this->transactionNumberService->resetCounter($id, $request->counter);

            if ($result) {
                return response()->json(['message' => 'Counter berhasil direset.']);
            } else {
                return response()->json(['message' => 'Gagal mereset counter.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    /**
     * Generate preview examples for detail page
     */
    private function generatePreviewExamples($config)
    {
        $examples = [];
        $counters = [$config->current_counter + 1, $config->current_counter + 2, $config->current_counter + 3];

        foreach ($counters as $counter) {
            $examples[] = $this->transactionNumberService->previewFormat(
                $config->format_template,
                $config->module_initial,
                $config->module_name,
                $counter,
                $config->counter_padding
            );
        }

        return $examples;
    }
}
