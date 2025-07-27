@extends('layouts.default')
@section('page_title')
    Detail Pengeluaran
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Pengeluaran</h4>
                </div>
                
                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        @php
                            $totalActions = 1; // Edit always present
                            if($canEdit) {
                                $totalActions++;
                            }
                        @endphp
                        
                        @if($totalActions < 3)
                            <!-- Show buttons directly when less than 3 actions -->
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-primary btn-md waves-effect waves-light" onclick="window.print()">
                                    <i class="icon-base ti tabler-printer me-2"></i>Print
                                </button>
                                @if($canEdit)
                                    <a href="{{ route('main_transaction.expense.edit', $expense->id) }}" class="btn btn-success btn-md waves-effect waves-light">
                                        <i class="icon-base ti tabler-edit me-2"></i>Edit
                                    </a>
                                @endif
                            </div>
                        @else
                            <!-- Show dropdown for 3 or more actions -->
                            <div class="dropdown">
                                <button type="button" class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown">
                                    Aksi
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><button class="dropdown-item" onclick="window.print()">
                                        <i class="icon-base ti tabler-printer me-2"></i>Print</button></li>
                                    @if($canEdit)
                                        <li><a class="dropdown-item" href="{{ route('main_transaction.expense.edit', $expense->id) }}">
                                            <i class="icon-base ti tabler-edit me-2"></i>Edit</a></li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Expense Title -->
            <h2>{{ $expense->transaction_number }}</h2>
            <span>
                <h6>Data pengeluaran berdasarkan tanggal {{ $expense->created_at }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" value="{{ $expense->transaction_number }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <span class="badge bg-label-primary d-block p-2 text-center">{{ $expense->status }}</span>
                </div>
            </div>
            <hr class="my-6 mx-n6" />
            
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="expense-info-list-item"
                            data-bs-toggle="list" href="#horizontal-expense-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Information Tab -->
                        <div class="tab-pane fade show active" id="horizontal-expense-info" role="tabpanel">
                            <div id="expense-info-content">
                                <!-- Transaction Information Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Transaksi</h6>
                                    <hr class="mx-n6">
                                </div>
                                
                                <!-- Row 1: Payment Method, Pay Later, Recipient ---->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Metode Pembayaran</label>
                                        <input type="text" class="form-control" value="{{ $expense->payment_method }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Penerima</label>
                                        <input type="text" class="form-control" value="{{ $expense->recipient }}" disabled />
                                    </div>
                                </div>
                                
                                <!-- Row 2: Transaction Date, Payment Terms -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Transaksi</label>
                                        <input type="text" class="form-control" value="{{ $expense->transaction_date }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Syarat Pembayaran</label>
                                        <input type="text" class="form-control" value="{{ $expense->payment_terms }}" disabled />
                                    </div>
                                </div>
                                
                                <!-- Row 3: Due Date, Pay Later -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Jatuh Tempo</label>
                                        <input type="text" class="form-control" value="{{ $expense->due_date }}" disabled />
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" {{ $expense->pay_later ? 'checked' : '' }} disabled>
                                            <label class="form-check-label">
                                                Bayar Nanti
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Row 4: Billing Address & Description -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Alamat Penagihan</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $expense->billing_address }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Keterangan</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $expense->description }}</textarea>
                                    </div>
                                </div>

                                <!-- Detail Biaya Section -->
                                <div class="section-header mt-12">
                                    <h6>Detail Biaya Pengeluaran</h6>
                                </div>
                                
                                <div class="mx-n5 my-4">
                                    <table class="table table-bordered" id="expense-lines-table">
                                        <thead>
                                            <tr>
                                                <th>Akun Biaya</th>
                                                <th>Pajak</th>
                                                <th>Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expense->expense_lines as $line)
                                            <tr>
                                                <td>{{ $line->account_name }}</td>
                                                <td>{{ $line->tax_name ?: '-' }}</td>
                                                <td class="text-end">Rp {{ number_format($line->amount, 0, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <th colspan="2">Total</th>
                                                <th class="text-end">Rp {{ number_format($expense->final_total, 0, ',', '.') }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Informasi Tambahan & Ringkasan Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Tambahan & Ringkasan</h6>
                                    <hr class="mx-n6">
                                </div>
                                
                                <!-- Split Layout: Tag, Memo, Attachment & Financial Summary -->
                                <div class="row">
                                    <!-- Left Side: Tag, Memo and Attachment -->
                                    <div class="col-md-6 mb-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tag</label>
                                            @if($expense->tags && count($expense->tags) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($expense->tags as $tag)
                                                        <span class="badge {{ $tag->preview }}">{{ $tag->name }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted">Tidak ada tag</div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Memo (Internal)</label>
                                            <textarea class="form-control" rows="4" disabled>{{ $expense->memo }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Lampiran</label>
                                            <div class="d-flex align-items-center">
                                                <i class="icon-base ti tabler-file-text me-2"></i>
                                                <a href="#" class="text-primary">{{ $expense->attachment }}</a>
                                                <span class="text-muted ms-2">(1.2 MB)</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Side: Financial Summary Section -->
                                    <div class="col-md-6">
                                        <div class="summary-section ps-4">
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label class="form-label">Subtotal</label>
                                                    <input type="text" class="form-control" value="Rp {{ number_format($expense->subtotal, 0, ',', '.') }}" disabled />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label class="form-label">Total Pajak</label>
                                                    <input type="text" class="form-control" value="Rp {{ number_format($expense->tax_amount, 0, ',', '.') }}" disabled />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label class="form-label">Total Sebelum Diskon</label>
                                                    <input type="text" class="form-control" value="Rp {{ number_format($expense->total_before_discount, 0, ',', '.') }}" disabled />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label class="form-label">Diskon</label>
                                                    <input type="text" class="form-control" value="Rp {{ number_format($expense->discount_amount, 0, ',', '.') }}" disabled />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label class="form-label"><strong>Total Akhir</strong></label>
                                                    <input type="text" class="form-control fw-bold" value="Rp {{ number_format($expense->final_total, 0, ',', '.') }}" disabled />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $expense->created_at }}</td>
                                            <td><a href="#" class="text-primary fw-semibold">{{ $expense->created_by }}</a></td>
                                            <td><span class="badge bg-label-success">CREATE</span></td>
                                            <td>Pengeluaran dibuat dengan nomor {{ $expense->transaction_number }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ $expense->updated_at }}</td>
                                            <td><a href="#" class="text-primary fw-semibold">{{ $expense->created_by }}</a></td>
                                            <td><span class="badge bg-label-warning">UPDATE</span></td>
                                            <td>Status diubah menjadi {{ $expense->status }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
        // Initialize DataTables for tables inside tabs (no scrollX)
        $('#expense-lines-table').DataTable({
            responsive: true,
            orderCellsTop: true,
            searching: false,
            paging: false,
            info: false
        });

        $('#audit-log-table').DataTable({
            responsive: true,
            orderCellsTop: true,
            order: [[0, 'desc']], // Sort by date descending
            searching: false,
            paging: false,
            info: false
        });
    });
</script>
@endsection