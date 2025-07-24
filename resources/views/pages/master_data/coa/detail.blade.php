@extends('layouts.default')
@section('page_title')
    Detail COA
@stop
@section('content')
    <!-- Header Section -->
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail COA</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('master_data.coa.edit', ['id' => $coa['id']]) }}"
                    class="btn btn-md btn-warning waves-effect waves-light">
                    <i class="icon-base ti tabler-edit me-2"></i>
                    Edit COA
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card mb-6">
        <div class="card-body">
            <!-- COA Title -->
            <h2>{{ $coa['account_name'] ?? '[1101] - Kas' }}</h2>
            <span>
                <h6>Data akun berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Kode Akun</label>
                    <input type="text" class="form-control" value="{{ $coa['account_code'] ?? '1101' }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori</label>
                    <input type="text" class="form-control" value="{{ $coa['category'] ?? 'Aset Lancar' }}" disabled />
                </div>
            </div>
            <hr class="my-6 mx-n6" />
            
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="coa-info-list-item"
                            data-bs-toggle="list" href="#coa-info-tab" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi COA</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="transactions-list-item"
                            data-bs-toggle="list" href="#transactions-tab" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-receipt-2 me-2"></i>
                            <b>Riwayat Transaksi</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#audit-log-tab" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- COA Information Tab -->
                        <div class="tab-pane fade show active" id="coa-info-tab" role="tabpanel">
                    <!-- Info COA Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi COA</h6>
                        <hr class="mx-n6">
                    </div>
                    
                    <!-- Row 1: Basic Information -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Kode COA</label>
                            <input type="text" class="form-control" value="{{ $coa['coa_code'] ?? '1101' }}" readonly />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama COA</label>
                            <input type="text" class="form-control" value="{{ $coa['coa_name'] ?? 'Kas' }}" readonly />
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Level</label>
                            <input type="text" class="form-control" value="{{ $coa['level'] ?? '4' }}" readonly />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Akun</label>
                            <input type="text" class="form-control" value="{{ $coa['account_type'] ?? 'AKTIVA' }}" readonly />
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-control-plaintext">
                                <span class="badge {{ ($coa['status'] ?? 'ACTIVE') === 'ACTIVE' ? 'bg-label-success' : 'bg-label-danger' }}">
                                    {{ $coa['status'] ?? 'ACTIVE' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Posisi Normal</label>
                            <input type="text" class="form-control" value="{{ $coa['normal_position'] ?? 'DEBIT' }}" readonly />
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" rows="3" readonly>{{ $coa['description'] ?? 'Akun kas untuk mencatat semua transaksi kas masuk dan keluar perusahaan.' }}</textarea>
                        </div>
                    </div>
                    
                    <!-- Summary Information -->
                    <div class="section-header mt-12">
                        <h6>Ringkasan Saldo</h6>
                        <hr class="mx-n6">
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Saldo Awal</label>
                            <input type="text" class="form-control text-end" value="{{ $coa['opening_balance'] ?? 'Rp 50,000,000' }}" readonly />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Debit</label>
                            <input type="text" class="form-control text-end" value="{{ $coa['total_debit'] ?? 'Rp 125,750,000' }}" readonly />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Kredit</label>
                            <input type="text" class="form-control text-end" value="{{ $coa['total_credit'] ?? 'Rp 98,250,000' }}" readonly />
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Saldo Akhir</label>
                            <input type="text" class="form-control text-end fw-bold" value="{{ $coa['ending_balance'] ?? 'Rp 77,500,000' }}" readonly />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Terakhir Diupdate</label>
                            <input type="text" class="form-control" value="{{ $coa['last_updated'] ?? '2025-01-20 16:45:30' }}" readonly />
                        </div>
                    </div>
                        </div>

                        <!-- Transactions Tab -->
                        <div class="tab-pane fade" id="transactions-tab" role="tabpanel">
                            <h5>Riwayat Transaksi</h5>
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-sm-6">
                            <div class="card card-border-shadow-primary h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar me-4">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="icon-base ti tabler-receipt icon-28px"></i>
                                            </span>
                                        </div>
                                        <h4 class="mb-0">156</h4>
                                    </div>
                                    <p class="mb-1">Total Transaksi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card card-border-shadow-success h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar me-4">
                                            <span class="avatar-initial rounded bg-label-success">
                                                <i class="icon-base ti tabler-trending-up icon-28px"></i>
                                            </span>
                                        </div>
                                        <h4 class="mb-0">Rp 125.7M</h4>
                                    </div>
                                    <p class="mb-1">Total Debit</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card card-border-shadow-warning h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar me-4">
                                            <span class="avatar-initial rounded bg-label-warning">
                                                <i class="icon-base ti tabler-trending-down icon-28px"></i>
                                            </span>
                                        </div>
                                        <h4 class="mb-0">Rp 98.3M</h4>
                                    </div>
                                    <p class="mb-1">Total Kredit</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card card-border-shadow-info h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar me-4">
                                            <span class="avatar-initial rounded bg-label-info">
                                                <i class="icon-base ti tabler-calculator icon-28px"></i>
                                            </span>
                                        </div>
                                        <h4 class="mb-0">Rp 27.4M</h4>
                                    </div>
                                    <p class="mb-1">Selisih</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Search Form -->
                    <form class="dt_adv_search" method="GET">
                        <div class="row">
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label">Tanggal Transaksi:</label>
                                        <input type="text" id="transaction-date-range-picker" class="form-control global-single-date-picker" />
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label">No Referensi:</label>
                                        <input type="text" class="form-control" id="reference-search" placeholder="Cari No Referensi" />
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label">Jenis Transaksi:</label>
                                        <select class="global-select2 form-select" id="transaction-type-filter" data-allow-clear="false">
                                            <option value="">Semua Jenis</option>
                                            <option value="jurnal">Jurnal</option>
                                            <option value="penjualan">Penjualan</option>
                                            <option value="pembelian">Pembelian</option>
                                            <option value="kas">Kas & Bank</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label">Deskripsi:</label>
                                        <input type="text" class="form-control" id="description-search" placeholder="Cari Deskripsi" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <hr class="my-6 mx-n6" />
                </div>
                
                <!-- Data Table -->
                <div class="card-datatable mx-n6">
                    <table class="table table-bordered" id="transactions-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No Referensi</th>
                                <th>Jenis</th>
                                <th>Deskripsi</th>
                                <th>Debit</th>
                                <th>Kredit</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2025-01-20</td>
                                <td>JU-2025-001</td>
                                {{-- <td><a href="{{ route('main_transaction.journal.detail', ['id' => 'JU-2025-001']) }}" class="text-primary fw-semibold">JU-2025-001</a></td> --}}
                                <td><span class="badge bg-label-info">Jurnal</span></td>
                                <td>Penerimaan kas dari penjualan produk</td>
                                <td class="text-end">Rp 15,500,000</td>
                                <td class="text-end">-</td>
                                <td class="text-end">Rp 93,000,000</td>
                            </tr>
                            <tr>
                                <td>2025-01-19</td>
                                <td>SI-2025-028</td>
                                {{-- <td><a href="{{ route('main_transaction.sales.detail', ['id' => 'SI-2025-028']) }}" class="text-primary fw-semibold">SI-2025-028</a></td> --}}
                                <td><span class="badge bg-label-success">Penjualan</span></td>
                                <td>Penjualan air mineral ke distributor</td>
                                <td class="text-end">Rp 8,750,000</td>
                                <td class="text-end">-</td>
                                <td class="text-end">Rp 77,500,000</td>
                            </tr>
                            <tr>
                                <td>2025-01-18</td>
                                <td>EX-2025-015</td>
                                {{-- <td><a href="{{ route('main_transaction.expense.detail', ['id' => 'EX-2025-015']) }}" class="text-primary fw-semibold">EX-2025-015</a></td> --}}
                                <td><span class="badge bg-label-warning">Biaya</span></td>
                                <td>Pembayaran biaya operasional</td>
                                <td class="text-end">-</td>
                                <td class="text-end">Rp 3,250,000</td>
                                <td class="text-end">Rp 68,750,000</td>
                            </tr>
                            <tr>
                                <td>2025-01-17</td>
                                <td>PI-2025-012</td>
                                {{-- <td><a href="{{ route('main_transaction.purchase.detail', ['id' => 'PI-2025-012']) }}" class="text-primary fw-semibold">PI-2025-012</a></td> --}}
                                <td><span class="badge bg-label-primary">Pembelian</span></td>
                                <td>Pembayaran supplier bahan baku</td>
                                <td class="text-end">-</td>
                                <td class="text-end">Rp 12,500,000</td>
                                <td class="text-end">Rp 72,000,000</td>
                            </tr>
                            <tr>
                                <td>2025-01-16</td>
                                <td>JU-2025-002</td>
                                {{-- <td><a href="{{ route('main_transaction.journal.detail', ['id' => 'JU-2025-002']) }}" class="text-primary fw-semibold">JU-2025-002</a></td> --}}
                                <td><span class="badge bg-label-info">Jurnal</span></td>
                                <td>Setoran modal dari investor</td>
                                <td class="text-end">Rp 25,000,000</td>
                                <td class="text-end">-</td>
                                <td class="text-end">Rp 84,500,000</td>
                            </tr>
                        </tbody>
                    </table>
                            </div>
                        </div>

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="audit-log-tab" role="tabpanel">
                            <h5>Riwayat Perubahan</h5>
                <div class="card-datatable mx-n6">
                    <table class="table table-bordered" id="audit-log-table">
                        <thead>
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2025-01-20 10:30:15</td>
                                <td><a href="{{ route('master_data.user.detail', ['id' => 'USR-001']) }}" class="text-primary fw-semibold">Admin User</a></td>
                                <td><span class="badge bg-label-warning">UPDATE</span></td>
                                <td>Mengubah nama COA dari "Kas Kecil" menjadi "Kas"</td>
                            </tr>
                            <tr>
                                <td>2025-01-15 14:22:40</td>
                                <td><a href="{{ route('master_data.user.detail', ['id' => 'USR-002']) }}" class="text-primary fw-semibold">Finance Manager</a></td>
                                <td><span class="badge bg-label-info">UPDATE</span></td>
                                <td>Memperbarui deskripsi COA dengan detail yang lebih lengkap</td>
                            </tr>
                            <tr>
                                <td>2025-01-10 09:15:25</td>
                                <td><a href="{{ route('master_data.user.detail', ['id' => 'USR-003']) }}" class="text-primary fw-semibold">System Admin</a></td>
                                <td><span class="badge bg-label-success">CREATE</span></td>
                                <td>COA kas dibuat untuk pencatatan transaksi kas masuk dan keluar</td>
                            </tr>
                        </tbody>
                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@stop

@section('js_script')
<script>
    $(function() {
        // Initialize DataTables for transactions and audit log
        $('#transactions-table').DataTable({
            scrollX: true,
            responsive: true,
            orderCellsTop: true,
            order: [[0, 'desc']], // Sort by date descending
            pageLength: 25,
            columnDefs: [
                {
                    targets: [4, 5, 6], // Debit, Credit, Saldo columns
                    className: 'text-end'
                }
            ],
            language: {
                paginate: {
                    next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                    previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                    first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                    last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                }
            }
        });

        $('#audit-log-table').DataTable({
            responsive: true,
            orderCellsTop: true,
            searching: false,
            paging: false,
            info: false,
            order: [[0, 'desc']] // Sort by date descending
        });
        
        // Search functionality
        $('#reference-search').on('keyup', function() {
            $('#transactions-table').DataTable().column(1).search(this.value).draw();
        });
        
        $('#description-search').on('keyup', function() {
            $('#transactions-table').DataTable().column(3).search(this.value).draw();
        });
        
        $('#transaction-type-filter').on('change', function() {
            $('#transactions-table').DataTable().column(2).search(this.value).draw();
        });
    });
</script>
@endsection