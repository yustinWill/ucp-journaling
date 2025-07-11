@extends('layouts.default')
@section('page_title')
    Informasi Kontak - {{ $contact['company_name'] }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Informasi Kontak</h4>
                    
                    <!-- Tab Navigation -->
                    <div class="nav nav-tabs contact-tabs" id="contactTabs" role="tablist">
                        <button class="nav-link active" id="profil-tab" data-bs-toggle="tab" data-bs-target="#profil" type="button" role="tab" aria-controls="profil" aria-selected="true">
                            Profil
                        </button>
                        <button class="nav-link" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi" type="button" role="tab" aria-controls="transaksi" aria-selected="false">
                            Transaksi
                        </button>
                        <button class="nav-link" id="credit-memo-tab" data-bs-toggle="tab" data-bs-target="#credit-memo" type="button" role="tab" aria-controls="credit-memo" aria-selected="false">
                            Credit Memo
                        </button>
                    </div>
                </div>
                
                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="btn-group-vertical" role="group">
                            <button type="button" class="btn btn-primary mb-2">
                                <i class="ti ti-settings me-1"></i>
                                Kelola Kontak
                            </button>
                            <button type="button" class="btn btn-outline-secondary mb-2">
                                <i class="ti ti-git-merge me-1"></i>
                                Gabungkan Kontak
                            </button>
                            <button type="button" class="btn btn-outline-danger">
                                <i class="ti ti-archive me-1"></i>
                                Arsipkan Kontak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Title -->
            <div class="mb-4">
                <h2 class="fw-bold">{{ $contact['company_name'] }}</h2>
            </div>
            
            <!-- Contact Type & Group -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Tipe Kontak:</label>
                    <div class="mt-1">
                        <span class="badge bg-warning text-dark">{{ $contact['type'] }}</span>
                    </div>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Grup:</label>
                    <input type="text" class="form-control" value="{{ $contact['group'] }}" placeholder="Belum ada grup" readonly>
                </div>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content" id="contactTabContent">
                <!-- Profil Tab -->
                <div class="tab-pane fade show active" id="profil" role="tabpanel" aria-labelledby="profil-tab">
                    <div id="profil-content">
                        <!-- Content will be loaded here by default -->
                    </div>
                </div>
                
                <!-- Transaksi Tab -->
                <div class="tab-pane fade" id="transaksi" role="tabpanel" aria-labelledby="transaksi-tab">
                    <div id="transaksi-content">
                        <!-- Content will be loaded dynamically -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Credit Memo Tab -->
                <div class="tab-pane fade" id="credit-memo" role="tabpanel" aria-labelledby="credit-memo-tab">
                    <div id="credit-memo-content">
                        <!-- Content will be loaded dynamically -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css_script')
<style>
    .contact-tabs .nav-link.active {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }
    
    .contact-tabs .nav-link {
        background-color: #e9ecef;
        border-color: #e9ecef;
        color: #495057;
        margin-right: 2px;
    }
    
    .contact-tabs .nav-link:hover:not(.active) {
        background-color: #dee2e6;
        border-color: #dee2e6;
    }
    
    .readonly-field {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #6c757d;
    }
    
    .section-header h5 {
        margin-bottom: 0;
        font-weight: 600;
    }
    
    .summary-card {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        text-align: center;
    }
    
    .summary-card.danger {
        background-color: #dc3545;
        color: white;
    }
    
    .summary-card.success {
        background-color: #28a745;
        color: white;
    }
    
    .selected-row {
        background-color: #007bff !important;
        color: white;
    }
</style>
@endsection

@section('js_script')
<script>
$(function() {
    const contactId = {{ $contact['id'] }};
    
    // Contact data from server
    const contactData = {
        contact_name: "{{ $contact['contact_name'] }}",
        company_name: "{{ $contact['company_name'] }}",
        email: "{{ $contact['email'] }}",
        phone: "{{ $contact['phone'] }}",
        landline: "{{ $contact['landline'] }}",
        fax: "{{ $contact['fax'] }}",
        npwp: "{{ $contact['npwp'] }}",
        identity_type: "{{ $contact['identity_type'] }}",
        identity_number: "{{ $contact['identity_number'] }}",
        billing_address: "{{ $contact['billing_address'] }}",
        shipping_address: "{{ $contact['shipping_address'] }}",
        other_info: "{{ $contact['other_info'] }}",
        bank_name: "{{ $contact['bank_name'] }}",
        account_holder: "{{ $contact['account_holder'] }}",
        bank_branch: "{{ $contact['bank_branch'] }}",
        account_number: "{{ $contact['account_number'] }}",
        debt_account: "{{ $contact['debt_account'] }}",
        receivable_account: "{{ $contact['receivable_account'] }}",
        payment_terms: "{{ $contact['payment_terms'] }}"
    };
    
    // Load profile content immediately
    loadProfilContent();
    
    // Tab switch event handlers
    $('#transaksi-tab').on('shown.bs.tab', function () {
        loadTransaksiContent();
    });
    
    $('#credit-memo-tab').on('shown.bs.tab', function () {
        loadCreditMemoContent();
    });
    
    function loadProfilContent() {
        const profilContent = `
            <!-- Info Umum Section -->
            <div class="section-header mt-12">
                <h5>Info Umum</h5>
                <hr>
            </div>
            
            <div class="row g-3 mb-4">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Kontak</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.contact_name}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Perusahaan</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.company_name}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control readonly-field" value="${contactData.email}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Handphone</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.phone}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.landline}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fax</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.fax}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NPWP</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.npwp}" readonly>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Identitas</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.identity_type}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Identitas</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.identity_number}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Penagihan</label>
                        <textarea class="form-control readonly-field" rows="3" readonly>${contactData.billing_address}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Pengiriman</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.shipping_address}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Info Lainnya</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.other_info}" readonly>
                    </div>
                </div>
            </div>
            
            <!-- Info Bank Section -->
            <div class="section-header mt-12">
                <h5>Info Bank</h5>
                <hr>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Bank</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.bank_name}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pemegang Akun Bank</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.account_holder}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kantor Cabang</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.bank_branch}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.account_number}" readonly>
                    </div>
                </div>
            </div>
            
            <!-- Pemetaan Akun Section -->
            <div class="section-header mt-12">
                <h5>Pemetaan Akun</h5>
                <hr>
            </div>
            
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Akun Hutang</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.debt_account}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Akun Piutang</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.receivable_account}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Syarat Pembayaran Utama</label>
                        <input type="text" class="form-control readonly-field" value="${contactData.payment_terms}" readonly>
                    </div>
                </div>
            </div>
        `;
        
        $('#profil-content').html(profilContent);
    }
    
    function loadTransaksiContent() {
        $.get(`/master_data/contact/detail_transaction_data/${contactId}`, function(data) {
            const transaksiContent = `
                <!-- Transaction Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="summary-card danger">
                            <h6>Transaksi Bulan Ditagih</h6>
                            <h4>${data.summary.monthly_invoiced}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-card danger">
                            <h6>Total</h6>
                            <h4>Rp ${data.summary.total_amount}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-card success">
                            <h6>Piutang Pengiriman</h6>
                            <h4>Rp ${data.summary.delivery_receivables}</h4>
                        </div>
                    </div>
                </div>
                
                <!-- Transaction Table -->
                <div class="row mb-3">
                    <div class="col-md-10">
                        <h5>List History Transaksi</h5>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-primary btn-sm">
                            Tambah Faktur Penjualan
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Tanggal Transaksi</th>
                                <th style="width: 25%;">No. Transaksi</th>
                                <th style="width: 20%;">Tanggal Jatuh Tempo</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 20%;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.transactions.map(transaction => `
                                <tr>
                                    <td>${transaction.date}</td>
                                    <td><a href="#" class="text-primary">${transaction.transaction_number}</a></td>
                                    <td>${transaction.due_date}</td>
                                    <td>
                                        <span class="badge ${transaction.status === 'Paid' ? 'bg-success' : 'bg-warning'}">${transaction.status}</span>
                                    </td>
                                    <td>${transaction.total}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">Displaying ${data.transactions.length} of ${data.transactions.length} entries</small>
                </div>
            `;
            
            $('#transaksi-content').html(transaksiContent);
        }).fail(function() {
            $('#transaksi-content').html('<div class="alert alert-danger">Error loading transaction data</div>');
        });
    }
    
    function loadCreditMemoContent() {
        $.get(`/master_data/contact/detail_credit_memo_data/${contactId}`, function(data) {
            const creditMemoUrl = "{{ route('main_transaction.credit_memo.add_with_contact', ':id') }}".replace(':id', contactId);
            const creditMemoContent = `
                <!-- Add Credit Memo Button -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Tambah Credit Memo:</label>
                        <a href="${creditMemoUrl}" class="btn btn-primary">
                            Tambah Credit Memo
                        </a>
                    </div>
                </div>
                
                <!-- Credit Memo Table -->
                <div class="mb-3">
                    <h5>List History Credit Memo</h5>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Tanggal Transaksi</th>
                                <th style="width: 25%;">No. Credit Memo</th>
                                <th style="width: 20%;">Jumlah Awal</th>
                                <th style="width: 15%;">Terpakai</th>
                                <th style="width: 20%;">Sisa Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.credit_memos.map(memo => `
                                <tr ${memo.selected ? 'class="selected-row"' : ''}>
                                    <td>${memo.date}</td>
                                    <td><a href="#" class="text-primary ${memo.selected ? 'text-white' : ''}">${memo.credit_memo_number}</a></td>
                                    <td>${memo.original_amount}</td>
                                    <td>${memo.used_amount}</td>
                                    <td>${memo.remaining_balance}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">Showing ${data.credit_memos.length} of ${data.credit_memos.length} entries</small>
                </div>
            `;
            
            $('#credit-memo-content').html(creditMemoContent);
        }).fail(function() {
            $('#credit-memo-content').html('<div class="alert alert-danger">Error loading credit memo data</div>');
        });
    }
});
</script>
@endsection