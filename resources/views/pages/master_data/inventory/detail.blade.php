@extends('layouts.default')

@section('page_title')
    Detail Aset - {{ $asset->name ?? 'Asset' }}
@stop

@section('content')
<div class="card mb-6">
    <div class="card-body">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h4 class="mb-3">Detail Pembelian Aset</h4>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-primary btn-md waves-effect waves-light" onclick="window.print()">
                        <i class="icon-base ti tabler-printer me-2"></i>Print
                    </button>
                    <a href="{{ route('master_data.inventory.edit', $asset->id) }}" class="btn btn-success btn-md waves-effect waves-light">
                        <i class="icon-base ti tabler-edit me-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>

        <!-- Asset Title -->
        <h2>{{ $asset->code ?? '-' }}</h2>
        <span>
            <h6>Data aset berdasarkan tanggal {{ $asset->created_at ? \Carbon\Carbon::parse($asset->created_at)->format('d M Y') : '-' }}</h6>
        </span>
        <hr class="my-6 mx-n6" />

        <!-- Informasi Dasar Transaksi Section - Outside tabs, on top -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tanggal Transaksi</label>
                <input type="text" class="form-control" value="{{ $asset->acquisition_date ? \Carbon\Carbon::parse($asset->acquisition_date)->format('d M Y') : '-' }}" disabled />
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Transaksi</label>
                <input type="text" class="form-control" value="{{ $asset->code ?? '-' }}" disabled />
            </div>
        </div>

        <hr class="my-6 mx-n6" />

        <!-- Tabs Section -->
        <div class="col-lg-12">
            <div class="demo-inline-spacing mt-4">
                <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                    <a class="list-group-item list-group-item-action waves-effect active" id="asset-info-list-item"
                        data-bs-toggle="list" href="#horizontal-asset-info" aria-selected="true" role="tab">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        <b>Informasi Detail</b>
                    </a>
                    <a class="list-group-item list-group-item-action waves-effect" id="transaction-history-list-item"
                        data-bs-toggle="list" href="#horizontal-transaction-history" aria-selected="false" role="tab">
                        <i class="icon-base ti tabler-file-invoice me-2"></i>
                        <b>History Transaksi</b>
                    </a>
                    <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                        data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                        <i class="icon-base ti tabler-history me-2"></i>
                        <b>Audit Log</b>
                    </a>
                </div>
                <div class="tab-content px-0 mt-0">
                    <!-- Information Tab -->
                    <div class="tab-pane fade show active" id="horizontal-asset-info" role="tabpanel">
                        <div id="asset-info-content">
                            <!-- Informasi Aset Section -->
                            <div class="section-header mt-12">
                                <h6>Informasi Aset</h6>
                                <hr class="mx-n6">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Aset</label>
                                    <input type="text" class="form-control" value="{{ $asset->name ?? '-' }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Aset</label>
                                    <input type="text" class="form-control" value="{{ $asset->code ?? '-' }}" disabled />
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Syarat Pembayaran</label>
                                    <input type="text" class="form-control" value="{{ $asset->payment_terms ?? '-' }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Jatuh Tempo</label>
                                    <input type="text" class="form-control" value="{{ $asset->due_date ? \Carbon\Carbon::parse($asset->due_date)->format('d M Y') : '-' }}" disabled />
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nilai Pembelian</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" value="@php $val = $asset->acquisition_value ?? 0; echo $val == floor($val) ? number_format($val, 0, ',', '.') : rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ','); @endphp" disabled />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $asset->description ?? '-' }}</textarea>
                                </div>
                            </div>

                            <!-- Informasi Pembelian Aset Section -->
                            <div class="section-header mt-12">
                                <h6>Informasi Pembelian Aset</h6>
                                <hr class="mx-n6">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Akun Aset Tetap</label>
                                    <input type="text" class="form-control" value="{{ $assetAccount ? $assetAccount->code . ' - ' . $assetAccount->name : '-' }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Akun Pembelian</label>
                                    <input type="text" class="form-control" value="{{ $purchaseAccount ? $purchaseAccount->code . ' - ' . $purchaseAccount->name : '-' }}" disabled />
                                </div>
                            </div>

                            <!-- Informasi Penyusutan Aset Section -->
                            <div class="section-header mt-12">
                                <h6>Informasi Penyusutan Aset</h6>
                                <hr class="mx-n6">
                            </div>

                            @if($asset->depreciation_method && $asset->depreciation_method != 'NONE')
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nilai/Tahun</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $asset->depreciation_rate ?? '0' }}" disabled />
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jangka Waktu Penyusutan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $asset->depreciation_period_months ?? '0' }}" disabled />
                                        <span class="input-group-text">Bulan</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Total Penyusutan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" value="@php $val = $asset->total_depreciation ?? 0; echo $val == floor($val) ? number_format($val, 0, ',', '.') : rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ','); @endphp" disabled />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nilai Buku</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" value="@php $val = $asset->net_book_value ?? ($asset->acquisition_value ?? 0); echo $val == floor($val) ? number_format($val, 0, ',', '.') : rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ','); @endphp" disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Akun Beban Penyusutan</label>
                                    <input type="text" class="form-control" value="{{ $depreciationAccount ? $depreciationAccount->code . ' - ' . $depreciationAccount->name : '-' }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Akun Akumulasi Penyusutan</label>
                                    <input type="text" class="form-control" value="{{ $accumulatedDepreciationAccount ? $accumulatedDepreciationAccount->code . ' - ' . $accumulatedDepreciationAccount->name : '-' }}" disabled />
                                </div>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="icon-base ti tabler-info-circle me-2"></i>
                                Aset ini tidak mengalami penyusutan
                            </div>
                            @endif

                            <!-- Informasi Tambahan & Ringkasan Section -->
                            <div class="section-header mt-12">
                                <h6>Informasi Tambahan & Ringkasan</h6>
                                <hr class="mx-n6">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Tag</label>
                                    <div class="mt-2">
                                        @if($tags && count($tags) > 0)
                                            @foreach($tags as $tag)
                                                <span class="badge {{ $tag->preview }} me-1">{{ $tag->code }} - {{ $tag->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Memo (Internal)</label>
                                    <textarea class="form-control" rows="4" disabled>{{ $asset->notes ?? '-' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction History Tab -->
                    <div class="tab-pane fade" id="horizontal-transaction-history" role="tabpanel">
                        <div class="section-header mt-12">
                            <h6>History Transaksi</h6>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="table table-bordered" id="history-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Transaksi</th>
                                        <th>Nomor Transaksi</th>
                                        <th>Akun</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Sample data - will be populated from actual transaction data -->
                                    <tr>
                                        <td>{{ $asset->acquisition_date ? \Carbon\Carbon::parse($asset->acquisition_date)->format('d M Y') : '-' }}</td>
                                        <td>Pembelian</td>
                                        <td>{{ $asset->code ?? '-' }}</td>
                                        <td>{{ $assetAccount ? $assetAccount->code . ' - ' . $assetAccount->name : '-' }}</td>
                                        <td>Rp @php $val = $asset->acquisition_value ?? 0; echo $val == floor($val) ? number_format($val, 0, ',', '.') : rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ','); @endphp</td>
                                        <td>-</td>
                                        <td><a href="#" class="link-primary">view</a></td>
                                    </tr>
                                    @if($asset->depreciation_method && $asset->depreciation_method != 'NONE')
                                    <tr>
                                        <td>{{ $asset->acquisition_date ? \Carbon\Carbon::parse($asset->acquisition_date)->addMonth()->format('d M Y') : '-' }}</td>
                                        <td>Penyusutan</td>
                                        <td>{{ $accumulatedDepreciationAccount ? $accumulatedDepreciationAccount->code : '-' }}</td>
                                        <td>{{ $accumulatedDepreciationAccount ? $accumulatedDepreciationAccount->code . ' - ' . $accumulatedDepreciationAccount->name : '-' }}</td>
                                        <td>-</td>
                                        <td>Rp @php $val = ($asset->acquisition_value ?? 0) * ($asset->depreciation_rate ?? 0) / 100; echo $val == floor($val) ? number_format($val, 0, ',', '.') : rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ','); @endphp</td>
                                        <td><a href="#" class="link-primary">view</a></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Audit Log Tab -->
                    <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
                        <div class="section-header mt-12">
                            <h6>Audit Log</h6>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="table table-bordered" id="audit-log-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>User</th>
                                        <th>Aksi</th>
                                        <th>Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($auditLogs as $log)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</td>
                                        <td>{{ $log->user_name ?? '-' }}</td>
                                        <td>
                                            <span class="badge
                                                @if($log->action_type === 'CREATE') bg-label-success
                                                @elseif($log->action_type === 'UPDATE') bg-label-info
                                                @elseif($log->action_type === 'DELETE') bg-label-danger
                                                @else bg-label-secondary
                                                @endif">
                                                {{ $log->action_type }}
                                            </span>
                                        </td>
                                        <td>{{ $log->description ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada audit log</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row mt-12">
            <div class="col-12">
                <a href="{{ route('master_data.inventory.index') }}" class="btn btn-label-secondary">
                    <i class="icon-base ti tabler-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('page_js')
<script>
$(function() {
    // Initialize DataTable for history
    $('#history-table').DataTable({
        ordering: false,
        paging: true,
        pageLength: 5,
        lengthChange: false,
        searching: false,
        info: false,
        language: {
            emptyTable: "Tidak ada data transaksi",
            zeroRecords: "Tidak ada data yang cocok"
        }
    });

    // Initialize DataTable for audit log
    $('#audit-log-table').DataTable({
        ordering: false,
        paging: true,
        pageLength: 10,
        lengthChange: false,
        searching: false,
        info: true,
        language: {
            emptyTable: "Belum ada audit log",
            zeroRecords: "Tidak ada data yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entries",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 entries"
        }
    });
});
</script>
@stop
