<?php

namespace App\Domains\Unit\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitConversionService
{
    private $table_name = 'm_unit_conversions';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_from_unit_id' => $data['from_unit_id'],
            'f_to_unit_id' => $data['to_unit_id'],
            'conversion_factor' => $data['conversion_factor'],
            'conversion_formula' => $data['conversion_formula'] ?? null,
            'status' => $data['status'] ?? GeneralStatuses::ACTIVE->value,
            'notes' => $data['notes'] ?? null,
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update([
                'f_from_unit_id' => $data['from_unit_id'],
                'f_to_unit_id' => $data['to_unit_id'],
                'conversion_factor' => $data['conversion_factor'],
                'conversion_formula' => $data['conversion_formula'] ?? null,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as uc')
            ->select([
                'uc.id',
                'uc.conversion_factor',
                'uc.conversion_formula',
                'uc.status',
                'uc.notes',
                'uc.created_at',
                'from_unit.code as from_unit_code',
                'from_unit.name as from_unit_name',
                'to_unit.code as to_unit_code',
                'to_unit.name as to_unit_name',
                DB::raw('CONCAT(from_unit.name, " (", from_unit.code, ")") as from_unit_display'),
                DB::raw('CONCAT(to_unit.name, " (", to_unit.code, ")") as to_unit_display'),
                DB::raw('CONCAT("1 ", from_unit.name, " = ", uc.conversion_factor, " ", to_unit.name) as conversion_display'),
            ])
            ->leftJoin('m_units as from_unit', 'uc.f_from_unit_id', '=', 'from_unit.id')
            ->leftJoin('m_units as to_unit', 'uc.f_to_unit_id', '=', 'to_unit.id')
            ->where('uc.company_code', session('user_company_code'))
            ->orderBy('uc.created_at', 'desc');

        return $query->get();
    }

    public function findAll()
    {
        return DB::table($this->table_name.' as uc')
            ->select([
                'uc.*',
                'from_unit.code as from_unit_code',
                'from_unit.name as from_unit_name',
                'to_unit.code as to_unit_code',
                'to_unit.name as to_unit_name',
            ])
            ->leftJoin('m_units as from_unit', 'uc.f_from_unit_id', '=', 'from_unit.id')
            ->leftJoin('m_units as to_unit', 'uc.f_to_unit_id', '=', 'to_unit.id')
            ->where('uc.company_code', session('user_company_code'))
            ->where('uc.status', GeneralStatuses::ACTIVE->value)
            ->orderBy('from_unit.name')
            ->get();
    }

    public function findOne(int $id)
    {
        return DB::table($this->table_name.' as uc')
            ->select([
                'uc.*',
                'from_unit.code as from_unit_code',
                'from_unit.name as from_unit_name',
                'to_unit.code as to_unit_code',
                'to_unit.name as to_unit_name',
            ])
            ->leftJoin('m_units as from_unit', 'uc.f_from_unit_id', '=', 'from_unit.id')
            ->leftJoin('m_units as to_unit', 'uc.f_to_unit_id', '=', 'to_unit.id')
            ->where('uc.id', $id)
            ->where('uc.company_code', session('user_company_code'))
            ->first();
    }

    public function delete(int $id): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->delete();
    }

    /**
     * Convert quantity from one unit to another
     *
     * @param int $fromUnitId
     * @param int $toUnitId
     * @param float $quantity
     * @return float|null Returns converted quantity or null if no conversion found
     */
    public function convert(int $fromUnitId, int $toUnitId, float $quantity): ?float
    {
        $conversion = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('f_from_unit_id', $fromUnitId)
            ->where('f_to_unit_id', $toUnitId)
            ->where('status', GeneralStatuses::ACTIVE->value)
            ->first();

        if ($conversion) {
            return $quantity * $conversion->conversion_factor;
        }

        // Try reverse conversion
        $reverseConversion = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('f_from_unit_id', $toUnitId)
            ->where('f_to_unit_id', $fromUnitId)
            ->where('status', GeneralStatuses::ACTIVE->value)
            ->first();

        if ($reverseConversion) {
            return $quantity / $reverseConversion->conversion_factor;
        }

        return null;
    }

    /**
     * Get all conversions for a specific unit
     */
    public function findByUnit(int $unitId)
    {
        return DB::table($this->table_name.' as uc')
            ->select([
                'uc.*',
                'from_unit.code as from_unit_code',
                'from_unit.name as from_unit_name',
                'to_unit.code as to_unit_code',
                'to_unit.name as to_unit_name',
            ])
            ->leftJoin('m_units as from_unit', 'uc.f_from_unit_id', '=', 'from_unit.id')
            ->leftJoin('m_units as to_unit', 'uc.f_to_unit_id', '=', 'to_unit.id')
            ->where('uc.company_code', session('user_company_code'))
            ->where('uc.status', GeneralStatuses::ACTIVE->value)
            ->where(function($query) use ($unitId) {
                $query->where('uc.f_from_unit_id', $unitId)
                      ->orWhere('uc.f_to_unit_id', $unitId);
            })
            ->get();
    }
}
