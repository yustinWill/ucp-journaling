<?php

namespace App\Http\Controllers\MasterData;

// Main
use App\Domains\Tag\Actions\CreateTagAction;
use App\Domains\Tag\Actions\UpdateTagAction;
use App\Domains\Tag\Services\TagAuditLogService;
use App\Domains\Tag\Services\TagColorService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Tag\Services\TagUsageLogService;
use App\Enums\AuditLogTypes;
// Actions
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
// Services
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
// Enums
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TagController extends Controller
{
    private $availableStatus = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif'],
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
                    Rule::in(array_column($this->availableStatus, 'value')),
                ],
                'f_tag_color_id' => 'required|exists:m_tag_colors,id',
                'description' => 'nullable|string|max:255',
                'memo' => 'nullable|string|max:255',
            ], [], [
                'name' => 'Nama Tag',
                'code' => 'Kode Tag',
                'status' => 'Status Tag',
                'f_tag_color_id' => 'Warna Tag',
                'description' => 'Deskripsi Tag',
                'memo' => 'Memo Tag',
            ]);

            app(CreateTagAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_TAG_SUCCESS->message(),
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
                'trace' => $e->getMessage(),
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
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'status' => [
                    'required',
                    Rule::in(array_column($this->availableStatus, 'value')),
                ],
                'f_tag_color_id' => 'required|exists:m_tag_colors,id',
                'description' => 'nullable|string|max:255',
                'memo' => 'nullable|string|max:255',
            ], [], [
                'id' => 'ID Tag',
                'name' => 'Nama Tag',
                'code' => 'Kode Tag',
                'status' => 'Status Tag',
                'f_tag_color_id' => 'Warna Tag',
                'description' => 'Deskripsi Tag',
                'memo' => 'Memo Tag',
            ]);

            app(UpdateTagAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_TAG_SUCCESS->message(),
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
                'trace' => $e->getMessage(),
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
        $usage_log_data = app(TagUsageLogService::class)->findAllBasedOnTagId($id);

        $tag->recent_transactions = $usage_log_data;
        $tag->audit_logs = $audit_log_data;

        return view('pages.master_data.tag.detail', array_merge(
            compact('tag'),
            ['auditLogType' => AuditLogTypes::MASTER_DATA_TAG->code()]
        ));
    }
}
