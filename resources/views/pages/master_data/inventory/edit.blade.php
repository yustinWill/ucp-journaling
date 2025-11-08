@extends('layouts.default')

@section('page_title')
    Edit Asset / Inventaris
@stop

@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Asset / Inventaris: {{ $asset->name ?? 'N/A' }}</h4>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                <strong>Form sedang dalam pengembangan</strong>
                <p class="mb-0 mt-2">Halaman form edit Asset/Inventaris akan segera tersedia. Silakan lihat dokumentasi implementasi di <code>.claude-sessions/inventory_asset_module_implementation_summary.md</code> untuk detail lengkap.</p>
            </div>

            <div class="mt-4">
                <a href="{{ route('master_data.inventory.index') }}" class="btn btn-secondary">
                    <i class="icon-base ti tabler-arrow-left me-2"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('master_data.inventory.detail', $asset->id ?? 0) }}" class="btn btn-primary">
                    <i class="icon-base ti tabler-eye me-2"></i> Lihat Detail
                </a>
            </div>
        </div>
    </div>
@stop
