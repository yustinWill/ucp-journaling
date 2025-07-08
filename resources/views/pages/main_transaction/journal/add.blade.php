@extends('layouts.default')
@section('page_title')
    Tambah Jurnal
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Tambah Jurnal Baru</h5>
        <form class="card-body" id="main-form">
            <h6>1. Data Tanggal & Penginput</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Jurnal <span style="color: red;">*</span></label>
                    <input type="text" class="form-control date-mask" value="" id="journal-date" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nama User <span style="color: red;">*</span></label>
                    <input type="text" class="form-control readonly-field" value="Cinta" disabled />
                </div>
                <div class="col-md-3">
                    <label class="form-label">No Referensi</label>
                    <input type="text" id='reference-no' class="form-control" value="20250701.JLL.QYZ33U" />
                </div>
            </div>
            <hr class="my-4 mx-n4" />
            <h6>2. Data Debit</h6>
            <div class="row g-3">
                <div class="col-12">
                    <span class="form-repeater-debit">
                        <div data-repeater-list="debit-list">
                            <div data-repeater-item>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Kode Akun <span style="color: red;">*</span></label>
                                        <select class="select2 form-select select2-coa-debit" name="debit_coa"
                                            data-allow-clear="true">
                                            <option value="">Select</option>
                                            {{-- @foreach ($coa_list as $row)
                                                <option value="{{ $row->code }}">{{ $row->code }} -
                                                    {{ $row->name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Nominal <span style="color: red;">*</span></label>
                                        <input type="text" name="debit_nominal"
                                            class="form-control select2-debit curr-mask" dir="rtl" value="0" />
                                    </div>
                                    <div class="mb-3 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                                        <button type="button" class="btn btn-label-danger mt-4" data-repeater-delete>
                                            <i class="icon-xs icon-base ti tabler-trash me-2"></i>
                                            <span class="align-middle">Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="row">
                                <div class="col-md-3">

                                </div>
                                <div class="col-md-3">

                                </div>
                                <div class="mb-3 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                                    <button type="button" class="btn btn-primary" data-repeater-create>
                                        <i class="icon-xs icon-base ti tabler-plus me-2"></i>
                                        <span class="align-middle">Tambah</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </span>
                </div>
            </div>
            <hr class="my-4 mx-n4" />
            <h6>3. Data Kredit</h6>
            <div class="row g-3">
                <div class="col-12">
                    <span class="form-repeater-credit">
                        <div data-repeater-list="credit-list">
                            <div data-repeater-item>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Kode Akun <span style="color: red;">*</span></label>
                                        <select class="select2 form-select select2-coa-credit" name="credit_coa"
                                            data-allow-clear="true">
                                            <option value="">Select</option>
                                            {{-- @foreach ($coa_list as $row)
                                                <option value="{{ $row->code }}">{{ $row->code }} -
                                                    {{ $row->name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Nominal <span style="color: red;">*</span></label>
                                        <input type="text" name="credit_nominal"
                                            class="form-control select2-credit curr-mask" dir="rtl" value="0" />
                                    </div>
                                    <div class="mb-3 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                                        <button type="button" class="btn btn-label-danger mt-4" data-repeater-delete>
                                            <i class="icon-xs icon-base ti tabler-trash me-2"></i>
                                            <span class="align-middle">Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="row">
                                <div class="col-md-3">

                                </div>
                                <div class="col-md-3">

                                </div>
                                <div class="mb-3 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                                    <button type="button" class="btn btn-primary" data-repeater-create>
                                        <i class="icon-xs icon-base ti tabler-plus me-2"></i>
                                        <span class="align-middle">Tambah</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </span>
                </div>
            </div>
            <hr class="my-4 mx-n4" />
            <h6>4. Catatan Jurnal</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Catatan <span style="color: red;">*</span></label>
                    <textarea class="form-control" rows="3" id="journal-note"></textarea>
                </div>
            </div>
            <hr class="my-4 mx-n4" />
            <h6>5. Summary</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Total Debit</label>
                    <input type="text" class="form-control readonly-field" id="total-debit" dir="rtl"
                        value="0" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total Kredit</label>
                    <input type="text" class="form-control readonly-field" id="total-credit" dir="rtl"
                        value="0" />
                </div>
            </div>
            <div class="pt-6 text-end">
                <button id="submit-btn" class="btn btn-primary me-4">Simpan Data</button>
            </div>
        </form>
    </div>
@endsection
@section('js_script')
    <script>
        $(function() {
            $('.form-repeater-debit').repeater({
                show: function() {
                    $(this).slideDown();
                    $('.select2-container').remove();
                    $('.select2').select2({
                        placeholder: "Select value",
                        allowClear: true
                    });
                    $('.select2-container').css('width', '100%');
                    $('.curr-mask').toArray().forEach(function(field) {
                        new Cleave(field, {
                            numeral: true,
                            numeralThousandsGroupStyle: 'thousand'
                        })
                    });
                    calculate_total_debit();
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });

            $('.form-repeater-credit').repeater({
                show: function() {
                    $(this).slideDown();
                    $('.select2-container').remove();
                    $('.select2').select2({
                        placeholder: "Select value",
                        allowClear: true
                    });
                    $('.select2-container').css('width', '100%');
                    $('.curr-mask').toArray().forEach(function(field) {
                        new Cleave(field, {
                            numeral: true,
                            numeralThousandsGroupStyle: 'thousand'
                        })
                    });
                    calculate_total_credit();
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });

            $('#submit-btn').click(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Simpan Data?',
                    text: "Cek kembali data yang sudah diisi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan Data!',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data berhasil disimpan.',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-label-secondary'
                            },
                        });
                    }


                })
            });
        });
    </script>
@endsection
