@extends('layouts.default')
@section('page_title')
    Informasi Kontak - {{ $contact->company_name }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Informasi Kontak</h4>
                </div>

                <!-- Action Panel -->
                <!-- <div class="col-md-4">
                                    <div class="text-end">
                                        <div class="btn-group-vertical" role="group">
                                            <button type="button" class="btn btn-primary mb-2">
                                                <i class="icon-base ti tabler-settings me-1"></i>
                                                Kelola Kontak
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary mb-2">
                                                <i class="icon-base ti tabler-git-merge me-1"></i>
                                                Gabungkan Kontak
                                            </button>
                                            <button type="button" class="btn btn-outline-danger">
                                                <i class="icon-base ti tabler-archive me-1"></i>
                                                Arsipkan Kontak
                                            </button>
                                        </div>
                                    </div>
                                </div> -->
            </div>

            <!-- Contact Title -->
            <h2>{{ $contact->company_name }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Tipe Kontak</label>
                    <div class="mt-2">
                        @if($contact->is_customer)
                            <span class="badge bg-label-primary me-1">Customer</span>
                        @endif
                        @if($contact->is_supplier)
                            <span class="badge bg-label-success me-1">Supplier</span>
                        @endif
                        @if($contact->is_employee)
                            <span class="badge bg-label-info me-1">Karyawan</span>
                        @endif
                        @if($contact->is_other)
                            <span class="badge bg-label-secondary me-1">Lainnya</span>
                        @endif
                        @if(!$contact->is_customer && !$contact->is_supplier && !$contact->is_employee && !$contact->is_other)
                            <span class="badge bg-label-warning">Tidak ada tipe</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Grup Kontak</label>
                    <input type="text" class="form-control" value="{{ $contact->group_contact_name }}"
                        placeholder="Belum ada grup" disabled />
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="profil-list-item"
                            data-bs-toggle="list" href="#horizontal-profil" aria-selected="true" role="tab"
                            tabindex="-1">
                            <i class="icon-base ti tabler-user me-2"></i>
                            <b>Profil</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="transaksi-list-item"
                            data-bs-toggle="list" href="#horizontal-transaksi" aria-selected="false" role="tab"
                            tabindex="-1">
                            <i class="icon-base ti tabler-receipt-2 me-2"></i>
                            <b>Transaksi</b>
                        </a>
                        {{-- <a class="list-group-item list-group-item-action waves-effect" id="credit-memo-list-item"
                            data-bs-toggle="list" href="#horizontal-credit-memo" aria-selected="false" role="tab"
                            tabindex="-1">
                            <i class="icon-base ti tabler-file-dollar me-2"></i>
                            <b>Credit Memo</b>
                        </a> --}}
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab"
                            tabindex="-1">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <div class="tab-pane fade show active" id="horizontal-profil" role="tabpanel"
                            aria-labelledby="profil-list-item">
                            <div id="profil-content">
                                <!-- Content will be loaded here by default -->
                            </div>
                        </div>
                        <div class="tab-pane fade" id="horizontal-transaksi" role="tabpanel"
                            aria-labelledby="transaksi-list-item">
                            <div id="transaksi-content">
                                <!-- Content will be loaded dynamically -->
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="tab-pane fade" id="horizontal-credit-memo" role="tabpanel"
                            aria-labelledby="credit-memo-list-item">
                            <div id="credit-memo-content">
                                <!-- Content will be loaded dynamically -->
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel"
                            aria-labelledby="audit-log-list-item">
                            <div class="section-header mt-6">
                                <h6>Log Audit Kontak</h6>
                            </div>
                            <div class="mx-n6">
                                <table id="audit-log-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal & Waktu</th>
                                            <th>Pengguna</th>
                                            <th>Aksi</th>
                                            <th>Deskripsi</th>
                                            <th>Lihat Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($audit_log_data))
                                            @foreach ($audit_log_data as $log)
                                                <tr>
                                                    <td>{{ $log->created_at }}</td>
                                                    <td><a href="{{ route('master_data.user.detail', ['id' => $log->user_id]) }}"
                                                            class="text-primary fw-semibold">{{ $log->user_name }}</a></td>
                                                    <td><span
                                                            class="badge bg-label-{{ $log->action_type === 'CREATE' ? 'success' : ($log->action_type === 'UPDATE' ? 'warning' : 'info') }}">{{ $log->action_type }}</span>
                                                    </td>
                                                    <td>{{ $log->description }}</td>
                                                    <td>
                                                        @if ($log->action_type == 'UPDATE')
                                                            <a target="_blank"
                                                                href="{{ route('audit_log.detail', ['id' => $log->id, 'audit_log_type' => $auditLogType]) }}"
                                                                class="btn btn-sm btn-primary waves-effect waves-light"
                                                                title="Detail">
                                                                <i class="icon-xs icon-base ti tabler-eye"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
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
            const contactId = {{ $contact->id }};

            const contactBank = JSON.parse('{!! json_encode($contact_bank) !!}');
            // Contact data from server
            const contactData = {
                contact_name: "{{ $contact->name }}",
                company_name: "{{ $contact->company_name }}",
                email: "{{ $contact->email }}",
                phone: "{{ $contact->phone }}",
                landline: "{{ $contact->phone }}",
                fax: "{{ $contact->company_fax_no }}",
                npwp: "{{ $contact->company_npwp }}",
                identity_type: "{{ $contact->f_identity_type_code }}",
                identity_number: "{{ $contact->identity_number }}",
                billing_address: "{{ $billing_contact_address->address }} {{ $billing_contact_address->subdistrict }} {{ $billing_contact_address->district }} {{ $billing_contact_address->city }} {{ $billing_contact_address->province }} {{ $billing_contact_address->postal_code }}",
                shipping_address: "{{ $shipping_contact_address->address }} {{ $shipping_contact_address->subdistrict }} {{ $shipping_contact_address->district }} {{ $shipping_contact_address->city }} {{ $shipping_contact_address->province }} {{ $shipping_contact_address->postal_code }}",
                other_info: "a",
                debt_account: "{{ $contact->f_coa_account_payable }} - {{ $contact->coa_account_payable_name }}",
                receivable_account: "{{ $contact->f_coa_account_receivable }} - {{ $contact->coa_account_receivable_name }}",
                payment_terms: "{{ $contact->f_payment_terms_id }} - {{ $contact->payment_terms_name }}",
                shipping_area: "{{ $shipping_contact_address->area }}",
                shipping_maps_url: "{{ $shipping_contact_address->maps_url }}",
                billing_area: "{{ $billing_contact_address->area }}",
                billing_maps_url: "{{ $billing_contact_address->maps_url }}",
            };

            // Load profile content immediately
            loadProfilContent();

            // Tab switch event handlers
            $('#transaksi-list-item').on('shown.bs.tab', function() {
                loadTransaksiContent();
            });

            $('#credit-memo-list-item').on('shown.bs.tab', function() {
                loadCreditMemoContent();
            });

            function loadProfilContent() {
                let profilContent = `
            <h6>1. Data Kontak</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Nama Kontak</label>
                    <input type="text" class="form-control" value="${contactData.contact_name}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Perusahaan</label>
                    <input type="text" class="form-control" value="${contactData.company_name}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" value="${contactData.email}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Telepon</label>
                    <input type="text" class="form-control" value="${contactData.phone}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>2. Data Umum</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Jenis Identitas</label>
                    <input type="text" class="form-control" value="${contactData.identity_type}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Identitas</label>
                    <input type="text" class="form-control" value="${contactData.identity_number}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Telepon Perusahaan</label>
                    <input type="text" class="form-control" value="${contactData.landline}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Fax</label>
                    <input type="text" class="form-control" value="${contactData.fax}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>3. Data Perusahaan</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">No NPWP</label>
                    <input type="text" class="form-control" value="${contactData.npwp}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Info Lainnya</label>
                    <input type="text" class="form-control" value="${contactData.other_info}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>4. Alamat Penagihan</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" disabled>${contactData.billing_address}</textarea>
                </div>
            </div>
             <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Area</label>
                    <textarea class="form-control" disabled>${contactData.billing_area}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Maps URL</label>
                    <textarea class="form-control" disabled>${contactData.billing_maps_url}</textarea>
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>5. Alamat Pengiriman</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" disabled>${contactData.shipping_address}</textarea>
                </div>
            </div>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Area</label>
                    <textarea class="form-control" disabled>${contactData.shipping_area}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Maps URL</label>
                    <textarea class="form-control" disabled>${contactData.shipping_maps_url}</textarea>
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>6. Pemetaan Akun</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Akun Hutang</label>
                    <input type="text" class="form-control" value="${contactData.debt_account}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Akun Piutang</label>
                    <input type="text" class="form-control" value="${contactData.receivable_account}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>7. Info Bank</h6>
        `;
                for (const bank of contactBank) {
                    profilContent += `
                        <div class="row g-6">
                            <div class="col-md-6">
                                <label class="form-label">Nama Bank</label>
                                <input type="text" class="form-control" value="${bank.bank_name}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kantor Cabang</label>
                                <input type="text" class="form-control" value="${bank.branch_office}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pemegang Akun Bank</label>
                                <input type="text" class="form-control" value="${bank.account_holder}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" class="form-control" value="${bank.account_number}" disabled />
                            </div>
                        </div>
                        <hr>
                    `;
                }

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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>List History Transaksi</h5>
                    <button type="button" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus"></i>
                        <span class="align-middle">Tambah Faktur Penjualan</span>
                    </button>
                </div>
                
                <div class="mx-n6">
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
                    $('#transaksi-content').html(
                        '<div class="alert alert-danger">Error loading transaction data</div>');
                });
            }

            function loadCreditMemoContent() {
                $.get(`/master_data/contact/detail_credit_memo_data/${contactId}`, function(data) {
                    const creditMemoUrl =
                        "{{ route('main_transaction.credit_memo.add_with_contact', ':id') }}".replace(
                            ':id', contactId);
                    const creditMemoContent = `
                <!-- Add Credit Memo Button -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>List History Credit Memo</h5>
                    <a href="${creditMemoUrl}" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus"></i>
                        <span class="align-middle">Tambah Credit Memo</span>
                    </a>
                </div>
                
                <!-- Credit Memo Table -->
                
                <div class="mx-n6">
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
                    $('#credit-memo-content').html(
                        '<div class="alert alert-danger">Error loading credit memo data</div>');
                });
            }

            // Initialize DataTable for audit log
            $('#audit-log-table').DataTable({
                responsive: true,
                orderCellsTop: true,
                searching: false,
                paging: false,
                info: false,
                order: [
                    [0, 'desc']
                ] // Sort by date descending
            });
        });
    </script>
@endsection
