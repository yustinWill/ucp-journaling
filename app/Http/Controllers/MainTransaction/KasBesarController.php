<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Services\KasBesarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            ->leftJoin('t_expenses as ex', 'kb.f_expense_id', '=', 'ex.id')
            ->leftJoin('t_reimbursements as rb', 'rb.f_kas_besar_id', '=', 'kb.id')
            ->select([
                'kb.id',
                'kb.transaction_number',
                'kb.transaction_date',
                'kb.type',
                'kb.topup_amount',
                'kb.saldo_after',
                'kb.notes',
                'kb.f_expense_id',
                'ex.transaction_no as expense_transaction_no',
                'rb.id as reimbursement_id',
                'rb.transaction_code as reimbursement_code',
            ])
            ->where('kb.company_code', $companyCode);

        if ($request->filled('start_date')) {
            $query->where('kb.transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('kb.transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('search_transaction_no')) {
            $search = '%'.$request->search_transaction_no.'%';
            $query->where(fn ($q) => $q->where('kb.transaction_number', 'LIKE', $search)->orWhere('ex.transaction_no', 'LIKE', $search)->orWhere('rb.transaction_code', 'LIKE', $search));
        }

        $recordsTotal = DB::table('t_kas_besar')->where('company_code', $companyCode)->count();

        $filteredQuery = clone $query;
        $recordsFiltered = $filteredQuery->count();

        $query->orderBy('kb.transaction_date', 'DESC')->orderBy('kb.id', 'DESC');

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $query->get(),
            'summary' => app(KasBesarService::class)->summary($request->start_date, $request->end_date),
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
            'topup_amount' => 'required|numeric|min:0.01|max:9999999999999',
            'notes' => 'nullable|string|max:500',
        ], [
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh lebih dari hari ini.',
            'transaction_date.date' => 'Tanggal transaksi tidak valid.',
            'topup_amount.required' => 'Jumlah topup wajib diisi.',
            'topup_amount.numeric' => 'Jumlah topup harus berupa angka.',
            'topup_amount.min' => 'Jumlah topup harus lebih dari 0.',
            'topup_amount.max' => 'Jumlah topup terlalu besar.',
        ]);

        try {
            $row = app(KasBesarService::class)->record(
                'TOPUP',
                (float) $validated['topup_amount'],
                $validated['transaction_date'],
                $validated['notes'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Kas Besar berhasil ditambahkan dengan nomor: '.$row['transaction_number'],
                'id' => $row['id'],
                'transaction_number' => $row['transaction_number'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function detail($id)
    {
        $companyCode = session('user_company_code');

        $kasBesar = DB::table('t_kas_besar as kb')
            ->leftJoin('users as cu', 'kb.created_by', '=', 'cu.id')
            ->leftJoin('t_expenses as ex', 'kb.f_expense_id', '=', 'ex.id')
            ->leftJoin('t_reimbursements as rb', 'rb.f_kas_besar_id', '=', 'kb.id')
            ->select([
                'kb.*',
                'cu.name as created_by_name',
                'ex.transaction_no as expense_transaction_no',
                'rb.id as reimbursement_id',
                'rb.transaction_code as reimbursement_code',
            ])
            ->where('kb.id', $id)
            ->where('kb.company_code', $companyCode)
            ->first();

        if (! $kasBesar) {
            abort(404, 'Data Kas Besar tidak ditemukan');
        }

        return view('pages.main_transaction.kas_besar.detail', [
            'kasBesar' => $kasBesar,
        ]);
    }

    public function generateTransactionNumber(Request $request)
    {
        try {
            // Preview only: the number is taken when the top-up is saved.
            $transactionNumber = app(KasBesarService::class)->previewNumber($this->dateFrom($request));

            return response()->json([
                'success' => true,
                'transaction_number' => $transactionNumber,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /** Saldo at the end of the given date (default today) and the most a deduction on that date may take. */
    public function getLatestSaldo(Request $request)
    {
        $date = $this->dateFrom($request);
        $kas = app(KasBesarService::class);

        return response()->json([
            'success' => true,
            'date' => $date,
            'saldo_after' => $kas->saldoOn($date),
            'available' => $kas->availableOn($date),
        ]);
    }

    private function dateFrom(Request $request): string
    {
        $date = $request->query('date');

        return is_string($date) && strtotime($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
    }
}
