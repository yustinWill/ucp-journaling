@extends('layouts.default')
@section('page_title')
    Laporan Neraca
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card mt-6">
        <h4 class="card-header">Laporan Neraca</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form method="GET" autocomplete="off" action="{{ route('report.neraca.index') }}">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Tanggal:</label>
                                <input type="text" id="base-date-picker" name="as_of_date" class="form-control"
                                    value="{{ $as_of_date }}" />
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

    @if ($html != '')
        <hr class="my-6 mx-n6" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header"><b>Neraca Per <span class="readable-date">{{ $as_of_date }}</span></b></h5>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><b>Kode Akun</b></th>
                                        <th><b>Nilai</b></th>
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
                    <h5 class="card-header"><b>Summary Neraca Per <span class="readable-date">{{ $as_of_date }}</span></b>
                    </h5>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><b>Kode Akun</b></th>
                                        <th><b>Nilai</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><b>Asset</b></td>
                                        <td dir="rtl">Rp. {{ number_format($asset_amount, 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Kewajiban</b></td>
                                        <td dir="rtl">Rp. {{ number_format($liability_amount, 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Ekuitas</b></td>
                                        <td dir="rtl">Rp. {{ number_format($equity_amount, 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Selisih</b></td>
                                        <td dir="rtl">Rp. {{ number_format($variance_amount, 2, '.', ',') }}</td>
                                    </tr>
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
