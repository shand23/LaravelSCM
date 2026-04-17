<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran - {{ $invoice->id_invoice }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; line-height: 1.5; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .section-title { background: #f3f4f6; padding: 5px 10px; font-weight: bold; border-left: 4px solid #1d4ed8; margin: 15px 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .item-table th { background: #1d4ed8; color: white; padding: 7px; border: 1px solid #ddd; }
        .item-table td { padding: 7px; border: 1px solid #ddd; }
        .photo-container { text-align: center; margin-top: 20px; page-break-before: always; }
       .photo-container img { 
    max-width: 500px; 
    max-height: 600px; 
    width: auto; 
    height: auto; 
    border: 1px solid #ccc; 
    margin-top: 10px;
}
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; color: #1d4ed8;">LAPORAN PERMOHONAN PEMBAYARAN</h2>
        <p style="margin:2px 0;">No. Dokumen: {{ $invoice->id_invoice }}</p>
    </div>

    <div class="section-title">I. INFORMASI TAGIHAN (INVOICE)</div>
    <table class="info-table">
        <tr>
            <td width="20%">No. Invoice Supplier</td><td width="2%">:</td><td>{{ $invoice->nomor_invoice_supplier }}</td>
            <td width="20%">Tanggal Tagihan</td><td width="2%">:</td><td>{{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Jatuh Tempo</td><td>:</td><td>{{ \Carbon\Carbon::parse($invoice->jatuh_tempo)->format('d/m/Y') }}</td>
            <td>Total Tagihan</td><td>:</td><td><strong>Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    <div class="section-title">II. INFORMASI KONTRAK / PURCHASE ORDER (PO)</div>
    <table class="info-table">
        <tr>
            <td width="20%">No. Kontrak</td><td width="2%">:</td><td>{{ $kontrak->nomor_kontrak }}</td>
            <td width="20%">Supplier</td><td width="2%">:</td><td>{{ $kontrak->supplier->nama_supplier }}</td>
        </tr>
        <tr>
            <td>Nilai Kontrak</td><td>:</td><td>Rp {{ number_format($kontrak->total_nilai_kontrak, 0, ',', '.') }}</td>
            <td>Status</td><td>:</td><td>{{ $kontrak->status_kontrak }}</td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Item Barang</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kontrak->detailKontrak as $idx => $item)
            <tr>
                <td align="center">{{ $idx+1 }}</td>
                <td>{{ $item->material->nama_material }}</td>
                <td align="center">{{ $item->jumlah_final }} {{ $item->material->satuan }}</td>
                <td align="right">Rp {{ number_format($item->harga_negosiasi_satuan, 0, ',', '.') }}</td>
                <td align="right">Rp {{ number_format($item->jumlah_final * $item->harga_negosiasi_satuan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">III. LAMPIRAN BUKTI FISIK INVOICE</div>
  <div class="photo-container">
        @if($imageData)
            <p>Foto Bukti Invoice Supplier:</p>
            <img src="{{ $imageData }}">
        @elseif($fileType === 'pdf')
            <p style="color: blue; font-style: italic;">
                [ Bukti berupa file PDF. Silakan lihat langsung melalui sistem untuk mengunduh bukti PDF tersebut. ]
            </p>
        @else
            <p style="color: red; font-style: italic;">Lampiran foto tidak tersedia.</p>
        @endif
    </div>

    <div style="margin-top: 30px;">
        <table width="100%">
            <tr>
                <td width="33%" align="center">Dibuat Oleh,<br><br><br><br>( {{ Auth::user()->name }} )</td>
                <td width="33%" align="center">Diperiksa Oleh,<br><br><br><br>( ________________ )</td>
                <td width="33%" align="center">Disetujui Keuangan,<br><br><br><br>( ________________ )</td>
            </tr>
        </table>
    </div>
</body>
</html>