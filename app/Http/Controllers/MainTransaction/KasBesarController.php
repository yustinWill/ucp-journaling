<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Services\KasBesarService;
use App\Services\TransactionNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasBesarController extends Controller
{
    public function index()
    {
        return view('pages.main_transaction.kas_besar.index');
    }

    public function index_data_table(Request $request)
    {
        $companyCode = session('user_company_code');

        $query = DB::table('t_kas_besar as kb')
            ->leftJoin('users as cu', 'kb.created_by', '=', 'cu.id')
            ->select([
                'kb.id',
                'kb.transaction_number',
                'kb.transaction_date',
                'kb.type',
                'kb.topup_amount',
                'kb.saldo_before',
                'kb.saldo_after',
                'kb.notes',
                'cu.name as created_by_name',
            ])
            ->where('kb.company_code', $companyCode);

        if ($request->filled('start_date')) {
            $query->where('kb.transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('kb.transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('search_transaction_no')) {
            $query->where('kb.transaction_number', 'LIKE', '%' . $request->search_transaction_no . '%');
        }

        $recordsTotal = DB::table('t_kas_besar')->where('company_code', $companyCode)->count();

        $filteredQuery = clone $query;
        $recordsFiltered = $filteredQuery->count();

        $query->orderBy('kb.transaction_date', 'DESC')->orderBy('kb.id', 'DESC');

        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $data = $query->get()->map(function ($item) {
            return [
                'id'                 => $item->id,
                'transaction_number' => $item->transaction_number,
                'transaction_date'   => $item->transaction_date,
                'type'               => $item->type,
                'topup_amount'       => $item->topup_amount,
                'saldo_before'       => $item->saldo_before,
                'saldo_after'        => $item->saldo_after,
                'notes'              => $item->notes,
                'created_by_name'    => $item->created_by_name,
            ];
        });

        return response()->json([
            'draw'            => (int) $request->input('draw', 1),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function add()
    {
        return view('pages.main_transaction.kas_besar.add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date|before_or_equal:today',
            'topup_amount'     => 'required|numeric|min:0.01',
            'notes'            => 'nullable|string|max:500',
        ], [
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh lebih dari hari ini.',
            'topup_amount.required'     => 'Jumlah topup wajib diisi.',
            'topup_amount.min'          => 'Jumlah topup harus lebih dari 0.',
        ]);

        try {
            $row = app(KasBesarService::class)->record(
                'TOPUP',
                (float) $validated['topup_amount'],
                $validated['transaction_date'],
                $validated['notes'] ?? null
            );

            return response()->json([
                'success'            => true,
                'message'            => 'Kas Besar berhasil ditambahkan dengan nomor: ' . $row['transaction_number'],
                'id'                 => $row['id'],
                'transaction_number' => $row['transaction_number'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function detail($id)
    {
        $companyCode = session('user_company_code');

        $kasBesar = DB::table('t_kas_besar as kb')
            ->leftJoin('users as cu', 'kb.created_by', '=', 'cu.id')
            ->leftJoin('t_expenses as ex', 'kb.f_expense_id', '=', 'ex.id')
            ->select([
                'kb.*',
                'cu.name as created_by_name',
                'ex.transaction_no as expense_transaction_no',
            ])
            ->where('kb.id', $id)
            ->where('kb.company_code', $companyCode)
            ->first();

        if (!$kasBesar) {
            abort(404, 'Data Kas Besar tidak ditemukan');
        }

        return view('pages.main_transaction.kas_besar.detail', [
            'kasBesar' => $kasBesar,
        ]);
    }

    public function generateTransactionNumber()
    {
        try {
            // Preview only: the number is taken when the top-up is saved.
            $transactionNumber = app(TransactionNumberService::class)->getNextNumberPreview('kas_besar');

            return response()->json([
                'success'            => true,
                'transaction_number' => $transactionNumber,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLatestSaldo()
    {
        $companyCode = session('user_company_code');

        $latest = DB::table('t_kas_besar')
            ->where('company_code', $companyCode)
            ->orderBy('transaction_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        return response()->json([
            'success'     => true,
            'saldo_after' => $latest ? $latest->saldo_after : 0,
        ]);
    }
}
