<?php

namespace App\Domains\PaymentTerm\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\DB;

class PaymentTermService
{
    private $table_name = 'm_payment_terms';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'name', 'code', 'days', 'type')
            ->where('status', GeneralStatuses::ACTIVE)
            ->where('company_code', session('user_company_code'))
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as c')
            ->select([
                'id',
                'code',
                'name',
                'description',
                'days',
                'type',
                'status',
                'is_default',
                'created_at',
            ])
            ->where('c.company_code', session('user_company_code'));

        // Filter by status
        if ($status = request()->input('status')) {
            $query->where('status', $status);
        }

        if ($type = request()->input('type')) {
            $query->where('type', $type);
        }

        if ($search = request()->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // DataTable pagination
        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('created_at', 'DESC');

        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }
}
