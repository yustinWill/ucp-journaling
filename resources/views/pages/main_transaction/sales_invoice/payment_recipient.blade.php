@extends('layouts.default')
@section('page_title')
    Transaksi Penerimaan Pembayaran
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Transaksi Penerimaan Pembayaran</h4>
        <form class="card-body" id="payment-receipt-form" autocomplete="off" enctype="multipart/form-data">
            @csrf

            <!-- Informasi Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Pelanggan</label>
                    <select class="global-select2 form-select" id="sales-order-customer" name="customer_id"
                        data-allow-clear="false" required>
                        <option value="">Pilih Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" data-email="{{ $customer->email }}"
                                data-billing-address="{{ $customer->billing_address }}">{{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Setor Ke</label>
                    <select class="global-select2 form-select" id="sales-order-account" name="account_id"
                        data-allow-clear="false" required>
                        <option value="">Pilih Akun</option>
                        @foreach ($coa_accounts as $account)
                            <option value="{{ $account->code }}">
                                {{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Cara Pembayaran</label>
                    <select class="global-select2 form-select" id="sales-order-payment_method" name="payment_method"
                        data-allow-clear="false" required>
                        <option value="">Kas atau Tunai</option>
                        <option value="">Transfer Bank</option>
                        <option value="">Dll</option>
                        
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pembayaran</label>
                    <input type="text" class="form-control" value="{{ now()->format('d/m/Y') }}"  />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <select class="global-select2 form-select" id="tag_codes" name="tag_codes[]" multiple data-allow-clear="false">
                        @foreach($availableTags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->code }} - {{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="transaction_number" name="transaction_number"
                            placeholder="Auto-generated but editable" required />
                        <button type="button" class="btn btn-outline-secondary" id="refresh-transaction-number">
                            <i class="icon-base ti tabler-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>

            <hr class="my-6 mx-n6" />

            <!-- Items Section -->
            <div class="section-header mt-12">
                <h6>Items</h6>
                <hr class="mx-n6">
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Deskripsi</th>
                            <th>Tgl Jatuh Tempo</th>
                            <th>Total</th>
                            <th>Sisa Tagihan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$invoice_data->transaction_code}}</td>
                            <td>{{$invoice_data->description}}</td>
                            <td>{{$invoice_data->invoice_date}}</td>
                            <td>Rp {{ number_format($invoice_data->subtotal ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($invoice_data->subtotal ?? 0, 0, ',', '.') }}</td>
                            <td>
                                <input type="text" class="form-control" placeholder="3,800.00" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Memo</label>
                    <textarea class="form-control" rows="3" placeholder="Masukkan Jumlah Pemotongan"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total</label>
                    <input type="text" class="form-control fw-bold text-primary" value="Rp {{ number_format($invoice_data->grand_total ?? 0, 0, ',', '.') }}" disabled />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Lampiran</label>
                    <input type="file" class="form-control" />
                    <small class="text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip</small>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger btn-md waves-effect waves-light">Batalkan</button>
                <button type="submit" class="btn btn-success btn-md waves-effect waves-light">Buat Penerimaan</button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Add any JavaScript logic here if needed
            function generateTransactionNumber() {
                $.ajax({
                    url: '{{ route("master_transaction.sales_invoice.generate_transaction_number") }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#transaction_number').val(response.transaction_number);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to generate transaction number:', xhr);
                    }
                });
            }

            // Auto-generate on page load
            generateTransactionNumber();
        });
    </script>
@endsection
