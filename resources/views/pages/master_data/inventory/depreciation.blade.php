@extends('layouts.default')

@section('page_title')
    Buat Transaksi - Penyusutan Bulanan Aset
@stop

@section('content')
<div class="card mb-6">
    <h4 class="card-header">Buat Transaksi - Penyusutan Bulanan Aset</h4>
    <form class="card-body" id="inventory-purchase-form" autocomplete="off">
        @csrf
        <div class="card-body">
            <h5 class="mb-4">Daftar Penyusutan</h5>

            <!-- Filter Bar -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Periode Bulan:</label>
                    <select class="global-select2 form-select" id="depreciation-month" name="month_filter" data-allow-clear="false">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11" selected>November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tahun:</label>
                    <select class="global-select2 form-select" id="depreciation-year" name="year_filter" data-allow-clear="false">
                        <option value="2024">2024</option>
                        <option value="2025" selected>2025</option>
                    </select>
                </div>
                
            </div>

            <hr class="my-6 mx-n6" />
            <!-- Action Buttons -->
            <div class="mt-4 mb-4">
                <div class="d-flex justify-content-end">
                    <div class="d-flex gap-2">
                        <button  class="btn btn-md btn-success waves-effect waves-light" id="execute-depreciation-button" type="submit">
                            <span class="icon-xs icon-base ti tabler-calculator me-2"></span> Eksekusi Penyusutan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-datatable">
            <table id="depreciation-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 120px;">Tanggal</th>
                        <th>Nama Aset</th>
                        <th style="width: 180px;">Periode</th>
                        <th style="width: 140px;">Nilai</th>
                        <th style="width: 140px;">Metode</th>
                        <th style="width: 150px;">Akumulasi Penyusutan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </form>
</div>
@stop

@section('js_script')
<script>
$(function() {
    let customDataTable;

    // Helper function to format numbers
    function formatNumber(num) {
        return parseFloat(num || 0).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    // Initialize the DataTable
    function initCustomDataTable() {
        if (customDataTable) {
            customDataTable.destroy();
        }
        function formatMonthYear(dateString) {
            const date = new Date(dateString);
            const monthNames = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();
            return `${month} ${year}`;
        }
        const monthFilter = $('#depreciation-month').val();
        const yearFilter = $('#depreciation-year').val();

        customDataTable = new DataTable('#depreciation-table', {
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("master_data.inventory.depreciation_data_table") }}', // Replace with your route
                data: {
                    month: monthFilter,
                    year: yearFilter
                },
                dataSrc: function(json) {
                    return json.data;
                }
            },
            columns: [
                {
                    data: 'created_at',
                    width: '120px',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                    }
                },
                { data: 'name' },
                {
                    data: 'depreciation_month_year',
                    width: '180px',
                    render: function(data) {
                        return data ? formatMonthYear(data) : '-';
                    }
                },
                {
                    data: 'depreciation_value',
                    width: '140px',
                    className: 'text-end',
                    render: function(data) {
                        return 'Rp ' + formatNumber(data);
                    }
                },
                {
                    data: 'depreciation_method',
                    width: '140px',
                    render: function(data, type, row) {
                        const rate = row.depreciation_rate || 0;
                        const methodName = data === 'STRAIGHT_LINE' ? 'Straight Line' :
                                         data === 'REDUCING_BALANCE' ? 'Reducing Balance' : data;
                        return rate + '% ' + methodName;
                    }
                },
                {
                    data: 'total_depreciation_value',
                    width: '150px',
                    className: 'text-end',
                    render: function(data) {
                        return 'Rp ' + formatNumber(data);
                    }
                }
                
            ],
            layout: {
                topStart: { rowClass: 'm-0', features: [] },
                topEnd: {},
                bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
                bottomEnd: 'paging'
            },
            language: {
                paginate: {
                    next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                    previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                    first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                    last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                }
            },
            scrollX: true,
            responsive: true,
            orderCellsTop: true,
            ordering: false
        });
    }

    // Initialize the DataTable on page load
    initCustomDataTable();

    // Reinitialize the DataTable when filters change
    $('#depreciation-month, #depreciation-year').on('change', function() {
        initCustomDataTable();
    });

    // Form submission
    $('#inventory-purchase-form').on('submit', function(e) {
        e.preventDefault();
        // Show confirmation
        Swal.fire({
            title: 'Eksekusi Penyusutan Aset?',
            text: 'Pastikan data yang diisi sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Buat!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    });

    function submitForm() {
        // Show loading state
        $('#submit-btn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
        );

        // Collect form data
        const formData = new FormData($('#inventory-purchase-form')[0]);

        // AJAX submission
        $.ajax({
            url: '{{ route("master_data.inventory.depreciation_save") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Transaksi pembelian aset berhasil dibuat.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = '{{ route("master_data.inventory.index") }}';
                });
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menyimpan data.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    title: 'Gagal!',
                    text: errorMessage,
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            },
            complete: function() {
                $('#submit-btn').prop('disabled', false).html('Simpan');
            }
        });
    }
});
</script>
@stop
