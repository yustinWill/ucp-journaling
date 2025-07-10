@extends('layouts.default')
@section('page_title')
    Buat Pengiriman Pembelian
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Buat Pengiriman Pembelian</h5>
        <form class="card-body" id="purchaseDeliveryForm">
            <!-- Transaction Information Section -->
            <div class="section-header mt-12">
                <h5>Informasi Transaksi</h5>
                <hr>
            </div>
            
            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Supplier <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="supplier-select" data-allow-clear="true">
                        <option value="">Pilih Supplier</option>
                        <option value="1">John</option>
                        <option value="2">PT. Sumber Makmur</option>
                        <option value="3">CV. Berkah Jaya</option>
                        <option value="4">PT. Mandiri Sejahtera</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span style="color: red;">*</span></label>
                    <input type="email" class="form-control" id="supplier-email" placeholder="email@supplier.com" />
                </div>
            </div>
            
            <!-- Row 2: Delivery Details -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pengiriman <span style="color: red;">*</span></label>
                    <input type="date" class="form-control" id="delivery-date" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="delivery-number" value="PD-2025-001" placeholder="autofill but editable" />
                </div>
            </div>
            
            <!-- Row 3: Reference Information -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nomor Referensi Pembelian</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="po-reference" placeholder="Kerjasama Utama 11008" />
                        <button class="btn btn-outline-secondary" type="button" id="po-lookup-btn">
                            <i class="ti ti-link"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor Referensi Supplier</label>
                    <input type="text" class="form-control" id="supplier-reference" placeholder="Referensi dari supplier" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tag</label>
                    <input type="text" class="form-control" id="tag" placeholder="Kategori pengiriman" />
                </div>
            </div>
            
            <!-- Row 4: Storage Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <select class="form-select" id="warehouse-select">
                        <option value="">Pilih Gudang</option>
                        <option value="1">Gudang Utama</option>
                        <option value="2">Gudang Cabang A</option>
                        <option value="3">Gudang Cabang B</option>
                    </select>
                </div>
            </div>
            
            <!-- Product Information Section -->
            <div class="section-header mt-12">
                <h5>Informasi Barang yang Diterima</h5>
                <hr>
            </div>
            
            <!-- Product Table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="deliveryTable">
                    <thead>
                        <tr>
                            <th style="width: 23%;">Nama Produk <span style="color: red;">*</span></th>
                            <th style="width: 37%;">Deskripsi</th>
                            <th style="width: 15%;">Kuantitas <span style="color: red;">*</span></th>
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
                                <input type="number" class="form-control qty-received" value="1" min="1" required>
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
                <div class="col-md-12">
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
            
            // Supplier selection change handler
            $('#supplier-select').change(function() {
                const selectedSupplier = $(this).val();
                if (selectedSupplier) {
                    // TODO: Load supplier details and populate email
                    console.log('Loading supplier details for:', selectedSupplier);
                }
            });
            
            // PO lookup button handler
            $('#po-lookup-btn').click(function() {
                // TODO: Open PO lookup modal and populate form
                console.log('Opening PO lookup modal');
            });
            
            // Calculate totals when quantity received changes
            $(document).on('input', '.qty-received', function() {
                // TODO: Calculate delivery totals
                console.log('Quantity received changed');
            });
            
            
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
            
            // Remove row function
            window.removeRow = function(button) {
                $(button).closest('tr').remove();
            };
        });
        
        // Add Cleave.js script import
        if (!document.querySelector('script[src*="cleave"]')) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js';
            document.head.appendChild(script);
        }
    </script>
@endsection