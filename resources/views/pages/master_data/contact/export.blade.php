@extends('layouts.default')
@section('page_title')
    Ekspor Kontak
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Ekspor Kontak</h5>
        <form class="card-body form-repeater">
            <h6>1. Pilih Jenis Format</h6>
            <div class="row gy-3 mt-0">
                <div
                    class="col-xl-4 col-md-5 col-sm-6 col-12 form-control-validation fv-plugins-icon-container fv-plugins-bootstrap5-row-valid">
                    <div class="form-check custom-option custom-option-icon checked">
                        <label class="form-check-label custom-option-content" for="basicPlanMain1">
                            <span class="custom-option-body">
                                <i class="icon-base ti tabler-file-type-xls"></i>
                                <span class="custom-option-title"> Excel </span>
                                <small> File ini berisi data yang dapat langsung dibuka dan diedit menggunakan MS
                                    Excel atau aplikasi spreadsheet lainnya. </small>
                            </span>
                            <input name="formValidationPlan" class="form-check-input" type="radio" value=""
                                id="basicPlanMain1" checked="">
                        </label>
                    </div>
                </div>
                <div class="col-xl-4 col-md-5 col-sm-6 col-12 form-control-validation">
                    <div class="form-check custom-option custom-option-icon">
                        <label class="form-check-label custom-option-content" for="basicPlanMain2">
                            <span class="custom-option-body">
                                <i class="icon-base ti tabler-file-type-csv"></i>
                                <span class="custom-option-title"> CSV </span>
                                <small> Format teks sederhana yang cocok digunakan untuk pemrosesan data di berbagai sistem
                                    dan aplikasi. </small>
                            </span>
                            <input name="formValidationPlan" class="form-check-input" type="radio" value=""
                                id="basicPlanMain2">
                        </label>
                    </div>
                </div>
                <div class="col-xl-4 col-md-5 col-sm-6 col-12 form-control-validation">
                    <div class="form-check custom-option custom-option-icon">
                        <label class="form-check-label custom-option-content" for="basicPlanMain3">
                            <span class="custom-option-body">
                                <i class="icon-base ti tabler-file-type-pdf"></i>
                                <span class="custom-option-title"> PDF </span>
                                <small> Dokumen versi tetap yang mempertahankan format dan tampilan, ideal untuk dibaca atau
                                    dicetak. </small>
                            </span>
                            <input name="formValidationPlan" class="form-check-input" type="radio" value=""
                                id="basicPlanMain3">
                        </label>
                    </div>
                </div>
            </div>
            <hr class="my-6 mx-n6" />
            <h6>2. Tentukan Urutan Kolom Yang Ingin Di Ekspor</h6>
            <div class="text-nowrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Urutan Kolom</th>
                            <th style="width: 30%;">Nama Kolom</th>
                            <th style="width: 20%;">Sembunyikan Kolom?</th>
                            <th style="width: 30%;">Nama Field</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr>
                                <td>
                                    <input type="text" class="form-control" value="{{ $row['no'] }}" />
                                </td>
                                <td>{{ $row['value'] }}</td>
                                <td>
                                    <div class="form-check d-flex mb-0">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck1">
                                    </div>
                                </td>
                                <td> <input type="text" class="form-control" value="{{ $row['value'] }}" /></td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            <div class="pt-6 text-end">
                <button id="submit-btn" class="btn btn-primary me-4">Ekspor Data</button>
            </div>
        </form>
    </div>
@endsection
@section('js_script')
    <script>
        $(function() {
            
        });
    </script>
@endsection
