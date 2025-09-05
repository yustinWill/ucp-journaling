<?php

namespace App\Http\Controllers\AuditLog;

use App\Domains\Category\Services\CategoryAuditLogService;
// Actions
use App\Domains\Category\Services\CategoryService;
use App\Domains\Contact\Services\ContactAuditLogService;
use App\Domains\Contact\Services\ContactService;
use App\Domains\Role\Services\RoleAuditLogService;
use App\Domains\Role\Services\RoleService;
use App\Domains\SalesPerson\Services\SalesPersonAuditLogService;
use App\Domains\SalesPerson\Services\SalesPersonService;
use App\Domains\Tag\Services\TagAuditLogService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Unit\Services\UnitAuditLogService;
use App\Domains\Unit\Services\UnitService;
use App\Domains\User\Services\UserAuditLogService;
use App\Domains\User\Services\UserService;
use App\Enums\AuditLogTypes;
use App\Http\Controllers\Controller;

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
                $form_title = AuditLogTypes::MASTER_DATA_UNIT->label().' -> '.$header_data->name;
                $form_type = AuditLogTypes::MASTER_DATA_UNIT->label();
                break;
            case AuditLogTypes::MASTER_DATA_CATEGORY->code():
                $log_data = app(CategoryAuditLogService::class)->findOneBasedOnId($id);
                $header_data = app(CategoryService::class)->findOne($log_data->f_category_id);
                $form_title = AuditLogTypes::MASTER_DATA_CATEGORY->label().' -> '.$header_data->name;
                $form_type = AuditLogTypes::MASTER_DATA_CATEGORY->label();
                break;
            case AuditLogTypes::MASTER_DATA_TAG->code():
                $log_data = app(TagAuditLogService::class)->findOneBasedOnId($id);
                $header_data = app(TagService::class)->findOne($log_data->f_tag_id);
                $form_title = AuditLogTypes::MASTER_DATA_TAG->label().' -> '.$header_data->name;
                $form_type = AuditLogTypes::MASTER_DATA_TAG->label();
                break;
            case AuditLogTypes::MASTER_DATA_SALES_PERSON->code():
                $log_data = app(SalesPersonAuditLogService::class)->findOneBasedOnId($id);
                $header_data = app(SalesPersonService::class)->findOne($log_data->f_sales_person_id);
                $form_title = AuditLogTypes::MASTER_DATA_SALES_PERSON->label().' -> '.$header_data->code;
                $form_type = AuditLogTypes::MASTER_DATA_SALES_PERSON->label();
                break;
            case AuditLogTypes::MASTER_DATA_USER->code():
                $log_data = app(UserAuditLogService::class)->findOneBasedOnId($id);
                $header_data = app(UserService::class)->findOne($log_data->f_user_id);
                $form_title = AuditLogTypes::MASTER_DATA_USER->label().' -> '.$header_data->name;
                $form_type = AuditLogTypes::MASTER_DATA_USER->label();
                break;
            case AuditLogTypes::MASTER_DATA_ROLE->code():
                $log_data = app(RoleAuditLogService::class)->findOneBasedOnId($id);
                $header_data = app(RoleService::class)->findOne($log_data->f_role_id);
                $form_title = AuditLogTypes::MASTER_DATA_ROLE->label().' -> '.$header_data->name;
                $form_type = AuditLogTypes::MASTER_DATA_ROLE->label();
                break;
            case AuditLogTypes::MASTER_DATA_CONTACT->code():
                $log_data = app(ContactAuditLogService::class)->findOneBasedOnId($id);
                $header_data = app(ContactService::class)->findOne($log_data->f_contact_id);
                $form_title = AuditLogTypes::MASTER_DATA_CONTACT->label().' -> '.$header_data->name;
                $form_type = AuditLogTypes::MASTER_DATA_CONTACT->label();
                break;
        }

        $user = app(UserService::class)->findOne($log_data->created_by);
        $metadata = json_decode($log_data->metadata, true);

        return view('pages.audit_log.detail', compact('log_data', 'metadata', 'header_data', 'form_title', 'form_type', 'user'));
    }
}
