<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;

class TransactionNumberService
{
    private $table_name = 'm_transaction_numbers';

    /**
     * Generate the next transaction number for a module
     */
    public function generateNumber(string $module): string
    {
        DB::beginTransaction();

        try {
            // Lock the row for the duration of this transaction to prevent race conditions
            $config = DB::table($this->table_name)
                ->where('module_name', $module)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $config) {
                throw new Exception("Transaction number configuration not found for module: {$module}");
            }

            // Check if counter needs to be reset based on frequency
            $shouldReset = $this->shouldResetCounter($config);

            if ($shouldReset) {
                $currentCounter = 1;
                DB::table($this->table_name)
                    ->where('id', $config->id)
                    ->update([
                        'current_counter' => $currentCounter,
                        'last_reset_date' => now()->toDateString(),
                        'updated_at' => now(),
                    ]);
            } else {
                $currentCounter = $config->current_counter + 1;
                DB::table($this->table_name)
                    ->where('id', $config->id)
                    ->update([
                        'current_counter' => $currentCounter,
                        'updated_at' => now(),
                    ]);
            }

            // Generate number using template
            $generatedNumber = $this->interpolateTemplate(
                $config->format_template,
                $config->module_initial,
                $config->module_name,
                $currentCounter,
                $config->counter_padding
            );

            DB::commit();

            return $generatedNumber;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Preview a number format without incrementing counter
     */
    public function previewFormat(string $template, string $moduleInitial, string $moduleName, int $counter, int $padding): string
    {
        return $this->interpolateTemplate($template, $moduleInitial, $moduleName, $counter, $padding);
    }

    /**
     * Check if transaction number already exists
     */
    public function checkDuplicate(string $module, string $number): bool
    {
        // This would need to be implemented based on your specific transaction tables
        // For now, return false (no duplicates found)
        // You would check against expense, purchase_order, sales, etc. tables
        return false;
    }

    /**
     * Get next number preview for a module
     */
    public function getNextNumberPreview(string $module): string
    {
        $config = $this->getModuleConfig($module);

        if (! $config) {
            throw new Exception("Transaction number configuration not found for module: {$module}");
        }

        // Same rule as generateNumber: the first number after a reset is 1.
        $nextCounter = $this->shouldResetCounter($config) ? 1 : $config->current_counter + 1;

        return $this->interpolateTemplate(
            $config->format_template,
            $config->module_initial,
            $config->module_name,
            $nextCounter,
            $config->counter_padding
        );
    }

    /**
     * Get all modules configuration
     */
    public function getAllModules(): array
    {
        return DB::table($this->table_name)
            ->orderBy('module_display_name')
            ->get()
            ->toArray();
    }

    /**
     * Get module configuration
     */
    public function getModuleConfig(string $module)
    {
        return DB::table($this->table_name)
            ->where('module_name', $module)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Update module configuration
     */
    public function updateConfig(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update($data + ['updated_at' => now()]);
    }

    /**
     * Reset counter manually
     */
    public function resetCounter(int $id, int $newCounter): bool
    {
        $result = DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'current_counter' => $newCounter,
                'last_reset_date' => now()->toDateString(),
                'updated_at' => now(),
            ]);

        return $result > 0;
    }

    /**
     * Get all available interpolation placeholders
     */
    public function getAvailablePlaceholders(): array
    {
        return [
            '{YEAR}' => [
                'description' => 'Current year (2025)',
                'example' => date('Y'),
            ],
            '{YY}' => [
                'description' => 'Year 2-digit (25)',
                'example' => date('y'),
            ],
            '{MM}' => [
                'description' => 'Month (07)',
                'example' => date('m'),
            ],
            '{DD}' => [
                'description' => 'Date (25)',
                'example' => date('d'),
            ],
            '{MODULE_INITIAL}' => [
                'description' => 'Module code (EX, PO)',
                'example' => 'EX',
            ],
            '{MODULE_NAME}' => [
                'description' => 'Full name (EXPENSE)',
                'example' => 'EXPENSE',
            ],
            '{COUNTER}' => [
                'description' => 'Counter (required)',
                'example' => '00001',
            ],
        ];
    }

    /**
     * Validate template format
     */
    public function validateTemplate(string $template): array
    {
        $errors = [];

        // Check if {COUNTER} is present (required)
        if (strpos($template, '{COUNTER}') === false) {
            $errors[] = 'Template must contain {COUNTER} placeholder';
        }

        // Check for unknown placeholders
        $validPlaceholders = array_keys($this->getAvailablePlaceholders());
        preg_match_all('/\{([^}]+)\}/', $template, $matches);

        foreach ($matches[0] as $placeholder) {
            if (! in_array($placeholder, $validPlaceholders)) {
                $errors[] = "Unknown placeholder: {$placeholder}";
            }
        }

        return $errors;
    }

    /**
     * Interpolate template with actual values
     */
    public function interpolateTemplate(string $template, string $moduleInitial, string $moduleName, int $counter, int $padding, ?int $timestamp = null): string
    {
        $timestamp ??= time();
        $replacements = [
            '{YEAR}' => date('Y', $timestamp),
            '{YY}' => date('y', $timestamp),
            '{MM}' => date('m', $timestamp),
            '{DD}' => date('d', $timestamp),
            '{MODULE_INITIAL}' => strtoupper($moduleInitial),
            '{MODULE_NAME}' => strtoupper($moduleName),
            '{COUNTER}' => str_pad($counter, $padding, '0', STR_PAD_LEFT),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Check if counter should be reset based on frequency
     */
    private function shouldResetCounter($config): bool
    {
        if ($config->reset_frequency === 'never') {
            return false;
        }

        $lastReset = $config->last_reset_date ? new \DateTime($config->last_reset_date) : null;
        $now = new \DateTime;

        if (! $lastReset) {
            return true; // First time, should reset
        }

        switch ($config->reset_frequency) {
            case 'daily':
                return $lastReset->format('Y-m-d') !== $now->format('Y-m-d');
            case 'monthly':
                return $lastReset->format('Y-m') !== $now->format('Y-m');
            case 'yearly':
                return $lastReset->format('Y') !== $now->format('Y');
            default:
                return false;
        }
    }
}
