@extends('layouts.default')
@section('page_title')
    Detail Pengeluaran
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card">
        <h5 class="card-header">Detail Pengeluaran #{{ $id }}</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <p class="text-muted">Detail pengeluaran akan ditampilkan di sini.</p>
                    <p>ID Pengeluaran: <strong>{{ $id }}</strong></p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-4">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                            onclick="window.location.href='{{ route('main_transaction.expense.edit', $id) }}'">
                        <span class="icon-xs icon-base ti tabler-edit me-2"></span> Edit
                    </button>
                    <button type="button" class="btn btn-secondary waves-effect waves-light"
                            onclick="window.location.href='{{ route('main_transaction.expense.index') }}'">
                        <span class="icon-xs icon-base ti tabler-arrow-left me-2"></span> Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop