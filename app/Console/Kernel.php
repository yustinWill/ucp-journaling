<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Daily database backup at 1 AM
        $schedule->command('backup:run --only-db')
                 ->dailyAt('01:00')
                 ->appendOutputTo(storage_path('logs/backup.log'))
                 ->onFailure(function () {
                     $this->sendBackupErrorToDiscord();
                 });

        // Optional: Run backup every 6 hours (for more frequent backups)
        // $schedule->command('backup:run --only-db')->everyHours(6);

        // Optional: Clean up old backups daily at 2 AM
        $schedule->command('backup:clean')
                 ->dailyAt('02:00')
                 ->appendOutputTo(storage_path('logs/backup.log'))
                 ->onFailure(function () {
                     $this->sendCleanupErrorToDiscord();
                 });
    }

    /**
     * Send backup error details to Discord.
     */
    private function sendBackupErrorToDiscord(): void
    {
        $webhookUrl = env('APP_ERROR_DISCORD_WEBHOOK_URL') ?? env('DISCORD_WEBHOOK_URL') ?? env('BACKUP_DISCORD_WEBHOOK');
        
        if (!$webhookUrl) {
            return;
        }

        // Read last error from log file
        $logFile = storage_path('logs/backup.log');
        $errorMessage = 'Unknown error';
        
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            // Get last 500 characters of log
            $errorMessage = substr($logs, -500);
        }

        try {
            Http::post($webhookUrl, [
                'username' => 'Database Backup Alert',
                'avatar_url' => 'https://www.laravel.com/img/logomark.min.svg',
                'embeds' => [
                    [
                        'title' => '❌ Database Backup FAILED',
                        'description' => 'Backup gagal - lihat error details di bawah',
                        'color' => 16711680, // Red color
                        'fields' => [
                            [
                                'name' => 'Database',
                                'value' => env('DB_DATABASE', 'unknown'),
                                'inline' => true,
                            ],
                            [
                                'name' => 'Waktu Gagal',
                                'value' => now()->format('Y-m-d H:i:s'),
                                'inline' => true,
                            ],
                            [
                                'name' => 'Error Details',
                                'value' => '```' . $errorMessage . '```',
                                'inline' => false,
                            ],
                        ],
                        'timestamp' => now()->toIso8601String(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to send backup error to Discord: ' . $e->getMessage());
        }
    }

    /**
     * Send cleanup error details to Discord.
     */
    private function sendCleanupErrorToDiscord(): void
    {
        $webhookUrl = env('APP_ERROR_DISCORD_WEBHOOK_URL') ?? env('DISCORD_WEBHOOK_URL') ?? env('BACKUP_DISCORD_WEBHOOK');
        
        if (!$webhookUrl) {
            return;
        }

        // Read last error from log file
        $logFile = storage_path('logs/backup.log');
        $errorMessage = 'Unknown error';
        
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            // Get last 500 characters of log
            $errorMessage = substr($logs, -500);
        }

        try {
            Http::post($webhookUrl, [
                'username' => 'Database Backup Alert',
                'avatar_url' => 'https://www.laravel.com/img/logomark.min.svg',
                'embeds' => [
                    [
                        'title' => '❌ Backup Cleanup FAILED',
                        'description' => 'Pembersihan backup lama gagal',
                        'color' => 16711680, // Red color
                        'fields' => [
                            [
                                'name' => 'Waktu Gagal',
                                'value' => now()->format('Y-m-d H:i:s'),
                                'inline' => true,
                            ],
                            [
                                'name' => 'Error Details',
                                'value' => '```' . $errorMessage . '```',
                                'inline' => false,
                            ],
                        ],
                        'timestamp' => now()->toIso8601String(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to send cleanup error to Discord: ' . $e->getMessage());
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
