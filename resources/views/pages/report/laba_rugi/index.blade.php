@extends('layouts.default')
@section('page_title')
    Laporan Laba Rugi
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card mt-6">
        <h4 class="card-header">Laporan Laba Rugi</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form method="GET" autocomplete="off" action="{{ route('report.laba_rugi.index') }}">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Tanggal:</label>
                                <input type="text" id="date-range-picker" name="date_range" class="form-control"
                                    value="{{ $date_range }}" />
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
    @if ($html)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header"><b>Laba Rugi Per {{ $from }} s/d
                        {{ $to }}</b></h5>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Kode Akun</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!! $html !!}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header"><b>Summary Laba Rugi Per {{ $from }} s/d
                        {{ $to }}</b>
                </h5>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Kode Akun</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Pendapatan</b></td>
                                    <td dir="rtl">Rp. {{ number_format($revenue_amount, 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td><b>Beban</b></td>
                                    <td dir="rtl">Rp. {{ number_format($expense_amount, 2, '.', ',') }}</td>
                                </tr>
                                @if ($variance_amount >= 0)
                                    <tr>
                                        <td><b>Laba</b></td>
                                        <td dir="rtl"><b><u>Rp.
                                                    {{ number_format($variance_amount, 2, '.', ',') }}</u></b></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td><b>Rugi</b></td>
                                        <td dir="rtl"><b><u>Rp.
                                                    {{ number_format($variance_amount, 2, '.', ',') }}</u></b></td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    

@stop
@section('js_script')
    <script>
        $(function() {

        });
    </script>
@endsection
