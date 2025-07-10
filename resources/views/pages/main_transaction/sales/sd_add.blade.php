@extends('layouts.default')
@section('page_title')
    Buat Pengiriman Penjualan
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Buat Pengiriman Penjualan</h5>
        <form class="card-body" id="salesDeliveryForm">
            <!-- Transaction Information Section -->
            <div class="section-header mt-12">
                <h5>Informasi Transaksi</h5>
                <hr>
            </div>
            
            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Pelanggan <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="customer-select" data-allow-clear="true">
                        <option value="">Pilih Pelanggan</option>
                        <option value="1">John</option>
                        <option value="2">PT. Cahaya Abadi</option>
                        <option value="3">CV. Maju Jaya</option>
                        <option value="4">PT. Sukses Mandiri</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span style="color: red;">*</span></label>
                    <input type="email" class="form-control" id="customer-email" placeholder="email@pelanggan.com" />
                </div>
            </div>
            
            <!-- Row 2: Delivery Details -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Pengiriman <span style="color: red;">*</span></label>
                    <input type="date" class="form-control" id="delivery-date" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="transaction-number" value="SD-2025-001" placeholder="autofill but editable" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor Referensi Penjualan</label>
                    <input type="text" class="form-control" id="so-reference" value="Sales Order 110001" placeholder="Sales Order 110001" />
                </div>
            </div>
            
            <!-- Row 3: Reference Information -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nomor Referensi Pelanggan</label>
                    <input type="text" class="form-control" id="customer-reference" placeholder="Referensi dari pelanggan" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <input type="text" class="form-control" id="tag" placeholder="Kategori pengiriman" />
                </div>
            </div>
            
            <!-- Row 4: Warehouse -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <select class="form-select" id="warehouse">
                        <option value="">Pilih Gudang</option>
                        <option value="1">Gudang Utama</option>
                        <option value="2">Gudang Cabang A</option>
                        <option value="3">Gudang Cabang B</option>
                    </select>
                </div>
            </div>
            
            <!-- Product Information Section -->
            <div class="section-header mt-12">
                <h5>Informasi Barang yang Dikirim</h5>
                <hr>
            </div>
            
            <!-- Product Table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="deliveryTable">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Nama Produk</th>
                            <th style="width: 40%;">Deskripsi</th>
                            <th style="width: 15%;">Kuantitas</th>
                            <th style="width: 20%;">Unit</th>
                            <th style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="deliveryTableBody">
                        <tr>
                            <td>
                                <input type="text" class="form-control" value="Robot plastik 600 ml" required>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="mainan keramgelit">
                            </td>
                            <td>
                                <input type="number" class="form-control qty-delivered" value="1" min="1" required>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="pcs" readonly>
                            </td>
                            <td>
                                <button class="btn btn-label-danger" type="button" onclick="removeRow(this)">
                                    <i class="icon-base ti tabler-x me-1"></i>
                                    <span class="align-middle">Hapus</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Additional Information -->
            <div class="section-header mt-12">
                <h5>Informasi Tambahan</h5>
                <hr>
            </div>
            
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Pesan</label>
                        <textarea class="form-control" rows="4" id="delivery-message" placeholder="Catatan pengiriman..." maxlength="500"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Memo</label>
                        <textarea class="form-control" rows="4" id="internal-memo" placeholder="Memo internal..." maxlength="500"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="attachment" accept=".xlsx,.xls,.docx,.doc,.pdf,.png,.zip">
                        <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-primary" id="submit-btn">Buat</button>
            </div>
        </form>
    </div>
@endsection
@section('js_script')
    <script>
        $(function() {
            // Set default delivery date to today
            $('#delivery-date').val(new Date().toISOString().split('T')[0]);
            
            // Customer selection change handler
            $('#customer-select').change(function() {
                const selectedCustomer = $(this).val();
                if (selectedCustomer) {
                    // TODO: Load customer details and populate email
                    console.log('Loading customer details for:', selectedCustomer);
                }
            });
            
            // SO reference lookup functionality
            $('#so-reference').click(function() {
                // TODO: Open SO lookup modal and populate form
                console.log('Opening SO lookup modal');
            });
            
            // Calculate totals when quantity delivered changes
            $(document).on('input', '.qty-delivered', function() {
                // TODO: Calculate delivery totals
                console.log('Quantity delivered changed');
            });
            
            // Remove row function
            window.removeRow = function(button) {
                $(button).closest('tr').remove();
            };
            
            // Submit button handler
            $('#submit-btn').click(function(e) {
                e.preventDefault();
                
                // TODO: Add validation
                
                Swal.fire({
                    title: 'Simpan Data?',
                    text: "Cek kembali data yang sudah diisi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan Data!',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data berhasil disimpan.',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-label-secondary'
                            },
                        });
                    }
                });
            });
        });
        
        // Add Cleave.js script import
        if (!document.querySelector('script[src*="cleave"]')) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js';
            document.head.appendChild(script);
        }
    </script>
@endsection