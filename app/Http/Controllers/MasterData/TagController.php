<?php

namespace App\Http\Controllers\MasterData;

//Main
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;
use App\Helpers\ValidationHelper;
use App\Helpers\DiscordNotifier;

//Actions
use App\Domains\Tag\Actions\CreateTagAction;
use App\Domains\Tag\Actions\UpdateTagAction;
//Services
use App\Domains\Tag\Services\TagColorService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Tag\Services\TagAuditLogService;
use App\Domains\Tag\Services\TagUsageLogService;
//Enums
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;


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
        $data = app(TagService::class)->findAllDatatable();
        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
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
                'memo' => 'nullable|string|max:255',
            ]);

            app(CreateTagAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_TAG_SUCCESS->message()
            ], SuccessCodes::CREATE_TAG_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);
            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function edit($id)
    {
        $tag = app(TagService::class)->findOne($id);

        if ($tag == null) {
            abort(404);
        }

        $availableColors = app(TagColorService::class)->findAll();

        return view('pages.master_data.tag.edit', compact('tag', 'availableColors'));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_tags,id',
                'name' => 'required|string|max:255',
                'status' => [
                    'required',
                    Rule::in(array_column($this->availableStatus, 'value'))
                ],
                'f_tag_color_id' => 'required|exists:m_tag_colors,id',
                'description' => 'nullable|string|max:255',
                'memo' => 'nullable|string|max:255',
            ]);

            app(UpdateTagAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_TAG_SUCCESS->message()
            ], SuccessCodes::CREATE_TAG_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);
            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function detail($id)
    {
        $tag = app(TagService::class)->findOne($id);
        if ($tag == null) {
            abort(404);
        }

        $audit_log_data = app(TagAuditLogService::class)->findAllBasedOnTagId($id);
        // dd($audit_log_data);
        $usage_log_data = app(TagUsageLogService::class)->findAllBasedOnTagId($id);
        // Sample tag data for detail view
        // $tag = [
        //     'id' => $id,
        //     'code' => 'PENJUALAN',
        //     'name' => 'Transaksi Penjualan',
        //     'color' => 'success',
        //     'description' => 'Tag untuk semua transaksi penjualan produk RIYO Water kepada distributor dan retailer. Termasuk penjualan tunai maupun kredit.',
        //     'status' => 'ACTIVE',
        //     'usage_count' => 145,
        //     'created_by' => 'Admin System',
        //     'created_at' => '2025-01-15 08:00:00',
        //     'updated_by' => 'Finance Manager',
        //     'updated_at' => '2025-01-18 14:30:00',
        //     'recent_transactions' => $usage_log_data,
        //     'audit_logs' => $audit_log_data
        // ];

          $tag->recent_transactions = $usage_log_data;
           $tag->audit_logs = $audit_log_data;

        // dd($tag);

        return view('pages.master_data.tag.detail', compact('tag'));
    }
}
