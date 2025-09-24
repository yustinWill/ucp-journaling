<?php

namespace App\Http\Controllers\Notification;

// Main
use App\Domains\Notification\Services\NotificationService;
use App\Enums\SuccessCodes;
// Services
use App\Http\Controllers\Controller;

// Enums

class HeaderListController extends Controller
{
    public function notification_list()
    {
        $data = app(NotificationService::class)->findAllList();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }
}
