@extends('layouts.default')
@section('page_title')
    Tambah Jurnal
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Tambah Jurnal Baru</h5>
        <form class="card-body" id="journalAddForm" autocomplete="off">
            <!-- Info Jurnal Section -->
            <div class="section-header mt-12">
                <h6>Informasi Jurnal</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jurnal <span style="color: red;">*</span></label>
                    <input type="text" class="form-control global-single-date-picker" id="journal-date"
                        value="{{ $today }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Amount</label>
                    <input type="text" class="form-control text-end currency-input" id="total-amount" value="Rp 0"
                        disabled />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">No Referensi</label>
                    <input type="text" class="form-control" id="reference-no" value="{{ $referenceNo }}" disabled />
                    <small class="form-text text-muted">Nomor referensi akan diisi otomatis oleh sistem</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="journal-tags" multiple data-allow-clear="false">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" data-color="{{ $tag->tag_color_code }}">
                                        {{ $tag->code }} - {{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddTags)
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" id="add-new-contact-tag"
                                    title="Tambah Tag Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="description" rows="3" placeholder="Masukkan deskripsi jurnal..."
                        maxlength="1000"></textarea>
                    <small class="form-text text-muted">Maksimal 1000 karakter</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" id="memo" rows="3" placeholder="Catatan internal untuk tim keuangan..."
                        maxlength="500"></textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                </div>
            </div>

            <!-- Journal Entries Section -->
            <div class="section-header mt-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Entri Jurnal</h6>
                </div>
            </div>

            <div class="mb-4">
                <div class="mx-n6">
                    <div class="form-repeater-journal">
                        <div>
                            <table class="table table-bordered" id="journal-entries-table">
                                <thead>
                                    <tr>
                                        <th width="30%">Akun</th>
                                        <th width="30%">Deskripsi</th>
                                        <th width="15%">Debit</th>
                                        <th width="15%">Kredit</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="journal-entries-tbody" data-repeater-list>
                                    <tr data-repeater-item class="journal-entry-row">
                                        <td>
                                            <div class="position-relative">
                                                <select class="select2Coa form-select coa-select" data-allow-clear="false">
                                                    <option value="">Pilih COA</option>
                                                    @foreach ($coas as $coa)
                                                        <option value="{{ $coa->code }}">{{ $coa->code }} -
                                                            {{ $coa->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control description">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control currency-input debit-amount text-end">
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control currency-input credit-amount text-end">
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger remove-entry" data-repeater-delete
                                                type="button">
                                                <i class="icon-base ti tabler-x"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <th></th>
                                        <th>Total</th>
                                        <th class="text-end" id="total-debit">0</th>
                                        <th class="text-end" id="total-credit">0</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>Selisih</th>
                                        <th class="text-end" id="difference">0</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="mb-0">
                                <button class="btn btn-primary" data-repeater-create type="button">
                                    <i class="icon-base ti tabler-plus me-1"></i>
                                    <span class="align-middle">Tambah Entri Jurnal</span>
                                </button>
                            </div>
                        </div>
                    </div>
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
            initCleaveCurrency(document);

            $('select.select2Coa').each(function() {
                $(this).select2({
                    placeholder: "Select value",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

            $('.form-repeater-journal').repeater({
                show: function() {
                    $(this).slideDown(() => {
                        $(this).find('.select2-container').remove();
                        $(this).find('select.select2Coa').removeClass(
                            'select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('select.select2Coa').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                alert('Destroying existing Select2 instance');
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Pilih COA",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });
                        initCleaveCurrency(document);
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });

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
                    $('#difference').text('Rp ' + Math.abs(difference).toLocaleString('id-ID')).removeClass(
                        'text-success').addClass('text-danger');
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

                let journal_data = $('.form-repeater-journal [data-repeater-item]').map(function() {
                    const account_code = $(this).find(
                        'select.coa-select').val();
                    const description = $(this).find(
                        'input.description').val();
                    const debit_amount = $(this).find(
                        'input.debit-amount').val().replace(
                        /[^0-9]/g, '');
                    const credit_amount = $(this).find(
                            'input.credit-amount').val()
                        .replace(/[^0-9]/g, '');

                    // Only return if all fields are filled
                    if (account_code && (debit_amount ||
                            credit_amount)) {
                        return {
                            account_code,
                            description,
                            debit_amount,
                            credit_amount
                        };
                    }
                }).get()
                if (journal_data.length === 0) {
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
                        if (result.value) {
                            $('#submit-btn').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                            );
                            $.ajax({
                                url: "{{ route('main_transaction.journal.save') }}",
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    date: $('#journal-date').val(),
                                    reference_no: $('#reference-no').val(),
                                    description: $('#description').val(),
                                    memo: $('#memo').val(),
                                    tags: $('#journal-tags').val(),
                                    journals: journal_data,
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Jurnal berhasil ditambahkan.',
                                        customClass: {
                                            confirmButton: 'btn btn-success'
                                        },
                                        buttonsStyling: false
                                    }).then(function() {
                                        window.location.href =
                                            "{{ route('main_transaction.journal.index') }}";
                                    });
                                },
                                error: function(xhr) {
                                    const res = xhr.responseJSON;
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal menyimpan',
                                        html: res?.message ||
                                            'Terjadi kesalahan saat menyimpan data.',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false
                                    });
                                },
                                complete: function() {
                                    $('#submit-btn').prop('disabled', false).html(
                                        'Simpan Data');
                                }
                            });
                        }
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
        });
    </script>
@endsection
