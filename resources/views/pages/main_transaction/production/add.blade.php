@extends('layouts.default')
@section('page_title')
    Tambah Produksi
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Produksi</h4>
        <form class="card-body" id="production-form" autocomplete="off" enctype="multipart/form-data">
            @csrf

            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="production-date"
                        name="production_date" value="{{ date('Y-m-d') }}" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="production-number" name="production_number"
                                placeholder="Auto-generated but editable" />
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                id="generate-number-btn" title="Generate New Number">
                                <i class="icon-base ti tabler-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Produksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Produksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Bundle Product <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="bundle-product" name="bundle_product_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih Bundle Product</option>
                                @foreach ($bundleProducts as $product)
                                    <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddProducts)
                            <div class="col-auto">
                                <a href="{{ route('master_data.product.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center"
                                    title="Tambah Bundle Product Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih product dengan tipe bundle</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="warehouse" name="warehouse_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->code }} - {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddWarehouses)
                            <div class="col-auto">
                                <a href="{{ route('master_data.warehouse.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Gudang Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Quantity Planned <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="quantity-planned" name="quantity_planned"
                        placeholder="100" min="1" required />
                    <small class="form-text text-muted">Jumlah bundle yang akan diproduksi</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="pic-user" name="pic_user_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih PIC</option>
                                @foreach ($picUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddContacts)
                            <div class="col-auto">
                                <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Contact Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bundle Components Section -->
            <div class="section-header mt-12">
                <h6>Komponen Bundle</h6>
            </div>
            <div id="components-disabled-message" class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                Silakan pilih <strong>Bundle Product</strong> terlebih dahulu untuk melihat komponen yang diperlukan.
            </div>
            <div id="components-section" style="display: none;">
                <div class="mx-n5">
                    <table class="table table-bordered" id="components-table">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Komponen Product</th>
                                <th style="width: 15%;">Qty per Bundle</th>
                                <th style="width: 15%;">Total Required</th>
                                <th style="width: 15%;">Stock Available</th>
                                <th style="width: 20%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="components-rows">
                            <!-- Rows will be populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Informasi Tambahan Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3"
                        placeholder="Jelaskan detail dan tujuan produksi..." maxlength="1000"></textarea>
                    <small class="form-text text-muted">Maksimal 1000 karakter</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" id="production-memo" name="memo" rows="3"
                        placeholder="Catatan internal untuk produksi..." maxlength="500"></textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Lampiran Dokumen</label>
                    <input type="file" class="form-control" id="attachment" name="attachment"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">Format: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max: 5MB)</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.production.index') }}" class="btn btn-label-danger me-3">Batalkan</a>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Generate number button functionality
            $('#generate-number-btn').on('click', function() {
                $.ajax({
                    url: "{{ route('main_transaction.production.generate_transaction_number') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#production-number').val(response.transaction_number);
                        }
                    },
                    error: function() {
                        // Fallback to client-side generation
                        $('#production-number').val('PROD-' + new Date().getFullYear() + '-' +
                            String(Date.now()).substr(-3));
                    }
                });
            });

            // Auto-generate number on page load
            $('#generate-number-btn').click();

            // Bundle product selection change
            $('#bundle-product').on('change', function() {
                const bundleProductId = $(this).val();

                if (bundleProductId) {
                    // TODO: Load bundle components via AJAX
                    loadBundleComponents(bundleProductId);
                    $('#components-disabled-message').hide();
                    $('#components-section').show();
                } else {
                    $('#components-disabled-message').show();
                    $('#components-section').hide();
                    $('#components-rows').empty();
                }
            });

            // Quantity planned change
            $('#quantity-planned').on('input', function() {
                updateTotalRequiredQuantities();
            });

            function loadBundleComponents(bundleProductId) {
                // Load bundle components via AJAX
                $.ajax({
                    url: "{{ route('main_transaction.production.get_bundle_components') }}",
                    type: 'GET',
                    data: {
                        bundle_product_id: bundleProductId
                    },
                    success: function(response) {
                        if (response.success) {
                            displayComponents(response.components);
                        } else {
                            displayComponents([]);
                        }
                    },
                    error: function() {

                    }
                });
            }

            function displayComponents(components) {
                console.log(components);
                let componentsHtml = '';
                components.forEach(component => {
                    const qtyPlanned = parseInt($('#quantity-planned').val()) || 0;
                    const totalRequired = component.quantity_required * qtyPlanned;
                    const isAvailable = totalRequired <= component.stock_available;

                    componentsHtml += `
                <tr>
                    <td>${component.product_name}</td>
                    <td class="text-center">${component.quantity_required} ${component.unit}</td>
                    <td class="text-center total-required" data-qty-per-bundle="${component.quantity_required}">${totalRequired} ${component.unit}</td>
                    <td class="text-center">${component.stock_available} ${component.unit}</td>
                    <td class="text-center">
                        ${isAvailable 
                            ? '<span class="badge bg-label-success">Available</span>'
                            : '<span class="badge bg-label-danger">Insufficient Stock</span>'
                        }
                    </td>
                </tr>
            `;
                });

                $('#components-rows').html(componentsHtml);
            }

            function updateTotalRequiredQuantities() {
                const qtyPlanned = parseInt($('#quantity-planned').val()) || 0;

                $('#components-rows .total-required').each(function() {
                    const qtyPerBundle = parseInt($(this).data('qty-per-bundle'));
                    const totalRequired = qtyPerBundle * qtyPlanned;

                    // Extract unit from current text
                    const currentText = $(this).text();
                    const unit = currentText.split(' ').pop();

                    $(this).text(totalRequired + ' ' + unit);

                    // Update status based on availability
                    const row = $(this).closest('tr');
                    const stockAvailable = parseInt(row.find('td').eq(3).text());
                    const statusCell = row.find('td').eq(4);

                    if (totalRequired <= stockAvailable) {
                        statusCell.html('<span class="badge bg-label-success">Available</span>');
                    } else {
                        statusCell.html('<span class="badge bg-label-danger">Insufficient Stock</span>');
                    }
                });
            }

            // Form submission
            $('#submit-btn').on('click', function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                const requiredFields = ['production-number', 'production-date', 'bundle-product',
                    'warehouse', 'quantity-planned', 'pic-user'
                ];

                requiredFields.forEach(function(fieldId) {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

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

                // TODO: Check component availability
                const hasInsufficientStock = $('#components-rows .badge.bg-label-danger').length > 0;
                if (hasInsufficientStock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock Tidak Cukup',
                        text: 'Beberapa komponen memiliki stock yang tidak mencukupi. Apakah Anda yakin ingin melanjutkan?',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        customClass: {
                            confirmButton: 'btn btn-warning',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitForm();
                        }
                    });
                    return;
                }

                Swal.fire({
                    title: 'Simpan Data?',
                    text: 'Cek kembali data yang sudah diisi!',
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
                        submitForm();
                    }
                });
            });

            function submitForm() {
                $('#submit-btn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
                );

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('production_number', $('#production-number').val());
                formData.append('production_date', $('#production-date').val());
                formData.append('bundle_product_id', $('#bundle-product').val());
                formData.append('warehouse_id', $('#warehouse').val());
                formData.append('quantity_planned', $('#quantity-planned').val());
                formData.append('pic_id', $('#pic-user').val());
                formData.append('description', $('#description').val());
                formData.append('memo', $('#production-memo').val());

                // Handle file attachment
                const attachmentFile = $('#attachment')[0].files[0];
                if (attachmentFile) {
                    formData.append('attachment', attachmentFile);
                }

                $.ajax({
                    url: "{{ route('main_transaction.production.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                window.location.href =
                                    "{{ route('main_transaction.production.index') }}";
                            });
                        }
                    },
                    error: function(xhr) {
                        let message = 'Terjadi kesalahan saat menyimpan data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: message,
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    },
                    complete: function() {
                        $('#submit-btn').prop('disabled', false).html('Simpan');
                    }
                });
            }
        });
    </script>
@endsection
