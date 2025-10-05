@extends('layouts.default')
@section('page_title')
    Detail Jurnal
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Jurnal</h4>
                </div>

                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('main_transaction.journal.edit', $journal['reference_no']) }}"
                                class="btn btn-primary btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Journal Title -->
            <h2>{{ $journal['reference_no'] }}</h2>
            <span>
                <h6>Data jurnal berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>

            <hr class="my-6 mx-n6" />

            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="journal-info-list-item"
                            data-bs-toggle="list" href="#journal-info-tab" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Jurnal</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="journal-entries-list-item"
                            data-bs-toggle="list" href="#journal-entries-tab" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-receipt-2 me-2"></i>
                            <b>Entri Jurnal</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#audit-log-tab" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Journal Information Tab -->
                        <div class="tab-pane fade show active" id="journal-info-tab" role="tabpanel">
                            <!-- Info Jurnal Section -->
                            <div class="section-header mt-6">
                                <h6>Informasi Jurnal</h6>
                                <hr class="mx-n6">
                            </div>

                            <!-- Row 1: Basic Information -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">No Referensi</label>
                                    <input type="text" class="form-control" value="{{ $journal['reference_no'] }}"
                                        disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Jurnal</label>
                                    <input type="text" class="form-control" value="{{ $journal['journal_date'] }}"
                                        disabled />
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $journal['description'] }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                                    <textarea class="form-control" rows="3" disabled>{{ $journal['memo'] }}</textarea>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Tag</label>
                                    <div class="form-control-plaintext">
                                        @if (isset($journal['tags']) && count($journal['tags']) > 0)
                                            @foreach ($journal['tags'] as $tag)
                                                <span class="badge {{ $tag['preview'] }} me-1">{{ $tag['code'] }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- Empty column for alignment -->
                                </div>
                            </div>


                            <!-- Journal Summary -->
                            <div class="section-header mt-12">
                                <h6>Ringkasan Jurnal</h6>
                                <hr class="mx-n6">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Total Debit</label>
                                    <input type="text" class="form-control text-end"
                                        value="{{ $journal['total_debit'] }}" disabled />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Total Kredit</label>
                                    <input type="text" class="form-control text-end"
                                        value="{{ $journal['total_credit'] }}" disabled />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Selisih</label>
                                    <input type="text" class="form-control text-end"
                                        value="{{ $journal['difference'] }}" disabled />
                                </div>
                            </div>

                        </div>

                        <!-- Journal Entries Tab -->
                        <div class="tab-pane fade" id="journal-entries-tab" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Entri Jurnal Detail</h6>
                            </div>
                            <div class="mx-n6">
                                <table class="table table-bordered table-sm" id="journal-entries-table">
                                    <thead>
                                        <tr>
                                            <th>Kode Akun</th>
                                            <th>Nama Akun</th>
                                            <th>Deskripsi</th>
                                            <th>Debit</th>
                                            <th>Kredit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($journal['entries'] as $entry)
                                            <tr>
                                                <td>{{ $entry['account_code'] }}</td>
                                                <td><a href="{{ route('master_data.coa.detail', ['id' => $entry['account_code']]) }}"
                                                        class="text-primary fw-semibold">{{ $entry['account_name'] }}</a>
                                                </td>
                                                <td>{{ $entry['description'] }}</td>
                                                <td class="text-end">{{ $entry['debit_amount'] ?: '-' }}</td>
                                                <td class="text-end">{{ $entry['credit_amount'] ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <th></th>
                                            <th></th>
                                            <th>Total</th>
                                            <th class="text-end">{{ $journal['total_debit'] }}</th>
                                            <th class="text-end">{{ $journal['total_credit'] }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="audit-log-tab" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Riwayat Perubahan</h6>
                            </div>
                            <div class="mx-n6">
                                <table class="table table-bordered" id="audit-log-table">
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
                                        @foreach ($journal['audit_logs'] as $log)
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
                                                            href="{{ route('audit_log.detail', ['id' => $log->id, 'audit_log_type' => $journal['auditLogType']]) }}"
                                                            class="btn btn-sm btn-primary waves-effect waves-light"
                                                            title="Detail">
                                                            <i class="icon-xs icon-base ti tabler-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Initialize DataTables for journal entries and audit log
            $('#journal-entries-table').DataTable({
                responsive: true,
                orderCellsTop: true,
                searching: false,
                paging: false,
                info: false,
                columnDefs: [{
                    targets: [3, 4], // Debit, Credit columns
                    className: 'text-end'
                }],
                language: {
                    paginate: {
                        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                        first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                        last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                    }
                }
            });

            $('#audit-log-table').DataTable({
                responsive: true,
                orderCellsTop: true,
                searching: false,
                paging: false,
                info: false,
                language: {
                    paginate: {
                        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                        first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                        last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                    }
                }
            });
        });
    </script>
@endsection
