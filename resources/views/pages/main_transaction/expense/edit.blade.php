@extends('layouts.default')
@section('page_title')
    Edit Pengeluaran
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card">
        <h5 class="card-header">Edit Pengeluaran #{{ $id }}</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <p class="text-muted">Form edit pengeluaran akan diimplementasikan di sini.</p>
                    <p>ID Pengeluaran: <strong>{{ $id }}</strong></p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-4">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light">
                        <span class="icon-xs icon-base ti tabler-device-floppy me-2"></span> Simpan
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