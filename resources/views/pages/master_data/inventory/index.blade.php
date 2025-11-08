@extends('layouts.default')

@section('page_title')
    Aset & Inventaris
@stop

@section('content')
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Aset & Inventaris</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Kategori:</label>
                                    <select class="global-select2 form-select" id="inventory-category-filter"
                                        data-allow-clear="true">
                                        <option value="">Semua Kategori</option>
                                        <option value="KENDARAAN">Kendaraan</option>
                                        <option value="BANGUNAN">Bangunan</option>
                                        <option value="ELEKTRONIK">Elektronik</option>
                                        <option value="FURNITURE">Furniture</option>
                                        <option value="LAINNYA">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Status:</label>
                                    <select class="global-select2 form-select" id="inventory-status-filter"
                                        data-allow-clear="true">
                                        <option value="">Semua Status</option>
                                        <option value="ACTIVE">Aktif</option>
                                        <option value="INACTIVE">Tidak Aktif</option>
                                        <option value="MAINTENANCE">Maintenance</option>
                                        <option value="DISPOSED">Dijual/Dilepas</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Pencarian:</label>
                                    <input type="text" class="form-control" id="inventory-search"
                                        placeholder="Cari Aset/Inventaris">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <hr class="my-6 mx-n6" />
                <!-- Action Buttons -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between">
                        <!-- Column Toggle -->
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggleTagColumn" checked>
                                <label class="form-check-label" for="toggleTagColumn">
                                    <i class="icon-base ti tabler-tag me-1"></i>Tampilkan Kolom Tag
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" id="btn-add-inventory"
                                class="btn btn-md btn-primary waves-effect waves-light"><span
                                    class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Data Table -->
            <div class="card-datatable">
                <table id="inventory-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th>Nama Aset</th>
                            <th>Kategori</th>
                            <th>Nilai Perolehan</th>
                            <th>Nilai Buku</th>
                            <th>Metode Penyusutan</th>
                            <th>Status</th>
                            <th>Tag</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            let inventoryTable;

            inventoryTable = new DataTable('#inventory-table', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: `{{ route('master_data.inventory.index_data_table') }}`,
                    data: function(d) {
                        d.category_filter = $('#inventory-category-filter').val();
                        d.status_filter = $('#inventory-status-filter').val();
                        d.search = $('#inventory-search').val();
                        d.tab = 'active_assets'; // Default to active assets
                        return d;
                    }
                },
                columns: [{
                        data: 'acquisition_date',
                        responsivePriority: 2,
                        render: function(data, type, row) {
                            if (data) {
                                const date = new Date(data);
                                const day = String(date.getDate()).padStart(2, '0');
                                const month = String(date.getMonth() + 1).padStart(2, '0');
                                const year = date.getFullYear();
                                return `${day}-${month}-${year}`;
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'code',
                        responsivePriority: 3
                    },
                    {
                        data: 'name',
                        responsivePriority: 4
                    },
                    {
                        data: 'category',
                        responsivePriority: 5
                    },
                    {
                        data: 'acquisition_value',
                        className: 'text-end',
                        responsivePriority: 6,
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                            }
                            return 'Rp 0';
                        }
                    },
                    {
                        data: 'net_book_value',
                        className: 'text-end',
                        responsivePriority: 7,
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                            }
                            return 'Rp 0';
                        }
                    },
                    {
                        data: 'depreciation_method',
                        responsivePriority: 8,
                        render: function(data, type, row) {
                            if (data === 'STRAIGHT_LINE') return 'Garis Lurus';
                            if (data === 'REDUCING_BALANCE') return 'Saldo Menurun';
                            if (data === 'UNITS_OF_PRODUCTION') return 'Unit Produksi';
                            return 'Tidak Ada';
                        }
                    },
                    {
                        data: 'status',
                        responsivePriority: 9,
                        render: function(data, type, row) {
                            let badgeClass = 'bg-label-secondary';
                            let statusText = data;

                            if (data === 'ACTIVE') {
                                badgeClass = 'bg-label-success';
                                statusText = 'Aktif';
                            } else if (data === 'INACTIVE') {
                                badgeClass = 'bg-label-secondary';
                                statusText = 'Tidak Aktif';
                            } else if (data === 'MAINTENANCE') {
                                badgeClass = 'bg-label-warning';
                                statusText = 'Maintenance';
                            } else if (data === 'DISPOSED') {
                                badgeClass = 'bg-label-danger';
                                statusText = 'Dilepas';
                            }

                            return `<span class="badge ${badgeClass}">${statusText}</span>`;
                        }
                    },
                    {
                        data: 'tags',
                        responsivePriority: 10,
                        render: function(data, type, row) {
                            if (!data || data.length === 0) {
                                return '-';
                            }
                            return data.map(tag => {
                                if (!tag.name) return '';
                                return `<span class="badge ${tag.preview} me-1">${tag.name}</span>`;
                            }).join('');
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        width: '120px',
                        render: function(data, type, row, meta) {
                            const detailUrl = `{{ route('master_data.inventory.detail', ':id') }}`
                                .replace(':id', row.id);
                            const editUrl = `{{ route('master_data.inventory.edit', ':id') }}`
                                .replace(':id', row.id);
                            return `
                                <div class="d-flex gap-2">
                                    <a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    <a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>
                                </div>
                                `;
                        }
                    }
                ],
                layout: {
                    topStart: {
                        rowClass: 'm-0',
                        features: []
                    },
                    topEnd: {},
                    bottomStart: {
                        rowClass: 'row mx-3 justify-content-between',
                        features: ['info']
                    },
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
                responsive: true
            });

            // Button click events
            $('#btn-add-inventory').on('click', function() {
                window.location.href = "{{ route('master_data.inventory.add') }}";
            });

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                if (inventoryTable) {
                    const tagColumn = inventoryTable.column(8); // Tag column index
                    tagColumn.visible(this.checked);
                }
            });

            // Initialize tag column visibility based on checkbox state
            const initialTagVisibility = $('#toggleTagColumn').is(':checked');
            if (inventoryTable) {
                inventoryTable.column(8).visible(initialTagVisibility);
            }

            // Search and filter functionality with delay
            let searchTimeout;
            $('#inventory-status-filter, #inventory-category-filter, #inventory-search').on('keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (inventoryTable) {
                            inventoryTable.draw();
                        }
                    }, 400); // 400ms delay
                });
        });
    </script>
@endsection
