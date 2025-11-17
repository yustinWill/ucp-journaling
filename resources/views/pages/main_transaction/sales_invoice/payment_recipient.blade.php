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
                    <input type="text" class="form-control" hidden id="sales-invoice-id" name="sales_invoice_id" value="{{ $invoice_data->id }}"  />
                    <input type="text" class="form-control" hidden id="sales-order-customer" name="customer_id" value="{{ $invoice_data->customer_id }}"  />
                    <input type="text" class="form-control"  name="customer_name" value="{{ $invoice_data->customer_name }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Setor Ke</label>
                    <select class="global-select2 form-select" id="sales-order-account" name="account_code"
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
                        <option value="">Pilih Cara Pembayaran</option>
                        @foreach ($payment_method as $row)
                            <option value="{{ $row["value"]; }}">{{ $row["label"]; }}</option>
                        @endforeach
                        
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pembayaran</label>
                    <input type="text" class="form-control" name="payment_date" value="{{ now()->format('Y-m-d') }}"  />
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
                        <input type="text" class="form-control" id="transaction_number" name="reference_number"
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
                            <td>Rp {{ number_format($invoice_data->remaining_payment ?? 0, 0, ',', '.') }}</td>
                            <td>
                                <input type="text" class="form-control" id="remaining_payment" name = "remaining_payment" value="{{ $invoice_data->remaining_payment; }}" hidden/>
                                <input type="text" class="form-control" id="payment_value" name = "payment_value" placeholder="3,800.00" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Memo</label>
                    <textarea class="form-control" rows="3" placeholder="Masukkan Memo"></textarea>
                </div>
                <div class="col-md-8">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Masukkan Jumlah Pemotongan<span style="color: red;">*</span></label>
                        </div>
                        <div class="col-md-4">
                            <select class="global-select2 form-select" id="deduction-account" name="deduction_account_code" data-allow-clear="false" required>
                                <option value="">Pilih Akun</option>
                                @foreach ($coa_accounts as $account)
                                    <option value="{{ $account->code }}">
                                        {{ $account->code }} - {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name = "deduction_value" id = "deduction_value" placeholder="3,800.00" />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Total</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control fw-bold text-primary" id="total_value" name="total_value" value="Rp {{ number_format(0, 0, ',', '.') }}" disabled />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Lampiran</label>
                    <input type="file" class="form-control" name="attachment" />
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

            function unformatNumber(value) {
                if (!value) return 0;
                return parseFloat(value.replace(/[^0-9.-]/g, "")) || 0;
            }

            function formatRupiah(value) {
                return "Rp " + value.toLocaleString('id-ID', { minimumFractionDigits: 0 });
            }

            function updateTotalValue() {
                const paymentValue = unformatNumber($('#payment_value').val());
                const deductionValue = unformatNumber($('#deduction_value').val());

                const total = paymentValue - deductionValue;

                $('#total_value').val(formatRupiah(total));
            }

            // Listen to typing in both fields
            $('#payment_value, #deduction_value').on('keyup change input', function () {
                updateTotalValue();
            });
            // Auto-generate on page load
            generateTransactionNumber();

            function submitPayment() {

            // Validate total amount
            // const totalAmount = parseFloat($('#total_invoice_amount').text().replace(/[^\d]/g, '')) || 0;
            // if (totalAmount === 0) {
            //     Swal.fire({
            //         title: 'Error!',
            //         text: 'Total invoice harus lebih dari 0',
            //         icon: 'error'
            //     });
            //     return;
            // }

            const formData = new FormData(document.getElementById('payment-receipt-form'));

                // Add additional data
                // formData.append('total_amount', totalAmount);
                // formData.append('sales_order_id', $('#sales_order_id').val());
                // formData.append('due_date', $('#due_date').val());
                // formData.append('total_items', $('#total_items_invoice').text().split(' ')[0]);

                // $('.invoice-qty').each(function() {
                //     const name = $(this).attr('name');
                //     const rawValue = $(this).val();
                //     const parsedValue = parseCurrency(rawValue);
                //     if (name) {
                //         formData.set(name, parsedValue); // overwrite with parsed numeric value
                //     }
                // });
                const paymentValue = parseFloat($('#payment_value').val().replace(/[^0-9.-]/g, '')) || 0;
                const remainingValue = parseFloat($('#remaining_payment').val().replace(/[^0-9.-]/g, '')) || 0; 
                if (paymentValue > remainingValue) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Payment value cannot be more than remaining payment.',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }else{
                    Swal.fire({
                        title: 'Buat Sales Invoice?',
                        text: 'Pastikan data yang diisi sudah benar.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Buat!',
                        cancelButtonText: 'Batal',
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-label-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            $('#submit-btn').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
                            );
                            $.ajax({
                                url: "{{ route('main_transaction.sales_invoice.payment_save') }}",
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        showConfirmButton: true
                                    }).then((result) => {
                                        window.location.href =
                                            "{{ route('main_transaction.sales_invoice.index') }}";
                                    });
                                },
                                error: function(xhr) {
                                    let errorMessage =
                                        'Terjadi kesalahan saat menyimpan data.';

                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }

                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: errorMessage,
                                        icon: 'error',
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
                }
            }
            // Form submission
            $('#payment-receipt-form').on('submit', function(e) {
                e.preventDefault();
                submitPayment();
            });
        });
    </script>
@endsection
