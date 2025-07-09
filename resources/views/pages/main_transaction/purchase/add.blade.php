@extends('layouts.default')
@section('page_title')
    Tambah Pembelian
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Tambah Pembelian Baru</h5>
        <form class="card-body" id="main-form">
            <h6>1. Data Tanggal & Supplier</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Pembelian <span style="color: red;">*</span></label>
                    <input type="text" class="form-control date-mask" value="" id="purchase-date" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nama User <span style="color: red;">*</span></label>
                    <input type="text" class="form-control readonly-field" value="Admin" disabled />
                </div>
                <div class="col-md-3">
                    <label class="form-label">No Referensi</label>
                    <input type="text" id='reference-no' class="form-control" value="20250701.PO.001" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Supplier <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="supplier-select" data-allow-clear="true">
                        <option value="">Pilih Supplier</option>
                        <option value="1">PT. Sumber Makmur</option>
                        <option value="2">CV. Berkah Jaya</option>
                        <option value="3">PT. Mandiri Sejahtera</option>
                        <option value="4">CV. Maju Bersama</option>
                    </select>
                </div>
            </div>
            <hr class="my-4 mx-n4" />
            <h6>2. Data Barang</h6>
            <div class="row g-3">
                <div class="col-12">
                    <span class="form-repeater-items">
                        <div data-repeater-list="items-list">
                            <div data-repeater-item>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Nama Barang <span style="color: red;">*</span></label>
                                        <input type="text" name="item_name" class="form-control" />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Jumlah <span style="color: red;">*</span></label>
                                        <input type="number" name="quantity" class="form-control quantity-field" value="1" min="1" />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                        <input type="text" name="unit_price" class="form-control unit-price-field curr-mask" dir="rtl" value="0" />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Subtotal</label>
                                        <input type="text" name="subtotal" class="form-control subtotal-field readonly-field" dir="rtl" value="0" disabled />
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Satuan</label>
                                        <select name="unit" class="form-select">
                                            <option value="pcs">Pcs</option>
                                            <option value="box">Box</option>
                                            <option value="kg">Kg</option>
                                            <option value="liter">Liter</option>
                                        </select>
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
                                <div class="col-md-2">

                                </div>
                                <div class="col-md-2">

                                </div>
                                <div class="col-md-2">

                                </div>
                                <div class="col-md-1">

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
            <h6>3. Informasi Tambahan</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" rows="3" id="purchase-note"></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="form-select" id="status-select">
                        <option value="Draft">Draft</option>
                        <option value="Pending">Pending</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>
            <hr class="my-4 mx-n4" />
            <h6>4. Summary</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Total Barang</label>
                    <input type="text" class="form-control readonly-field" id="total-items" dir="rtl" value="0" disabled />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total Amount</label>
                    <input type="text" class="form-control readonly-field" id="total-amount" dir="rtl" value="0" disabled />
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
            $('.form-repeater-items').repeater({
                show: function() {
                    $(this).slideDown();
                    $('.curr-mask').toArray().forEach(function(field) {
                        new Cleave(field, {
                            numeral: true,
                            numeralThousandsGroupStyle: 'thousand'
                        })
                    });
                    calculate_totals();
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                    calculate_totals();
                }
            });

            // Calculate subtotal when quantity or unit price changes
            $(document).on('input', '.quantity-field, .unit-price-field', function() {
                calculate_row_subtotal($(this).closest('[data-repeater-item]'));
                calculate_totals();
            });

            function calculate_row_subtotal(row) {
                const quantity = parseFloat(row.find('.quantity-field').val()) || 0;
                const unitPriceText = row.find('.unit-price-field').val().replace(/,/g, '');
                const unitPrice = parseFloat(unitPriceText) || 0;
                const subtotal = quantity * unitPrice;
                
                row.find('.subtotal-field').val(subtotal.toLocaleString('id-ID'));
            }

            function calculate_totals() {
                let totalItems = 0;
                let totalAmount = 0;

                $('[data-repeater-item]').each(function() {
                    const quantity = parseFloat($(this).find('.quantity-field').val()) || 0;
                    const subtotalText = $(this).find('.subtotal-field').val().replace(/,/g, '');
                    const subtotal = parseFloat(subtotalText) || 0;
                    
                    totalItems += quantity;
                    totalAmount += subtotal;
                });

                $('#total-items').val(totalItems.toLocaleString('id-ID'));
                $('#total-amount').val(totalAmount.toLocaleString('id-ID'));
            }

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
                            text: 'Data pembelian berhasil disimpan.',
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