<?php

namespace App\Domains\Production\Services;

use Illuminate\Support\Facades\DB;

class ProductionExpenseService
{
    private $table_name = 't_production_expenses';

    public function findByProductionId(int $productionId): array
    {
        return DB::table($this->table_name)
            ->where('production_id', $productionId)
            ->orderBy('expense_type')
            ->get()
            ->toArray();
    }

    public function createBulk(int $productionId, array $expenses): void
    {
        $insertData = [];

        foreach ($expenses as $expense) {
            $insertData[] = [
                'production_id' => $productionId,
                'expense_type' => $expense['expense_type'],
                'expense_name' => $expense['expense_name'],
                'default_amount' => $expense['default_amount'] ?? 0,
                'actual_amount' => $expense['actual_amount'],
                'unit' => $expense['unit'] ?? null,
                'description' => $expense['description'] ?? null,
                'notes' => $expense['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table($this->table_name)->insert($insertData);
    }

    public function updateBulk(int $productionId, array $expenses): void
    {
        // Delete existing expenses
        DB::table($this->table_name)->where('production_id', $productionId)->delete();

        // Insert updated expenses
        $this->createBulk($productionId, $expenses);
    }

    public function deleteByProductionId(int $productionId): int
    {
        return DB::table($this->table_name)
            ->where('production_id', $productionId)
            ->delete();
    }

    public function calculateTotalProductionExpense(int $productionId): float
    {
        return DB::table($this->table_name)
            ->where('production_id', $productionId)
            ->sum('actual_amount');
    }

    public function getExpensesByType(int $productionId): array
    {
        $expenses = DB::table($this->table_name)
            ->where('production_id', $productionId)
            ->get()
            ->groupBy('expense_type')
            ->toArray();

        $groupedExpenses = [];
        foreach ($expenses as $type => $expenseList) {
            $groupedExpenses[$type] = [
                'expense_type' => $type,
                'total_amount' => array_sum(array_column($expenseList, 'actual_amount')),
                'items' => $expenseList,
            ];
        }

        return $groupedExpenses;
    }

    public function getDefaultExpensesForProduct(int $bundleProductId): array
    {
        // Get default expenses from m_product_expenses
        return DB::table('m_product_expenses')
            ->where('f_product_id', $bundleProductId)
            ->get()
            ->map(function ($expense) {
                return [
                    'expense_type' => $expense->expense_type,
                    'expense_name' => $expense->expense_name,
                    'default_amount' => $expense->amount,
                    'actual_amount' => $expense->amount, // Default to same as default
                    'unit' => $expense->unit,
                    'description' => $expense->description,
                ];
            })
            ->toArray();
    }

    public function validateExpenseData(array $expenses): array
    {
        $validation = [];

        foreach ($expenses as $index => $expense) {
            $errors = [];

            if (empty($expense['expense_type'])) {
                $errors[] = 'Expense type is required';
            }

            if (empty($expense['expense_name'])) {
                $errors[] = 'Expense name is required';
            }

            if (! isset($expense['actual_amount']) || ! is_numeric($expense['actual_amount'])) {
                $errors[] = 'Actual amount must be a valid number';
            } elseif ($expense['actual_amount'] < 0) {
                $errors[] = 'Actual amount cannot be negative';
            }

            $validation[$index] = [
                'is_valid' => empty($errors),
                'errors' => $errors,
                'data' => $expense,
            ];
        }

        return $validation;
    }

    public function calculateExpenseDifference(int $productionId): array
    {
        $expenses = DB::table($this->table_name)
            ->where('production_id', $productionId)
            ->get();

        $totalDefault = 0;
        $totalActual = 0;
        $differences = [];

        foreach ($expenses as $expense) {
            $difference = $expense->actual_amount - $expense->default_amount;
            $totalDefault += $expense->default_amount;
            $totalActual += $expense->actual_amount;

            $differences[] = [
                'expense_name' => $expense->expense_name,
                'default_amount' => $expense->default_amount,
                'actual_amount' => $expense->actual_amount,
                'difference' => $difference,
                'percentage_change' => $expense->default_amount > 0
                    ? ($difference / $expense->default_amount) * 100
                    : 0,
            ];
        }

        return [
            'total_default' => $totalDefault,
            'total_actual' => $totalActual,
            'total_difference' => $totalActual - $totalDefault,
            'items' => $differences,
        ];
    }
}
