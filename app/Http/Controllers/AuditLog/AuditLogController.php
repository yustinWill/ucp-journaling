<?php

namespace App\Http\Controllers\AuditLog;

use App\Http\Controllers\Controller;

//Actions
use App\Enums\AuditLogTypes;

use App\Domains\User\Services\UserService;
use App\Domains\Unit\Services\UnitAuditLogService;
use App\Domains\Unit\Services\UnitService;

use App\Domains\Category\Services\CategoryAuditLogService;
use App\Domains\Category\Services\CategoryService;


class AuditLogController extends Controller
{
    public function detail($audit_log_type, $id)
    {
        $log_data = null;
        $header_data = null;
        $form_title = null;
        $form_type = null;

        switch ($audit_log_type) {
            case AuditLogTypes::MASTER_DATA_UNIT->code():
                $log_data = app(UnitAuditLogService::class)->findOneBasedOnId($id);
                $header_data = app(UnitService::class)->findOne($log_data->f_unit_id);
                $form_title = AuditLogTypes::MASTER_DATA_UNIT->label(). ' -> ' . $header_data->name;
                $form_type = AuditLogTypes::MASTER_DATA_UNIT->label();
                break;
            case AuditLogTypes::MASTER_DATA_CATEGORY->code():
                $log_data = app(CategoryAuditLogService::class)->findOneBasedOnId($id);
                $header_data = app(CategoryService::class)->findOne($log_data->f_category_id);
                $form_title = AuditLogTypes::MASTER_DATA_CATEGORY->label(). ' -> ' . $header_data->name;
                $form_type = AuditLogTypes::MASTER_DATA_CATEGORY->label();
                break;
        }

        $user = app(UserService::class)->findOne($log_data->created_by);
        $metadata = json_decode($log_data->metadata, true);
        return view('pages.audit_log.detail', compact('log_data', 'metadata', 'header_data', 'form_title', 'form_type', 'user'));
    }
}
