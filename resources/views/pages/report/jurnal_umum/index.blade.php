@extends('layouts.default')
@section('page_title')
    Laporan Jurnal Umum
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card mt-6">
        <h4 class="card-header">Laporan Jurnal Umum</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form method="GET" autocomplete="off" action="{{ route('report.jurnal_umum.index') }}">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Tanggal:</label>
                                <input type="text" id="date-range-picker" name="date_range" class="form-control"
                                    value="{{ $date_range }}" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">COA:</label>
                                <select class="global-select2 form-select" data-allow-clear="true" name="coa_code_filter">
                                    <option value="">Pilih COA</option>
                                    @foreach ($coas as $coa)
                                        <option value="{{ $coa->code }}"
                                            @if ($coa->code === $coa_code_filter) selected @endif>{{ $coa->code }} -
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
                            {{-- <a href="{{ route('master_data.contact.import') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                        <a href="{{ route('master_data.contact.export') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a> --}}
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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header"><b>Jurnal Umum Tanggal {{ $from }} s/d {{ $to }}</b></h5>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="journal-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>No Ref</th>
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Keterangan</th>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                    <th>Dibuat Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $debit_amount = 0;
                                    $credit_amount = 0;
                                @endphp
                                @foreach ($journal_data as $key => $journal_row)
                                    <tr>
                                        <td>{{ $key + 1 }}.</td>
                                        <td class="readable-date">{{ $journal_row->date }}</td>
                                        <td>{{ $journal_row->reference_no }}</td>
                                        <td>{{ $journal_row->coa_code }}</td>
                                        <td>{{ $journal_row->coa_name }}</td>
                                        <td>{{ $journal_row->description }}</td>
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
                                        <td>{{ $journal_row->created_by_name }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6"><b>Total</b></td>
                                    <td dir="rtl"><b>Rp. {{ number_format($debit_amount) }}</b></td>
                                    <td dir="rtl"><b>Rp. {{ number_format($credit_amount) }}</b></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js_script')
    <script>
        $(function() {

        });
    </script>
@endsection
