<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $kontrak->nomor_kontrak }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #1d4ed8; }
        .header p { margin: 2px 0; }
        
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { vertical-align: top; padding: 2px; }
        .title-box { background: #f3f4f6; padding: 10px; font-weight: bold; text-align: center; border: 1px solid #ccc; margin-bottom: 15px; }

        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .item-table th { background-color: #1d4ed8; color: white; padding: 8px; border: 1px solid #ddd; text-align: center; }
        .item-table td { padding: 8px; border: 1px solid #ddd; }
        
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 4px 8px; }
        .total-row { font-weight: bold; background: #f3f4f6; font-size: 12px; }

        .footer { margin-top: 30px; }
        .signature-wrapper { width: 100%; margin-top: 50px; }
        .signature-box { width: 45%; float: right; text-align: center; }
        .space { height: 70px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PURCHASE ORDER (PO)</h1>
        <p><strong>PT. NAMA PERUSAHAAN ANDA</strong></p>
        <p>Alamat Kantor Lengkap Anda - Telp: 021-xxxxxx</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="55%">
                <strong>Kepada Yth:</strong><br>
                {{ $kontrak->supplier->nama_supplier }}<br>
                {{ $kontrak->supplier->alamat ?? '-' }}<br>
                Telp: {{ $kontrak->supplier->no_telepon?? '-' }}
            </td>
            <td width="45%">
                <strong>Nomor PO:</strong> {{ $kontrak->nomor_kontrak }}<br>
                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($kontrak->tanggal_kontrak)->translatedFormat('d F Y') }}<br>
                <strong>Nomor Pesanan:</strong> {{ $kontrak->pesanan->nomor_pesanan ?? '-' }}
            </td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Nama Material</th>
                <th width="10%">Qty</th>
                <th width="15%">Harga Satuan</th>
                <th width="25%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kontrak->detailKontrak as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->material->nama_material }}</td>
                <td style="text-align: center;">{{ $item->jumlah_final }} {{ $item->material->satuan }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->harga_negosiasi_satuan, 0, ',', '.') }}</td>
<td style="text-align: right;">Rp {{ number_format($item->jumlah_final * $item->harga_negosiasi_satuan, 0, ',', '.') }}</td>

            @endforeach
        </tbody>
    </table>

    <table width="100%">
        <tr>
            <td width="50%" style="vertical-align: top;">
                <p><strong>Keterangan:</strong><br>
                {{ $kontrak->keterangan ?? 'Barang dikirim sesuai dengan jadwal yang disepakati.' }}</p>
            </td>
            <td width="50%">
                <table class="summary-table">
                    <tr>
                  
    <td>Total Item</td>
    <td style="text-align: right;">Rp {{ number_format($kontrak->detailKontrak->sum(function($item) { return $item->jumlah_final * $item->harga_negosiasi_satuan; }), 0, ',', '.') }}</td>
                    @if($kontrak->total_diskon > 0)
                    <tr>
                        <td>Diskon ({{ $kontrak->diskon_persen }}%)</td>
                        <td style="text-align: right; color: red;">- Rp {{ number_format($kontrak->total_diskon, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Ongkos Kirim</td>
                        <td style="text-align: right;">Rp {{ number_format($kontrak->total_ongkir, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Pajak (PPN {{ $kontrak->ppn_persen ?? 11 }}%)</td>
                        <td style="text-align: right;">Rp {{ number_format($kontrak->total_ppn, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>GRAND TOTAL</td>
                        <td style="text-align: right;">Rp {{ number_format($kontrak->total_nilai_kontrak, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        <div class="signature-wrapper">
            <div class="signature-box">
                <p>Disetujui Oleh,</p>
                <div class="space"></div>
                <p><strong>( ____________________ )</strong><br>Manajer Pengadaan</p>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>