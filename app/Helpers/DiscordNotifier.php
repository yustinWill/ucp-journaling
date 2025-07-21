<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DiscordNotifier
{
    public static function send($message)
    {
        Http::post(env('DISCORD_WEBHOOK_URL'), [
            'content' => $message
        ]);
    }

    public static function reportException(\Throwable $e)
    {
        $ip = request()->ip();
        $url = request()->fullUrl();
        $user = Auth::user()->name ." - ".Auth::user()->email;
        $env = app()->environment();
        $time = now()->toDateTimeString();

        $requestData = request()->except(['password', 'token']);
        $requestJson = json_encode($requestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $msg =  "**🚨 Exception Alert**\n\n" .
            "**🧠 Message:** `" . $e->getMessage() . "`\n" .
            "**📄 File:** `" . $e->getFile() . "`\n" .
            "**🔢 Line:** `" . $e->getLine() . "`\n" .
            "**🌐 URL:** `$url`\n" .
            "**👤 User:** `$user`\n" .
            "**🌍 IP:** `$ip`\n" .
            "**🕐 Time:** `$time`\n" .
            "**📦 Environment:** `$env`\n\n" .
            "**📥 Request Data:**\n```json\n$requestJson\n```";

        self::send($msg);
    }
}
