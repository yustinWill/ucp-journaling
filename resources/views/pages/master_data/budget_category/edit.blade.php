@extends('layouts.default')
@section('page_title')
    Edit Kategori Anggaran
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Kategori Anggaran</h4>
        <form class="card-body" id="budgetCategoryEditForm" autocomplete="off">
            <input type="hidden" id="category_id" value="{{ $category->id }}">
            <!-- Info Section -->
            <div class="section-header mt-12">
                <h6>Informasi Kategori</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Category Name and Status -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Kategori <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="category_name" value="{{ $category->category_name }}"
                        maxlength="255" @if(!$canWrite) disabled @endif />
                    <small class="form-text text-muted">Nama kategori anggaran yang akan digunakan</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false" @if(!$canWrite) disabled @endif>
                        <option value="active" {{ $category->status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $category->status === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($canWrite)
                <div class="pt-6 text-end">
                    <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                    <button type="button" class="btn btn-success" id="submit-btn">Simpan Perubahan</button>
                </div>
            @else
                <div class="pt-6 text-end">
                    <a href="{{ route('master_data.budget_category.index') }}" class="btn btn-label-secondary">Kembali</a>
                </div>
            @endif
        </form>
    </div>
@endsection

@section('js_script')
    <script>
        $(function() {
            @if($canWrite)
            // Form submission
            $('#submit-btn').click(function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                let errorMessage = '';

                if (!$('#category_name').val().trim()) {
                    isValid = false;
                    errorMessage = 'Nama kategori harus diisi.';
                }

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Validasi',
                        text: errorMessage,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                Swal.fire({
                    title: 'Perbarui Kategori?',
                    text: 'Cek kembali data yang sudah diubah!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                        );
                        $.ajax({
                            url: "{{ route('master_data.budget_category.update') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: $('#category_id').val(),
                                category_name: $('#category_name').val(),
                                status: $('#status-select').val()
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Kategori anggaran berhasil diperbarui.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href = "{{ route('master_data.budget_category.index') }}";
                                });
                            },
                            error: function(xhr) {
                                const res = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal menyimpan',
                                    html: res?.message || 'Terjadi kesalahan saat menyimpan data.',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                });
                            },
                            complete: function() {
                                $('#submit-btn').prop('disabled', false).html('Simpan Perubahan');
                            }
                        });
                    }
                });
            });

            // Cancel button
            $('#cancel-btn').click(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Batalkan Perubahan?',
                    text: 'Perubahan yang sudah dibuat akan hilang!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        window.location.href = "{{ route('master_data.budget_category.index') }}";
                    }
                });
            });
            @endif
        });
    </script>
@endsection
