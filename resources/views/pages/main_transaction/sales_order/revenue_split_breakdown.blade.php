@if($splits && count($splits) > 0)
    <!-- Action Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @if($summary && $summary->custom_splits > 0)
                <span class="badge bg-label-warning me-2">
                    <i class="icon-base ti tabler-edit me-1"></i>{{ $summary->custom_splits }} Custom Split(s)
                </span>
            @endif
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshRevenueSplit()">
                <i class="icon-base ti tabler-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="section-header mt-12">
        <h6>Ringkasan Revenue Split</h6>
        <hr class="mx-n6">
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-label-primary">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-primary">
                                <i class="icon-base ti tabler-building-factory icon-20px text-white"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">Porsi Pabrik</h6>
                            <small class="text-primary">Rp {{ number_format($summary->total_pabrik ?? 0, 0, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-label-warning">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-warning">
                                <i class="icon-base ti tabler-user-star icon-20px text-white"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">Porsi Owner</h6>
                            <small class="text-warning">Rp {{ number_format($summary->total_owner ?? 0, 0, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-label-success">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-success">
                                <i class="icon-base ti tabler-building icon-20px text-white"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">Porsi Perusahaan</h6>
                            <small class="text-success">Rp {{ number_format($summary->total_perusahaan ?? 0, 0, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-label-info">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-info">
                                <i class="icon-base ti tabler-chart-pie icon-20px text-white"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Revenue</h6>
                            <small class="text-info">Rp {{ number_format($summary->total_amount ?? 0, 0, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown Section -->
    <div class="section-header mt-12">
        <h6>Detail Revenue Split per Produk</h6>
    </div>

    <div class="mb-4 mx-n5">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Porsi Pabrik</th>
                        <th class="text-end">Porsi Owner</th>
                        <th class="text-end">Porsi Perusahaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($splits as $split)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $split->product_name }}</strong>
                                    <br><small class="text-muted">{{ $split->product_code }}</small>
                                </div>
                            </td>
                            <td class="text-end">Rp {{ number_format($split->product_unit_price, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($split->product_quantity, 0, ',', '.') }} {{ $split->unit_name }}</td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($split->product_total_amount, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end">
                                <span class="text-primary">Rp {{ number_format($split->porsi_pabrik, 0, ',', '.') }}</span>
                                @if($split->remaining_allocation === 'pabrik')
                                    <br><small class="text-success"><i class="icon-base ti tabler-plus"></i> Dapat sisa</small>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="text-warning">Rp {{ number_format($split->porsi_owner, 0, ',', '.') }}</span>
                                @if($split->remaining_allocation === 'owner')
                                    <br><small class="text-success"><i class="icon-base ti tabler-plus"></i> Dapat sisa</small>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="text-success">Rp {{ number_format($split->porsi_perusahaan, 0, ',', '.') }}</span>
                                @if($split->remaining_allocation === 'perusahaan')
                                    <br><small class="text-success"><i class="icon-base ti tabler-plus"></i> Dapat sisa</small>
                                @endif
                            </td>
                            <td>
                                @if($split->is_custom_split)
                                    <span class="badge bg-label-warning">
                                        <i class="icon-base ti tabler-edit me-1"></i>Custom
                                    </span>
                                    @if($split->split_notes)
                                        <br><small class="text-muted" title="{{ $split->split_notes }}">
                                            <i class="icon-base ti tabler-note"></i> Ada catatan
                                        </small>
                                    @endif
                                @else
                                    @if($split->f_revenue_split_config_id)
                                        <span class="badge bg-label-success">
                                            <i class="icon-base ti tabler-settings me-1"></i>Auto
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary">
                                            <i class="icon-base ti tabler-question-mark me-1"></i>Default
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" 
                                                onclick="editRevenueSplit({{ $split->f_product_id }}, '{{ $split->product_name }}', {{ json_encode($split) }})">
                                                <i class="icon-base ti tabler-edit me-2"></i>Edit Split
                                            </a>
                                        </li>
                                        @if($split->is_custom_split)
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)" 
                                                    onclick="resetRevenueSplit({{ $split->f_product_id }}, '{{ $split->product_name }}')">
                                                    <i class="icon-base ti tabler-refresh me-2"></i>Reset to Default
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Distribution Chart Section -->
    <div class="section-header mt-12">
        <h6>Distribusi Persentase Revenue Split</h6>
        <hr class="mx-n6">
    </div>

    @php
        $totalRevenue = $summary->total_amount ?? 1;
        $pabrikPercentage = ($summary->total_pabrik / $totalRevenue) * 100;
        $ownerPercentage = ($summary->total_owner / $totalRevenue) * 100;
        $perusahaanPercentage = ($summary->total_perusahaan / $totalRevenue) * 100;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span>Pabrik</span>
                <span>{{ number_format($pabrikPercentage, 1) }}%</span>
            </div>
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-primary" style="width: {{ $pabrikPercentage }}%"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span>Owner</span>
                <span>{{ number_format($ownerPercentage, 1) }}%</span>
            </div>
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" style="width: {{ $ownerPercentage }}%"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span>Perusahaan</span>
                <span>{{ number_format($perusahaanPercentage, 1) }}%</span>
            </div>
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: {{ $perusahaanPercentage }}%"></div>
            </div>
        </div>
    </div>

@else
    <!-- Empty State -->
    <div class="text-center py-4">
        <i class="icon-base ti tabler-percentage icon-48px text-muted mb-3"></i>
        <h6 class="text-muted">Belum Ada Revenue Split</h6>
        <p class="text-muted mb-0">Revenue split akan dibuat otomatis ketika sales order memiliki produk.</p>
    </div>
@endif

<!-- Edit Revenue Split Modal -->
<div class="modal fade" id="editRevenueSplitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icon-base ti tabler-edit me-2"></i>Edit Revenue Split: <span id="editProductName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRevenueSplitForm">
                <div class="modal-body">
                    <input type="hidden" id="editProductId">
                    <input type="hidden" id="editProductTotal">
                    
                    <div class="alert alert-info">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        Total porsi harus sama dengan total produk: <strong id="editTotalDisplay"></strong>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Porsi Pabrik <span style="color: red;">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control currency-input" id="editPorsiPabrik" required>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="edit_remaining_allocation" value="pabrik" id="editRemainingPabrik">
                                <label class="form-check-label text-success" for="editRemainingPabrik">
                                    <small>Dapat sisa</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Porsi Owner <span style="color: red;">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control currency-input" id="editPorsiOwner" required>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="edit_remaining_allocation" value="owner" id="editRemainingOwner">
                                <label class="form-check-label text-success" for="editRemainingOwner">
                                    <small>Dapat sisa</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Porsi Perusahaan <span style="color: red;">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control currency-input" id="editPorsiPerusahaan" required>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="edit_remaining_allocation" value="perusahaan" id="editRemainingPerusahaan">
                                <label class="form-check-label text-success" for="editRemainingPerusahaan">
                                    <small>Dapat sisa</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Total Split</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="editTotalSplit" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Selisih</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="editDifference" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="editSplitNotes" rows="3" 
                                placeholder="Catatan mengenai perubahan revenue split ini..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="saveEditSplitBtn">
                        <i class="icon-base ti tabler-device-floppy me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>