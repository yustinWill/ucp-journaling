@extends('layouts.default')
@section('page_title')
    Tambah Jurnal
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Tambah Jurnal Baru</h5>
        <form class="card-body" id="journalAddForm">
            <!-- Info Jurnal Section -->
            <div class="section-header mt-12">
                <h6>Informasi Jurnal</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false">
                        <option value="ACTIVE" selected>ACTIVE - Active</option>
                        <option value="INACTIVE">INACTIVE - Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Amount</label>
                    <input type="text" class="form-control text-end curr-mask" id="total-amount" value="Rp 0" disabled />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">No Referensi</label>
                    <input type="text" class="form-control" id="reference-no" value="AUTO-GENERATED" disabled />
                    <small class="form-text text-muted">Nomor referensi akan diisi otomatis oleh sistem</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jurnal <span style="color: red;">*</span></label>
                    <input type="text" class="form-control global-single-date-picker" id="journal-date" value="" />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="description" rows="3" placeholder="Masukkan deskripsi jurnal..." maxlength="1000"></textarea>
                    <small class="form-text text-muted">Maksimal 1000 karakter</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" id="memo" rows="3" placeholder="Catatan internal untuk tim keuangan..." maxlength="500"></textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                </div>
            </div>
            
            <!-- Journal Entries Section -->
            <div class="section-header mt-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Entri Jurnal</h6>
                    <button type="button" class="btn btn-primary" id="add-journal-entry">
                        <i class="icon-base ti tabler-plus"></i>
                        <span class="align-middle">Tambah Entri</span>
                    </button>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="mx-n6">
                    <table class="table table-bordered" id="journal-entries-table">
                        <thead>
                            <tr>
                                <th width="20%">Akun</th>
                                <th width="30%">Deskripsi</th>
                                <th width="20%">Debit</th>
                                <th width="20%">Kredit</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="journal-entries-tbody">
                            <!-- Entries will be added here dynamically -->
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <th></th>
                                <th>Total</th>
                                <th class="text-end" id="total-debit">Rp 0</th>
                                <th class="text-end" id="total-credit">Rp 0</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>Selisih</th>
                                <th class="text-end" id="difference">Rp 0</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan Data</button>
            </div>
        </form>
    </div>
@endsection

@section('js_script')
<script>
    $(function() {
        let entryCounter = 0;
        
        // Account options for new rows
        const accountOptions = [
            @foreach($accounts as $account)
                {code: '{{ $account['code'] }}', name: '{{ $account['name'] }}', label: '{{ $account['label'] }}'},
            @endforeach
        ];
        
        // Add new journal entry
        $('#add-journal-entry').click(function() {
            addJournalEntry();
        });
        
        function addJournalEntry() {
            entryCounter++;
            // Generate account options HTML
            let optionsHtml = '<option value="">Pilih Akun</option>';
            accountOptions.forEach(function(account) {
                optionsHtml += `<option value="${account.code}" data-name="${account.name}">${account.label}</option>`;
            });
            
            const row = `
                <tr class="journal-entry-row" data-id="${entryCounter}">
                    <td>
                        <select class="global-select2 form-select account-select" data-id="${entryCounter}" data-allow-clear="false">
                            ${optionsHtml}
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control entry-description" data-id="${entryCounter}">
                    </td>
                    <td>
                        <input type="text" class="form-control curr-mask debit-amount text-end" data-id="${entryCounter}">
                    </td>
                    <td>
                        <input type="text" class="form-control curr-mask credit-amount text-end" data-id="${entryCounter}">
                    </td>
                    <td>
                        <button class="btn btn-sm btn-danger remove-entry" type="button" data-id="${entryCounter}">
                            <i class="icon-base ti tabler-x"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#journal-entries-tbody').append(row);
            
            // Initialize select2 for new row with proper parent container
            $(`.account-select[data-id="${entryCounter}"]`).select2({
                dropdownParent: $('.card-body')
            });
            
            // Initialize currency mask for new row
            if (typeof Cleave !== 'undefined') {
                $(`.curr-mask[data-id="${entryCounter}"]`).each(function() {
                    new Cleave(this, {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand',
                        prefix: 'Rp ',
                        noImmediatePrefix: false,
                        rawValueTrimPrefix: true,
                        numeralPositiveOnly: true,
                        stripLeadingZeroes: true
                    });
                });
            }
        }
        
        // Remove journal entry
        $(document).on('click', '.remove-entry', function() {
            const id = $(this).data('id');
            $(`.journal-entry-row[data-id="${id}"]`).remove();
            calculateTotals();
        });
        
        // Calculate totals when amounts change
        $(document).on('input', '.debit-amount, .credit-amount', function() {
            calculateTotals();
        });
        
        function calculateTotals() {
            let totalDebit = 0;
            let totalCredit = 0;
            
            $('.debit-amount').each(function() {
                const value = $(this).val().replace(/[^0-9]/g, '');
                if (value) {
                    totalDebit += parseInt(value);
                }
            });
            
            $('.credit-amount').each(function() {
                const value = $(this).val().replace(/[^0-9]/g, '');
                if (value) {
                    totalCredit += parseInt(value);
                }
            });
            
            const difference = totalDebit - totalCredit;
            
            $('#total-debit').text('Rp ' + totalDebit.toLocaleString('id-ID'));
            $('#total-credit').text('Rp ' + totalCredit.toLocaleString('id-ID'));
            $('#total-amount').val('Rp ' + Math.max(totalDebit, totalCredit).toLocaleString('id-ID'));
            
            if (difference === 0) {
                $('#difference').text('Rp 0').removeClass('text-danger').addClass('text-success');
            } else {
                $('#difference').text('Rp ' + Math.abs(difference).toLocaleString('id-ID')).removeClass('text-success').addClass('text-danger');
            }
        }
        
        // Form submission
        $('#submit-btn').click(function(e) {
            e.preventDefault();
            
            // Validate required fields
            if (!$('#journal-date').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Tanggal jurnal harus diisi.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }
            
            if (!$('#description').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Deskripsi jurnal harus diisi.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }
            
            // Validate totals balance
            const totalDebit = parseInt($('#total-debit').text().replace(/[^0-9]/g, '') || 0);
            const totalCredit = parseInt($('#total-credit').text().replace(/[^0-9]/g, '') || 0);
            
            if (totalDebit !== totalCredit) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Total debit dan kredit harus seimbang.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }
            
            if ($('.journal-entry-row').length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Minimal harus ada satu entri jurnal.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
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
                    // TODO: Submit form data to server
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Jurnal berhasil disimpan.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    }).then(function() {
                        window.location.href = "{{ route('main_transaction.journal.index') }}";
                    });
                }
            });
        });
        
        // Cancel button
        $('#cancel-btn').click(function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Batalkan Input?',
                text: 'Data yang sudah diisi akan hilang!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tidak',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = "{{ route('main_transaction.journal.index') }}";
                }
            });
        });
        
        // Initialize currency formatting
        if (typeof Cleave !== 'undefined') {
            $('.curr-mask').each(function() {
                new Cleave(this, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    prefix: 'Rp ',
                    noImmediatePrefix: false,
                    rawValueTrimPrefix: true,
                    numeralPositiveOnly: true,
                    stripLeadingZeroes: true
                });
            });
        }
        
        // Add initial entry
        addJournalEntry();
        addJournalEntry();
    });
</script>
@endsection