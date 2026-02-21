@extends('layouts.default')
@section('page_title')
    Laporan Buku Besar
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card mt-6">
        <h4 class="card-header">Laporan Buku Besar</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form method="GET" autocomplete="off" action="{{ route('report.buku_besar.index') }}">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Tanggal:</label>
                                <input type="text" id="date-range-picker" name="date_range" class="form-control" value="{{ $date_range }}" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">COA:</label>
                                <select class="global-select2 form-select" data-allow-clear="true" name="coa_code_filter[]" multiple>
                                    <option value="">Pilih COA</option>
                                    @foreach ($coas as $coa)
                                        <option value="{{ $coa->code }}"
                                            @if (isset($coa_code_filter) && in_array($coa->code, $coa_code_filter)) selected @endif>{{ $coa->code }} -
                                            {{ $coa->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-6 mx-n6" />
                <!-- Action Buttons -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between">
                        <!-- Column Toggle -->
                        <div class="d-flex align-items-center">
                        </div>

                        <div class="d-flex gap-2">
                            
                            <button type="submit" class="btn btn-md btn-primary waves-effect waves-light"><span
                                    class="icon-xs icon-base ti tabler-settings me-2"></span> Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr class="my-6 mx-n6" />
    @foreach ($buku_besar_data as $bb_row)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header"><b>AKUN: {{ $bb_row['coa_code'] }} - {{ $bb_row['coa_name'] }}</b></h5>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>No Ref</th>
                                        <th>Keterangan</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Debit</th>
                                        <th>Kredit</th>
                                        <th>Nilai Akhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $debit_amount = 0;
                                        $credit_amount = 0;
                                    @endphp
                                    @foreach ($bb_row['journal_data'] as $key => $journal_row)
                                        <tr>
                                            <td>{{ $key + 1 }}.</td>
                                            <td class="readable-date">{{ $journal_row->date }}</td>
                                            <td>{{ $journal_row->reference_no }}</td>
                                            <td>{{ $journal_row->description }}
                                            </td>
                                            <td>{{ $journal_row->created_by_name }}</td>
                                            <td dir="rtl">
                                                @if ($journal_row->debit_credit == 'D')
                                                    Rp. {{ number_format($journal_row->amount) }}
                                                    @php
                                                        $debit_amount += $journal_row->amount;
                                                    @endphp
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td dir="rtl">
                                                @if ($journal_row->debit_credit == 'C')
                                                    Rp. {{ number_format($journal_row->amount) }}
                                                    @php
                                                        $credit_amount += $journal_row->amount;
                                                    @endphp
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td dir="rtl">Rp. {{ number_format($journal_row->after_amount) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="6"><b>Total</b></td>
                                        <td dir="rtl"><b>Rp. {{ number_format($debit_amount) }}</b></td>
                                        <td dir="rtl"><b>Rp. {{ number_format($credit_amount) }}</b></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6"><b>Saldo Awal</b></td>
                                        <td colspan="2" dir="rtl">
                                            <b>Rp. {{ number_format($bb_row['opening_balance_amount']) }}</b>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6"><b>Mutasi</b></td>
                                        <td colspan="2" dir="rtl">
                                            @if (substr($bb_row['coa_code'], 0, 1) >= 2 && substr($bb_row['coa_code'], 0, 1) <= 4)
                                                <b>Rp. {{ number_format($credit_amount - $debit_amount) }}</b>
                                            @else
                                                <b>Rp. {{ number_format($debit_amount - $credit_amount) }}</b>
                                            @endif

                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6"><b>Saldo Akhir</b></td>
                                        <td colspan="2" dir="rtl">
                                            @if (substr($bb_row['coa_code'], 0, 1) >= 2 && substr($bb_row['coa_code'], 0, 1) <= 4)
                                                <b>Rp.
                                                    {{ number_format($bb_row['opening_balance_amount'] + $debit_amount - $credit_amount) }}</b>
                                            @else
                                                <b>Rp.
                                                    {{ number_format($bb_row['opening_balance_amount'] + $debit_amount - $credit_amount) }}</b>
                                            @endif

                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
    @endforeach
@stop
@section('js_script')
    <script>
        $(function() {

        });
    </script>
@endsection
