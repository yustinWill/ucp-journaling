@extends('layouts.default')
@section('page_title')
    Detail Stock Transfer
@stop
@php
    // Helper: format number, strip trailing zeros (5,00 → 5 | 0,5000 → 0,5 | 0,501000 → 0,501)
    function formatQty($value) {
        if ($value == floor($value)) {
            return number_format($value, 0, ',', '.');
        }
        $formatted = number_format($value, 4, ',', '.');
        return rtrim(rtrim($formatted, '0'), ',');
    }
@endphp
@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Detail Stock Transfer</h4>
            <a href="{{ route('master_data.warehouse.index') }}" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali ke List
            </a>
        </div>
        <div class="card-body">
            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="text" class="form-control"
                        value="{{ \Carbon\Carbon::parse($adjustment->adjustment_date)->format('d F Y') }}"
                        disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control"
                        value="{{ $adjustment->adjustment_number }}"
                        disabled />
                </div>
            </div>

            <!-- Informasi Adjustment Section -->
            <div class="section-header mt-12">
                <h6>Informasi Adjustment</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang Sumber</label>
                    <input type="text" class="form-control"
                        value="{{ $adjustment->warehouse_code }} - {{ $adjustment->warehouse_name }}"
                        disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang Tujuan</label>
                    <input type="text" class="form-control"
                        value="{{ $destinationWarehouse ? $destinationWarehouse->code . ' - ' . $destinationWarehouse->name : '-' }}"
                        disabled />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <input type="text" class="form-control"
                        value="{{ $adjustment->pic_name ?? '-' }}"
                        disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" rows="3" disabled>{{ $adjustment->description ?? '-' }}</textarea>
                </div>
            </div>

            <!-- Product List Section -->
            <div class="section-header mt-12">
                <h6>Daftar Produk yang Ditransfer</h6>
                <hr class="mx-n6">
            </div>

            <div class="mx-n5 mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Kode Produk</th>
                            <th style="width: 25%;">Nama Produk</th>
                            <th style="width: 12%;">Stok Tersedia</th>
                            <th style="width: 12%;">Qty Transfer</th>
                            <th style="width: 12%;">Sisa Stok</th>
                            <th style="width: 8%;">Unit</th>
                            <th style="width: 16%;">Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            @php
                                $sisaStok = $item->stock_before - $item->adjustment_qty;
                            @endphp
                            <tr>
                                <td><strong>{{ $item->product_code }}</strong></td>
                                <td>{{ $item->product_name }}</td>
                                <td class="text-end"><span class="text-info">{{ formatQty($item->stock_before) }}</span></td>
                                <td class="text-end">{{ formatQty($item->adjustment_qty) }}</td>
                                <td class="text-end">
                                    <span class="text-{{ $sisaStok < 0 ? 'danger' : 'info' }}">{{ formatQty($sisaStok) }}</span>
                                </td>
                                <td class="text-center">{{ $item->unit }}</td>
                                <td class="text-muted small">{{ $item->reason ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="icon-base ti tabler-box-off me-2"></i>
                                    Tidak ada produk
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Summary Section -->
            <div class="section-header mt-12">
                <h6>Ringkasan Stock Transfer</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-6">
                <div class="col-md-6">
                    <div class="summary-card success">
                        <h6>Total Item</h6>
                        <h4>{{ count($items) }}</h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-card">
                        <h6>Total Qty Transfer</h6>
                        <h4>{{ formatQty($items->sum('adjustment_qty')) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" rows="3" disabled>{{ $adjustment->memo ?? '-' }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($tags as $tag)
                            <span class="badge bg-label-{{ $tag->tag_color_code ?? 'primary' }}">
                                {{ $tag->code }} - {{ $tag->name }}
                            </span>
                        @empty
                            <span class="text-muted">-</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control"
                        value="{{ $adjustment->status }}"
                        disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Created At</label>
                    <input type="text" class="form-control"
                        value="{{ \Carbon\Carbon::parse($adjustment->created_at)->format('d F Y H:i') }}"
                        disabled />
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('master_data.warehouse.index') }}" class="btn btn-label-secondary">Kembali</a>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Detail page - no interactive functionality needed
        });
    </script>
@endsection
