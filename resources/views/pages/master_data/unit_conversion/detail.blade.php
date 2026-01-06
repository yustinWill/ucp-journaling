@extends('layouts.default')
@section('page_title')
    Informasi Konversi Unit
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Konversi Unit</h4>
                </div>

                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            @if ($canShowEditButton)
                                <a href="{{ route('master_data.unit_conversion.edit', $unitConversion->id) }}"
                                    class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                            @endif
                            @if ($canShowDeleteButton)
                                <button class="btn btn-danger btn-md waves-effect waves-light"
                                    onclick="deleteItem({{ $unitConversion->id }})">
                                    <i class="icon-base ti tabler-trash me-2"></i>
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entity Title -->
            <h2>{{ $unitConversion->from_unit_code }} → {{ $unitConversion->to_unit_code }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Top-level Information -->
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Unit Asal</label>
                    <input type="text" class="form-control" value="{{ $unitConversion->from_unit_code }} - {{ $unitConversion->from_unit_name }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Unit Tujuan</label>
                    <input type="text" class="form-control" value="{{ $unitConversion->to_unit_code }} - {{ $unitConversion->to_unit_name }}" disabled />
                </div>
            </div>

            <div class="row g-6 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Faktor Konversi</label>
                    <input type="text" class="form-control" value="{{ $unitConversion->conversion_factor }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Formula Konversi</label>
                    <input type="text" class="form-control" value="{{ $unitConversion->conversion_formula ?: '-' }}" disabled />
                </div>
            </div>

            <div class="row g-6 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-control-plaintext">
                        @if ($unitConversion->status == 'ACTIVE')
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Inaktif</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>

            <!-- Conversion Preview -->
            <div class="row g-6 mt-3">
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        <h5 class="alert-heading mb-2">
                            <i class="icon-base ti tabler-calculator me-2"></i>Preview Konversi
                        </h5>
                        <p class="mb-0">
                            <strong>1 {{ $unitConversion->from_unit_code }} = {{ $unitConversion->conversion_factor }} {{ $unitConversion->to_unit_code }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <hr class="my-6 mx-n6" />

            <!-- Tab System with Section Headers -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" data-bs-toggle="list"
                            href="#horizontal-info" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Informasi Detail Tab -->
                        <div class="tab-pane fade show active" id="horizontal-info" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Detail Informasi Konversi</h6>
                                <hr class="mx-n6">
                            </div>

                            <div class="row g-6">
                                <div class="col-md-6">
                                    <label class="form-label">Catatan</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $unitConversion->notes ?: '-' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Dibuat Oleh</label>
                                    <input type="text" class="form-control" value="{{ $unitConversion->created_by_name ?? '-' }}" disabled />
                                    <label class="form-label mt-3">Dibuat Pada</label>
                                    <input type="text" class="form-control" value="{{ $unitConversion->created_at }}" disabled />
                                </div>
                            </div>

                            <div class="row g-6 mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Diupdate Oleh</label>
                                    <input type="text" class="form-control" value="{{ $unitConversion->updated_by_name ?? '-' }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Diupdate Pada</label>
                                    <input type="text" class="form-control" value="{{ $unitConversion->updated_at }}" disabled />
                                </div>
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
        // Delete item function
        function deleteItem(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus konversi unit ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('master_data.unit_conversion.delete', ':id') }}`.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                customClass: {
                                    confirmButton: 'btn btn-success waves-effect waves-light'
                                }
                            }).then(() => {
                                window.location.href = "{{ route('master_data.unit_conversion.index') }}";
                            });
                        },
                        error: function(xhr) {
                            const errorMessage = xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat menghapus data.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMessage,
                                customClass: {
                                    confirmButton: 'btn btn-danger waves-effect waves-light'
                                }
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
