<?php

namespace App\Helpers;

use App\Services\TransactionNumberService;

class TransactionNumberHelper
{
    /**
     * Generate a transaction number for a specific module
     *
     * @param string $module The module name (e.g. 'expense', 'purchase', 'sales')
     * @return string The generated transaction number
     * @throws \Exception If the module configuration is not found
     */
    public static function generateNumber(string $module): string
    {
        $service = app(TransactionNumberService::class);
        return $service->generateNumber($module);
    }

    /**
     * Check if a transaction number already exists for a module
     *
     * @param string $module The module name
     * @param string $number The transaction number to check
     * @return bool True if the number exists, false otherwise
     */
    public static function checkDuplicate(string $module, string $number): bool
    {
        $service = app(TransactionNumberService::class);
        return $service->checkDuplicate($module, $number);
    }

    /**
     * Get next transaction number preview for a module
     *
     * @param string $module The module name
     * @return string The preview of the next transaction number
     * @throws \Exception If the module configuration is not found
     */
    public static function getNextNumberPreview(string $module): string
    {
        $service = app(TransactionNumberService::class);
        return $service->getNextNumberPreview($module);
    }

    /**
     * Validate a transaction number format
     *
     * @param string $number The transaction number to validate
     * @param string $module The module name (optional, for additional validation)
     * @return bool True if valid, false otherwise
     */
    public static function validateFormat(string $number, string $module = null): bool
    {
        // Basic validation - not empty and minimum length
        if (empty($number) || strlen($number) < 3) {
            return false;
        }

        // If module is provided, check against the module's configuration
        if ($module) {
            $service = app(TransactionNumberService::class);
            try {
                $config = $service->getModuleConfig($module);
                if (!$config) {
                    return false;
                }
                
                // Additional module-specific validation can be added here
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all available modules with their configuration
     *
     * @return array Array of module configurations
     */
    public static function getAvailableModules(): array
    {
        $service = app(TransactionNumberService::class);
        return $service->getAllModules();
    }

    /**
     * Get configuration for a specific module
     *
     * @param string $module The module name
     * @return object|null The module configuration or null if not found
     */
    public static function getModuleConfig(string $module): ?object
    {
        $service = app(TransactionNumberService::class);
        return $service->getModuleConfig($module);
    }
}