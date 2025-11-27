<div class="header" style="text-align: left;">
    <h2>SALES INVOICE</h2>
    <h3>{{ $invoice_data->transaction_code }}</h3>
    <p>Tanggal: {{ date('d/m/Y', strtotime($invoice_data->invoice_date)) }}</p>
</div>

<div class="info-section">
    <table style="border: none;">
        <tr style="border: none;">
            <td style="border: none; width: 50%; vertical-align: top;">
                <strong>Informasi Perusahaan:</strong><br>
                RIYO Water<br>
                Jl. Contoh Alamat No. 123<br>
                Kota, Provinsi 12345<br>
                Telp: (021) 1234-5678
            </td>
            <td style="border: none; width: 50%; vertical-align: top;">
                <strong>Informasi Customer:</strong><br>
                {{ $invoice_data->customer_name ?? '-' }}<br>
                {{ $invoice_data->customer_address ?? '-' }}<br>
                Email: {{ $invoice_data->customer_email ?? '-' }}
            </td>
        </tr>
    </table>
</div>

<div class="info-section">
    <table style="border: none;">
        <tr style="border: none;">
            <td style="border: none; width: 50%;">
                <strong>Nomor SO:</strong> {{ $invoice_data->so_reference ?? '-' }}
            </td>
            <td style="border: none; width: 50%;">
                <strong>Tanggal Jatuh Tempo:</strong> {{ $invoice_data->due_date ? date('d/m/Y', strtotime($invoice_data->due_date)) : '-' }}
            </td>
        </tr>
        <tr style="border: none;">
            <td style="border: none; width: 50%;">
                <strong>Termin Pembayaran:</strong> {{ $invoice_data->payment_term_name ?? '-' }}
            </td>
            <td style="border: none; width: 50%;">
                <strong>Status:</strong> {{ $invoice_data->status }}
            </td>
        </tr>
    </table>
</div>

<div class="info-section">
    <h4>Detail Produk</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Produk</th>
                <th style="width: 10%;" class="text-center">Qty Invoice</th>
                <th style="width: 10%;" class="text-center">Unit</th>
                <th style="width: 15%;" class="text-right">Harga Satuan</th>
                <th style="width: 15%;" class="text-right">Total</th>
                <th style="width: 15%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice_products as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $product->product_code ?? '' }}</strong><br>{{ $product->product_name ?? '' }}</td>
                    <td class="text-center">{{ number_format($product->qty, 2, ',', '.') }}</td>
                    <td class="text-center">{{ $product->unit_code ?? '' }}</td>
                    <td class="text-right">Rp {{ number_format($product->unit_price ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($product->line_total ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $product->product_invoice_note ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada item</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($invoice_data->subtotal ?? 0, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><strong>Total Pajak:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($invoice_data->total_tax ?? 0, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><strong>Total Diskon:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($invoice_data->total_discount ?? 0, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
            <tr style="background-color: #f5f5f5;">
                <td colspan="5" class="text-right"><strong>GRAND TOTAL:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($invoice_data->grand_total ?? 0, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

@if($invoice_data->description)
<div class="info-section">
    <strong>Keterangan:</strong><br>
    {{ $invoice_data->description }}
</div>
@endif

<div class="info-section" style="margin-top: 50px;">
    <table style="border: none;">
        <tr style="border: none;">
            <td style="border: none; width: 33%; text-align: center; vertical-align: bottom;">
                <div style="margin-top: 80px; border-top: 1px solid #000; padding-top: 5px;">
                    Dibuat Oleh
                </div>
            </td>
            <td style="border: none; width: 33%; text-align: center; vertical-align: bottom;">
                <div style="margin-top: 80px; border-top: 1px solid #000; padding-top: 5px;">
                    Diperiksa Oleh
                </div>
            </td>
            <td style="border: none; width: 33%; text-align: center; vertical-align: bottom;">
                <div style="margin-top: 80px; border-top: 1px solid #000; padding-top: 5px;">
                    Disetujui Oleh
                </div>
            </td>
        </tr>
    </table>
</div>
