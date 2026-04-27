@extends('layouts.default')
@section('page_title')
    Tambah Cycle Baru
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Cycle Baru</h4>
        <form class="card-body" id="revenue-split-form" action="#" method="POST" autocomplete="off">
            @csrf

            <!-- Informasi Dasar Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Cycle <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="cycle-name" name="cycle_name"
                        placeholder="Cycle 6 - Q2 2025" maxlength="255" required
                        value="{{ isset($duplicateFrom) ? $duplicateFrom->cycle_name . ' (Copy)' : '' }}" />
                    <small class="form-text text-muted">Nama cycle tidak boleh sama dengan yang sudah ada</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <input type="text" class="form-control" id="description" name="description"
                        placeholder="Kuartal Kedua 2025" maxlength="255"
                        value="{{ isset($duplicateFrom) ? $duplicateFrom->description : '' }}" />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Mulai <span style="color: red;">*</span></label>
                    <input type="date" class="form-control" id="start-date" name="start_date" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Selesai <span style="color: red;">*</span></label>
                    <input type="date" class="form-control" id="end-date" name="end_date" required />
                </div>
            </div>

            <!-- Target Keuntungan Section -->
            <div class="section-header mt-12">
                <h6>Target Keuntungan</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Target Keuntungan Pabrik <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control currency-input" id="target-pabrik" name="target_pabrik"
                            placeholder="500000000" required
                            value="{{ isset($duplicateFrom) ? $duplicateFrom->target_pabrik : '' }}" />
                    </div>
                    <small class="form-text text-muted">Input angka (format Rupiah), tidak boleh negatif</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Target Keuntungan Owner <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control currency-input" id="target-owner" name="target_owner"
                            placeholder="500000000" required
                            value="{{ isset($duplicateFrom) ? $duplicateFrom->target_owner : '' }}" />
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Target Keuntungan Perusahaan <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control currency-input" id="target-perusahaan" name="target_perusahaan"
                            placeholder="500000000" required
                            value="{{ isset($duplicateFrom) ? $duplicateFrom->target_perusahaan : '' }}" />
                    </div>
                    <small class="form-text text-muted">Hanya menerima numerik, auto-format ke Rupiah</small>
                </div>
                <div class="col-md-6">
                    <!-- Total target calculation display -->
                    <label class="form-label">Total Target Keuntungan</label>
                    <div class="card bg-success text-white">
                        <div class="card-body p-3">
                            <h5 class="card-title mb-0" id="total-target-display">Rp 0</h5>
                            <small class="text-white-50">Total keuntungan dihitung otomatis, user tidak bisa edit manual</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aturan Data & Validasi Section -->
            <div class="section-header mt-12">
                <h6>Aturan Data & Validasi</h6>
                <hr class="mx-n6">
            </div>

            <div class="alert alert-info" role="alert">
                <h6 class="alert-heading">Informasi Penting:</h6>
                <ul class="mb-0">
                    <li><strong>Unik:</strong> Nama cycle tidak boleh sama dengan yang sudah ada</li>
                    <li><strong>Tanggal:</strong> Tidak boleh overlap dengan cycle lain</li>
                    <li><strong>Angka:</strong> Hanya menerima numerik, auto-format ke Rupiah</li>
                    <li><strong>Total keuntungan:</strong> Dihitung otomatis</li>
                    <li>Ditampilkan dalam box khusus agar mudah terlihat</li>
                </ul>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4"
                        placeholder="Catatan tambahan mengenai cycle ini...">{{ isset($duplicateFrom) ? $duplicateFrom->notes : '' }}</textarea>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                {{-- <button type="button" class="btn btn-label-secondary me-3" onclick="resetForm()">Reset Form</button> --}}
                <button type="button" class="btn btn-label-danger me-3" onclick="window.history.back()">Kembali</button>
                @if($canWrite)
                    <button type="submit" class="btn btn-success" id="submit-btn">Tambah Data</button>
                @endif
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Currency formatting function
            function formatCurrency(value) {
                if (!value) return '';
                const number = parseFloat(value.toString().replace(/[^\d]/g, ''));
                if (isNaN(number)) return '';
                return number.toLocaleString('id-ID');
            }

            // Parse currency to number
            function parseCurrency(value) {
                if (!value) return 0;
                return parseFloat(value.replace(/[^\d]/g, '')) || 0;
            }

            // Format currency inputs on keyup
            $('.currency-input').on('keyup', function() {
                const cursorPos = this.selectionStart;
                const value = this.value.replace(/[^\d]/g, '');
                const formatted = formatCurrency(value);
                this.value = formatted;
                
                // Calculate total
                calculateTotal();
            });

            // Calculate total target
            function calculateTotal() {
                const pabrik = parseCurrency($('#target-pabrik').val());
                const owner = parseCurrency($('#target-owner').val());
                const perusahaan = parseCurrency($('#target-perusahaan').val());
                const total = pabrik + owner + perusahaan;
                
                $('#total-target-display').text('Rp ' + total.toLocaleString('id-ID'));
            }

            // Reset form function
            window.resetForm = function() {
                $('#revenue-split-form')[0].reset();
                $('#total-target-display').text('Rp 0');
                $('.is-invalid').removeClass('is-invalid');
            };

            // Form validation and submission
            $('#revenue-split-form').on('submit', function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                const requiredFields = ['cycle-name', 'start-date', 'end-date', 'target-pabrik', 'target-owner', 'target-perusahaan'];

                requiredFields.forEach(function(fieldId) {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Validate date range
                const startDate = new Date($('#start-date').val());
                const endDate = new Date($('#end-date').val());
                if (startDate >= endDate) {
                    isValid = false;
                    $('#end-date').addClass('is-invalid');
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Tanggal',
                        text: 'Tanggal selesai harus setelah tanggal mulai.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Validasi',
                        text: 'Silakan lengkapi semua field yang wajib diisi.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Confirm save
                Swal.fire({
                    title: 'Simpan Cycle Baru?',
                    text: 'Apakah Anda yakin ingin membuat cycle keuntungan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.value) {
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                        );
                        
                        $.ajax({
                            url: "{{ route('master_data.revenue_split.save') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                cycle_name: $('#cycle-name').val(),
                                description: $('#description').val(),
                                start_date: $('#start-date').val(),
                                end_date: $('#end-date').val(),
                                target_pabrik: parseCurrency($('#target-pabrik').val()),
                                target_owner: parseCurrency($('#target-owner').val()),
                                target_perusahaan: parseCurrency($('#target-perusahaan').val()),
                                notes: $('#notes').val()
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Cycle keuntungan berhasil dibuat.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href = "{{ route('master_data.revenue_split.index') }}";
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
                                $('#submit-btn').prop('disabled', false).html('Tambah Data');
                            }
                        });
                    }
                });
            });

            // Real-time validation
            $('input, select, textarea').on('change blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            // Format any pre-filled values (e.g. from duplicate)
            $('.currency-input').each(function() {
                if (this.value) {
                    const raw = this.value.replace(/[^\d]/g, '');
                    this.value = formatCurrency(raw);
                }
            });

            // Initialize total calculation
            calculateTotal();
        });
    </script>
@endsection