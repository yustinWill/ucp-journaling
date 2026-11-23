@extends('layouts.default')
@section('page_title')
    Kas Besar - Tambah Topup
@stop
@section('content')
    <div class="card">
        <h5 class="card-header">Tambah Topup Kas Besar</h5>
        <div class="card-body">
            <form id="kasBesarForm" method="POST" autocomplete="off">
                @csrf

                <div class="section-header mt-4">
                    <h6>Informasi Transaksi</h6>
                    <hr class="mx-n6">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nomor Transaksi <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="text" id="transaction-number" name="transaction_number"
                                    class="form-control" placeholder="Auto-generated" readonly>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                    id="generate-number-btn" title="Generate Nomor Baru">
                                    <i class="icon-base ti tabler-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="transaction-date"
                            name="transaction_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        <small class="text-warning d-none" id="backdate-note">
                            Tanggal mundur: saldo semua transaksi sesudah tanggal ini ikut bertambah.
                        </small>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jumlah Topup <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control text-end" id="topup-amount-display"
                                placeholder="0" autocomplete="off">
                            <input type="hidden" id="topup-amount" name="topup_amount">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" id="saldo-label">Saldo Saat Ini</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control text-end bg-light" id="current-saldo"
                                readonly placeholder="Memuat...">
                        </div>
                        <small class="text-muted">Saldo setelah topup: <strong id="saldo-after-preview">-</strong></small>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"
                            placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('main_transaction.kas_besar.index') }}" class="btn btn-label-danger">
                        <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="icon-base ti tabler-device-floppy me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            let currentSaldo = 0;

            function fmt(num) {
                return parseInt(num || 0).toLocaleString('id-ID');
            }

            const today = '{{ date('Y-m-d') }}';

            function fmtDate(date) {
                return new Date(date + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            }

            // The number and saldo depend on the date: a backdated top-up continues that month's
            // numbers and starts from the saldo on that day.
            function loadForDate() {
                const date = $('#transaction-date').val() || today;
                $('#saldo-label').text(date === today ? 'Saldo Saat Ini' : `Saldo per ${fmtDate(date)}`);
                $('#backdate-note').toggleClass('d-none', date >= today);
                $('#current-saldo').val('').attr('placeholder', 'Memuat...');
                $.get('{{ route('main_transaction.kas_besar.get_latest_saldo') }}', { date: date }, function(res) {
                    if (res.success && date === ($('#transaction-date').val() || today)) {
                        currentSaldo = parseFloat(res.saldo_after) || 0;
                        $('#current-saldo').val(fmt(currentSaldo));
                        updateSaldoPreview();
                    }
                });
                generateNumber();
            }

            function generateNumber() {
                const date = $('#transaction-date').val() || today;
                $.get('{{ route('main_transaction.kas_besar.generate_transaction_number') }}', { date: date }, function(res) {
                    if (res.success) {
                        $('#transaction-number').val(res.transaction_number);
                    }
                }).fail(function(xhr) {
                    $('#transaction-number').val('');
                    Swal.fire({ icon: 'error', title: 'Nomor transaksi', text: xhr.responseJSON?.message || 'Nomor transaksi gagal dibuat.' });
                });
            }

            loadForDate();
            $('#transaction-date').on('change', loadForDate);
            $('#generate-number-btn').on('click', generateNumber);

            // Topup amount input formatting
            $('#topup-amount-display').on('input', function() {
                let raw = $(this).val().replace(/[^0-9]/g, '');
                if (raw) {
                    $(this).val(parseInt(raw).toLocaleString('id-ID'));
                    $('#topup-amount').val(raw);
                } else {
                    $(this).val('');
                    $('#topup-amount').val('');
                }
                updateSaldoPreview();
            });

            function updateSaldoPreview() {
                const topup = parseInt($('#topup-amount').val() || 0);
                if (topup > 0) {
                    const after = currentSaldo + topup;
                    $('#saldo-after-preview').text('Rp ' + fmt(after)).removeClass('text-danger').addClass('text-success');
                } else {
                    $('#saldo-after-preview').text('-').removeClass('text-success text-danger');
                }
            }

            // Form submit
            $('#kasBesarForm').on('submit', function(e) {
                e.preventDefault();

                const topupAmount = parseInt($('#topup-amount').val() || 0);
                if (!topupAmount || topupAmount <= 0) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Jumlah topup harus lebih dari 0.' });
                    return;
                }
                if (!$('#transaction-number').val()) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Nomor transaksi wajib diisi.' });
                    return;
                }
                const date = $('#transaction-date').val();
                if (!date || date > today) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tanggal transaksi tidak boleh kosong atau lebih dari hari ini.' });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Topup',
                    html: `<div class="text-start">
                        <p><strong>Nomor:</strong> ${$('#transaction-number').val()}</p>
                        <p><strong>Tanggal:</strong> ${fmtDate(date)}</p>
                        <p><strong>Jumlah Topup:</strong> Rp ${fmt(topupAmount)}</p>
                        <p><strong>Saldo Setelah:</strong> Rp ${fmt(currentSaldo + topupAmount)}</p>
                        ${date < today ? '<p class="text-warning mb-0">Saldo transaksi sesudah tanggal ini ikut bertambah.</p>' : ''}
                    </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal',
                    customClass: { confirmButton: 'btn btn-primary me-2', cancelButton: 'btn btn-label-secondary' },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            });

            function submitForm() {
                const formData = {
                    _token: '{{ csrf_token() }}',
                    transaction_date: $('#transaction-date').val(),
                    topup_amount: $('#topup-amount').val(),
                    notes: $('#notes').val(),
                };

                $('#submit-btn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...'
                );

                $.ajax({
                    url: '{{ route('main_transaction.kas_besar.store') }}',
                    method: 'POST',
                    data: formData,
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message })
                                .then(() => {
                                    window.location.href = '{{ route('main_transaction.kas_besar.index') }}';
                                });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                            $('#submit-btn').prop('disabled', false).html(
                                '<i class="icon-base ti tabler-device-floppy me-1"></i> Simpan'
                            );
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Terjadi kesalahan server.';
                        Swal.fire({ icon: xhr.status === 422 ? 'warning' : 'error', title: xhr.status === 422 ? 'Periksa kembali' : 'Error', text: msg });
                        $('#submit-btn').prop('disabled', false).html(
                            '<i class="icon-base ti tabler-device-floppy me-1"></i> Simpan'
                        );
                    }
                });
            }
        });
    </script>
@endsection
