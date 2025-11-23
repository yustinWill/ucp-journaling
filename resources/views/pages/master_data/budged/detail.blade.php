@extends('layouts.default')
@section('page_title')
    Informasi Anggaran - {{ $budged->budged_name }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Anggaran</h4>
                </div>

                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            @if ($canShowEditButton)
                                <a href="{{ route('master_data.budged.edit', $budged->id) }}"
                                    class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entity Title -->
            <h2>{{ $budged->budged_name }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Top-level Information (col-6 Maximum) -->
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Nama Anggaran</label>
                    <input type="text" class="form-control" value="{{ $budged->budged_name }}" disabled />
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <!-- Tab System with Section Headers -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    {{-- <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" data-bs-toggle="list"
                            href="#horizontal-periode" role="tab">
                            <i class="icon-base ti tabler-calendar me-2"></i>
                            <b>Periode Anggaran</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" data-bs-toggle="list"
                            href="#horizontal-audit-log" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div> --}}
                    <div class="tab-content px-0 mt-0">
                        <!-- Periode Anggaran Tab -->
                        <div class="tab-pane fade show active" id="horizontal-periode" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Daftar Periode Anggaran</h6>
                                <hr class="mx-n6">
                            </div>

                            <div class="mx-n6">
                                @forelse ($history as $index => $item)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <i class="icon-base ti tabler-calendar me-2"></i>
                                                        Periode {{ $index + 1 }}: {{ \Carbon\Carbon::parse($item->period_start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->period_end_date)->format('d/m/Y') }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <span class="badge bg-label-info">{{ count($item->expenses) }} Pengeluaran</span>
                                                    </small>
                                                </div>
                                                @php
                                                    $totalExpenses = $item->expenses->sum('total_amount');
                                                    $remaining = $item->budged_limit_value - $totalExpenses;
                                                    $remainingClass = $remaining < 0 ? 'text-danger' : 'text-success';
                                                @endphp
                                                <div class="text-end">
                                                    <div class="mb-1">
                                                        <small class="text-muted">Limit:</small>
                                                        <strong>Rp {{ number_format($item->budged_limit_value, 0, ',', '.') }}</strong>
                                                    </div>
                                                    <div class="mb-1">
                                                        <small class="text-muted">Terpakai:</small>
                                                        <strong class="text-danger">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</strong>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">Sisa:</small>
                                                        <strong class="{{ $remainingClass }}">Rp {{ number_format($remaining, 0, ',', '.') }}</strong>
                                                        @if($remaining < 0)
                                                            <small class="d-block text-danger">(Over Budget)</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if(count($item->expenses) > 0)
                                            <div class="card-body">
                                                <div class="accordion" id="accordion-{{ $item->id }}">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapse-{{ $item->id }}" aria-expanded="false">
                                                                <i class="icon-base ti tabler-receipt me-2"></i>
                                                                Lihat Detail Pengeluaran ({{ count($item->expenses) }})
                                                            </button>
                                                        </h2>
                                                        <div id="collapse-{{ $item->id }}" class="accordion-collapse collapse"
                                                            data-bs-parent="#accordion-{{ $item->id }}">
                                                            <div class="accordion-body p-0">
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table-hover mb-0">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th>No. Transaksi</th>
                                                                                <th>Tanggal</th>
                                                                                <th>Tipe</th>
                                                                                <th>Penerima</th>
                                                                                <th>Deskripsi</th>
                                                                                <th>Status Pembayaran</th>
                                                                                <th class="text-end">Total</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($item->expenses as $expense)
                                                                                <tr>
                                                                                    <td>
                                                                                        <a href="{{ route('main_transaction.expense.detail', $expense->id) }}"
                                                                                            class="text-primary">
                                                                                            {{ $expense->transaction_no }}
                                                                                        </a>
                                                                                    </td>
                                                                                    <td>{{ \Carbon\Carbon::parse($expense->transaction_date)->format('d/m/Y') }}</td>
                                                                                    <td>
                                                                                        @if($expense->type == 'PURCHASE')
                                                                                            <span class="badge bg-label-primary">Pembelian</span>
                                                                                        @elseif($expense->type == 'EXPENSE')
                                                                                            <span class="badge bg-label-warning">Biaya</span>
                                                                                        @else
                                                                                            <span class="badge bg-label-secondary">{{ $expense->type }}</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>{{ $expense->receiver_name ?? '-' }}</td>
                                                                                    <td>
                                                                                        <small>{{ $expense->description ? \Illuminate\Support\Str::limit($expense->description, 50) : '-' }}</small>
                                                                                    </td>
                                                                                    <td>
                                                                                        @if($expense->to_be_paid_later)
                                                                                            @if($expense->total_remaining_amount > 0)
                                                                                                <span class="badge bg-label-warning">Belum Lunas</span>
                                                                                                <small class="d-block text-muted">
                                                                                                    Sisa: Rp {{ number_format($expense->total_remaining_amount, 0, ',', '.') }}
                                                                                                </small>
                                                                                            @else
                                                                                                <span class="badge bg-label-success">Lunas</span>
                                                                                            @endif
                                                                                        @else
                                                                                            <span class="badge bg-label-success">Tunai</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-end">
                                                                                        <strong>Rp {{ number_format($expense->total_amount, 0, ',', '.') }}</strong>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                        <tfoot class="table-light">
                                                                            <tr>
                                                                                <th colspan="6" class="text-end">Total Pengeluaran:</th>
                                                                                <th class="text-end">
                                                                                    Rp {{ number_format($item->expenses->sum('total_amount'), 0, ',', '.') }}
                                                                                </th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="card-body">
                                                <p class="text-muted text-center mb-0">
                                                    <i class="icon-base ti tabler-inbox me-1"></i>
                                                    Belum ada pengeluaran pada periode ini
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="alert alert-info mb-0">
                                        <i class="icon-base ti tabler-info-circle me-2"></i>
                                        Belum ada periode anggaran
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Audit Log Tab -->
                        {{-- <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Riwayat Perubahan</h6>
                            </div>
                            <div class="mx-n6">
                                <table class="table table-bordered" id="audit-log-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal & Waktu</th>
                                            <th>Pengguna</th>
                                            <th>Aksi</th>
                                            <th>Deskripsi</th>
                                            <th>Lihat Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($auditLogs as $log)
                                            <tr>
                                                <td>{{ $log->created_at }}</td>
                                                <td>
                                                    <a href="{{ route('master_data.user.detail', ['id' => $log->user_id]) }}"
                                                        class="text-primary fw-semibold">{{ $log->user_name }}</a>
                                                </td>
                                                <td>
                                                    @if ($log->action_type == 'CREATE')
                                                        <span class="badge bg-label-success">CREATE</span>
                                                    @elseif($log->action_type == 'UPDATE')
                                                        <span class="badge bg-label-warning">UPDATE</span>
                                                    @elseif($log->action_type == 'DELETE')
                                                        <span class="badge bg-label-danger">DELETE</span>
                                                    @else
                                                        <span class="badge bg-label-info">{{ $log->action_type }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $log->description }}</td>
                                                <td>
                                                    @if ($log->action_type == 'UPDATE')
                                                        <a target="_blank"
                                                            href="{{ route('audit_log.detail', ['id' => $log->id, 'audit_log_type' => $auditLogType]) }}"
                                                            class="btn btn-sm btn-primary waves-effect waves-light"
                                                            title="Detail">
                                                            <i class="icon-xs icon-base ti tabler-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Initialize DataTable for audit log
            $('#audit-log-table').DataTable({
                responsive: true,
                orderCellsTop: true,
                searching: false,
                paging: false,
                info: false,
                order: [
                    [0, 'desc']
                ] // Sort by date descending
            });
        });
    </script>
@endsection
