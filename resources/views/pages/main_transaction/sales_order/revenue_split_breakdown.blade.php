@php
    // Calculate percentages for the entire view
    $totalRevenue = $summary->total_amount ?? 1;
    $pabrikPercentage = $totalRevenue > 0 ? ($summary->total_pabrik / $totalRevenue) * 100 : 0;
    $ownerPercentage = $totalRevenue > 0 ? ($summary->total_owner / $totalRevenue) * 100 : 0;
    $perusahaanPercentage = $totalRevenue > 0 ? ($summary->total_perusahaan / $totalRevenue) * 100 : 0;
@endphp

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
        @if(empty($readonly))
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshRevenueSplit()">
                    <i class="icon-base ti tabler-refresh"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Summary Section -->
    <div class="section-header mt-12">
        <h6>Ringkasan Revenue Split</h6>
        <hr class="mx-n6">
    </div>

    <!-- Summary Cards with % and Amount -->
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
                            <small class="text-primary fw-bold">{{ number_format($pabrikPercentage, 1) }}%</small>
                            <br>
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
                            <small class="text-warning fw-bold">{{ number_format($ownerPercentage, 1) }}%</small>
                            <br>
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
                            <small class="text-success fw-bold">{{ number_format($perusahaanPercentage, 1) }}%</small>
                            <br>
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
                            <small class="text-info fw-bold">100%</small>
                            <br>
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
                        <th class="text-end">Qty Total</th>
                        <th class="text-end">Bonus</th>
                        <th class="text-end">Anulir</th>
                        <th class="text-end">Efektif</th>
                        <th class="text-end">Total Efektif</th>
                        <th class="text-end">Porsi Pabrik</th>
                        <th class="text-end">Porsi Owner</th>
                        <th class="text-end">Porsi Perusahaan</th>
                        <th>Status</th>
                        @if(empty($readonly))
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($splits as $split)
                        @php
                            // Calculate percentages per product
                            $productTotal = $split->product_total_amount > 0 ? $split->product_total_amount : 1;
                            $pabrikPct = ($split->porsi_pabrik / $productTotal) * 100;
                            $ownerPct = ($split->porsi_owner / $productTotal) * 100;
                            $perusahaanPct = ($split->porsi_perusahaan / $productTotal) * 100;
                        @endphp
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
                                @if($split->bonus_qty > 0)
                                    <span class="badge bg-label-info">{{ number_format($split->bonus_qty, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($split->return_qty > 0)
                                    <span class="badge bg-label-danger">{{ number_format($split->return_qty, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($split->return_qty > 0 || $split->bonus_qty > 0)
                                    <span class="badge bg-label-success">{{ number_format($split->effective_qty, 0, ',', '.') }}</span>
                                @else
                                    {{ number_format($split->product_quantity, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($split->product_total_amount, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end">
                                <span class="badge bg-label-primary mb-1">{{ number_format($pabrikPct, 1) }}%</span>
                                <br>
                                <span class="text-primary">Rp {{ number_format($split->porsi_pabrik, 0, ',', '.') }}</span>
                                @if($split->remaining_allocation === 'pabrik')
                                    <br><small class="text-success"><i class="icon-base ti tabler-plus"></i> Dapat sisa</small>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="badge bg-label-warning mb-1">{{ number_format($ownerPct, 1) }}%</span>
                                <br>
                                <span class="text-warning">Rp {{ number_format($split->porsi_owner, 0, ',', '.') }}</span>
                                @if($split->remaining_allocation === 'owner')
                                    <br><small class="text-success"><i class="icon-base ti tabler-plus"></i> Dapat sisa</small>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="badge bg-label-success mb-1">{{ number_format($perusahaanPct, 1) }}%</span>
                                <br>
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
                                    <span class="badge bg-label-success">
                                        <i class="icon-base ti tabler-settings me-1"></i>Auto
                                    </span>
                                    @if($split->f_revenue_split_config_id)
                                        <br><small class="text-muted">Dari konfigurasi</small>
                                    @else
                                        <br><small class="text-muted">Default sistem</small>
                                    @endif
                                @endif
                            </td>
                            @if(empty($readonly))
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
                            @endif
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
    @if(!empty($readonly))
        <div class="text-center py-4">
            <i class="icon-base ti tabler-percentage icon-48px text-muted mb-3"></i>
            <h6 class="text-muted">Belum Ada Revenue Split</h6>
            <p class="text-muted mb-3">Revenue split belum dikonfigurasi. Silakan atur melalui halaman detail Sales Order.</p>
        </div>
    @else
        <!-- Empty State with Add Form -->
        <div id="emptyStateContainer">
            <div class="text-center py-4">
                <i class="icon-base ti tabler-percentage icon-48px text-muted mb-3"></i>
                <h6 class="text-muted">Belum Ada Revenue Split</h6>
                <p class="text-muted mb-3">Klik tombol di bawah untuk menambahkan revenue split.</p>
                <button type="button" class="btn btn-primary" onclick="showAddRevenueSplitForm()">
                    <i class="icon-base ti tabler-plus me-2"></i>Tambah Revenue Split
                </button>
            </div>
        </div>

        <!-- Add Revenue Split Form (Hidden by default) -->
        <div id="addRevenueSplitFormContainer" style="display: none;">
            <div class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                <strong>Petunjuk:</strong> Masukkan porsi revenue untuk setiap produk. Total porsi harus sama dengan total harga produk.
            </div>

            <form id="addRevenueSplitForm">
                <div id="productSplitsList">
                    <!-- Products will be loaded here dynamically -->
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-secondary" onclick="hideAddRevenueSplitForm()">
                        <i class="icon-base ti tabler-x me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="icon-base ti tabler-device-floppy me-2"></i>Simpan Semua
                    </button>
                </div>
            </form>
        </div>
    @endif
@endif

@if(empty($readonly))
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
                    <input type="hidden" id="editOriginalQuantity">
                    <input type="hidden" id="editBonusQuantity">
                    <input type="hidden" id="editOriginalTotalAmount">
                    <input type="hidden" id="editConfigPabrik">
                    <input type="hidden" id="editConfigOwner">
                    <input type="hidden" id="editConfigPerusahaan">

                    <div class="alert alert-info">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        Bonus dibayar ke Pabrik dan dipotong dari porsi Owner. Total porsi harus sama
                        dengan total efektif: <strong id="editTotalDisplay"></strong>
                    </div>
                    <div class="alert alert-danger d-none" id="editCalculationError" role="alert"></div>
                    <div class="alert alert-warning d-none" id="editCalculationWarning" role="alert"></div>

                    <!-- Quantity Information Section -->
                    <div class="row g-3 mb-2">
                        <div class="col-md">
                            <label class="form-label">Qty Original</label>
                            <input type="text" class="form-control" id="editOriginalQtyDisplay" readonly>
                        </div>
                        <div class="col-md">
                            <label class="form-label">Qty Bonus</label>
                            <input type="text" class="form-control bg-label-info" id="editBonusQtyDisplay" readonly>
                        </div>
                        <div class="col-md">
                            <label class="form-label">Qty Anulir</label>
                            <input type="number" class="form-control" id="editReturnQty" min="0" step="1" value="0">
                        </div>
                        <div class="col-md">
                            <label class="form-label">Qty Efektif</label>
                            <input type="text" class="form-control bg-label-success" id="editEffectiveQty" readonly>
                        </div>
                        <div class="col-md">
                            <label class="form-label">Total Efektif</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control bg-label-success" id="editEffectiveTotal" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 mb-4" role="status" aria-live="polite">
                        <div class="d-flex align-items-start">
                            <i class="icon-base ti tabler-calculator me-2 mt-1"></i>
                            <div>
                                <strong>Rumus Porsi Default:</strong>
                                <div class="small mt-1" id="editFormulaPabrik">Pabrik = (Qty Efektif + Qty Bonus) x Rate Pabrik</div>
                                <div class="small" id="editFormulaOwner">Owner = (Qty Efektif x Rate Owner) - (Qty Bonus x Rate Pabrik)</div>
                                <div class="small" id="editFormulaPerusahaan">Perusahaan = Qty Efektif x Rate Perusahaan</div>
                                <div class="small mt-1 fw-semibold" id="editFormulaSisa">Selisih dialokasikan ke entity yang dipilih sebagai penerima sisa.</div>
                            </div>
                        </div>
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
@endif
