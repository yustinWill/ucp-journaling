@extends('layouts.default')
@section('page_title')
    Detail {{ $transactionNumber->module_display_name }} - Master Nomor Transaksi
@stop

@section('content')
<div class="card mb-6">
    <div class="card-body">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h4 class="mb-3">Detail Master Nomor Transaksi</h4>
            </div>
            <div class="col-md-4">
                @if($canEdit)
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('master_data.transaction_number.edit', $transactionNumber->id) }}" class="btn btn-primary btn-md waves-effect waves-light">
                            <i class="icon-base ti tabler-edit me-2"></i>
                            Edit Konfigurasi
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Module Title -->
        <h2>{{ $transactionNumber->module_display_name }}</h2>
        <span>
            <h6>Data berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
        </span>
        <div class="row g-6">
            <div class="col-md-6">
                <label class="form-label">Nama Modul</label>
                <input type="text" class="form-control" value="{{ $transactionNumber->module_display_name }}" disabled />
            </div>
            <div class="col-md-6">
                <label class="form-label">Kode Modul</label>
                <input type="text" class="form-control" value="{{ $transactionNumber->module_initial }}" disabled />
            </div>
        </div>
        <hr class="my-6 mx-n6" />

        <div class="col-lg-12">
            <div class="demo-inline-spacing mt-4">
                <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                    <a class="list-group-item list-group-item-action waves-effect active" id="config-info-list-item"
                        data-bs-toggle="list" href="#horizontal-config-info" aria-selected="true" role="tab">
                        <i class="icon-base ti tabler-settings me-2"></i>
                        <b>Konfigurasi</b>
                    </a>
                    <a class="list-group-item list-group-item-action waves-effect" id="preview-list-item"
                        data-bs-toggle="list" href="#horizontal-preview" aria-selected="false" role="tab">
                        <i class="icon-base ti tabler-eye me-2"></i>
                        <b>Preview</b>
                    </a>
                    <a class="list-group-item list-group-item-action waves-effect" id="placeholders-list-item"
                        data-bs-toggle="list" href="#horizontal-placeholders" aria-selected="false" role="tab">
                        <i class="icon-base ti tabler-code me-2"></i>
                        <b>Placeholder</b>
                    </a>
                </div>
                <div class="tab-content px-0 mt-0">
                    <!-- Configuration Tab -->
                    <div class="tab-pane fade show active" id="horizontal-config-info" role="tabpanel">
                        <!-- Current Configuration Section -->
                        <div class="section-header mt-12">
                            <h6>Konfigurasi Saat Ini</h6>
                            <hr class="mx-n6">
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Format Template</label>
                                <input type="text" class="form-control" value="{{ $transactionNumber->format_template }}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Counter Saat Ini</label>
                                <input type="text" class="form-control" value="{{ $transactionNumber->current_counter }}" disabled />
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Counter Padding</label>
                                <input type="text" class="form-control" value="{{ $transactionNumber->counter_padding }} digit" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reset Frequency</label>
                                <input type="text" class="form-control" value="{{ ucfirst($transactionNumber->reset_frequency) }}" disabled />
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" value="{{ $transactionNumber->status === 'active' ? 'Aktif' : 'Nonaktif' }}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Terakhir Reset</label>
                                <input type="text" class="form-control" value="{{ $transactionNumber->last_reset_date ?: 'Belum pernah' }}" disabled />
                            </div>
                        </div>

                        <!-- Timestamps Section -->
                        <div class="section-header mt-12">
                            <h6>Informasi Sistem</h6>
                            <hr class="mx-n6">
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Dibuat Pada</label>
                                <input type="text" class="form-control" value="{{ $transactionNumber->created_at }}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Diupdate Pada</label>
                                <input type="text" class="form-control" value="{{ $transactionNumber->updated_at }}" disabled />
                            </div>
                        </div>
                    </div>

                    <!-- Preview Tab -->
                    <div class="tab-pane fade" id="horizontal-preview" role="tabpanel">
                        <div class="section-header mt-12">
                            <h6>Contoh Nomor yang Akan Dihasilkan</h6>
                            <hr class="mx-n6">
                        </div>
                        
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Format Template Aktif</h6>
                            <code class="text-primary fs-5">{{ $transactionNumber->format_template }}</code>
                        </div>

                        <div class="row g-3 mb-4">
                            @foreach($previews as $index => $preview)
                                <div class="col-md-4">
                                    <label class="form-label">Nomor {{ $index + 1 }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fw-bold text-success" value="{{ $preview }}" disabled />
                                        <span class="input-group-text">
                                            <i class="icon-base ti tabler-check text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-warning">
                            <h6 class="alert-heading">Catatan Penting</h6>
                            <ul class="mb-0">
                                <li>Nomor dihasilkan berdasarkan tanggal dan waktu saat ini</li>
                                <li>Counter akan otomatis bertambah setiap kali nomor baru dibuat</li>
                                <li>Reset otomatis terjadi sesuai dengan pengaturan reset frequency</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Placeholders Tab -->
                    <div class="tab-pane fade" id="horizontal-placeholders" role="tabpanel">
                        <div class="section-header mt-12">
                            <h6>Placeholder yang Tersedia</h6>
                            <hr class="mx-n6">
                        </div>
                        
                        <div class="row g-3">
                            @foreach($placeholders as $placeholder => $info)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <code class="text-primary">{{ $placeholder }}</code>
                                                        @if($placeholder === '{COUNTER}')
                                                            <span class="badge bg-label-danger ms-2">Required</span>
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted mb-1">{{ $info['description'] }}</p>
                                                    <small class="text-success">Contoh: <strong>{{ $info['example'] }}</strong></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-info mt-4">
                            <h6 class="alert-heading">Cara Penggunaan</h6>
                            <ul class="mb-0">
                                <li>Gunakan placeholder di dalam kurung kurawal <code>{}</code></li>
                                <li>Placeholder <code>{COUNTER}</code> wajib ada dalam setiap template</li>
                                <li>Anda dapat menggabungkan beberapa placeholder, contoh: <code>RIYO-{YEAR}-{COUNTER}</code></li>
                                <li>Teks di luar placeholder akan ditampilkan apa adanya</li>
                            </ul>
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
    // Tab navigation is handled by Bootstrap automatically
});
</script>
@endsection