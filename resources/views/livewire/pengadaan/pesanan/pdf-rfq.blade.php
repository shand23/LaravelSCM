<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Request for Quotation - {{ $pesanan->nomor_pesanan }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px; vertical-align: top; }
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th, .item-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .item-table th { background-color: #f4f4f4; }
        .footer { margin-top: 40px; text-align: right; }
        .signature { margin-top: 60px; border-top: 1px solid #333; display: inline-block; padding-top: 5px; width: 200px; text-align: center;}
    </style>
</head>
<body>

    <div class="header">
        <h1>Request for Quotation (RFQ)</h1>
        <p>No. Dokumen: <strong>{{ $pesanan->nomor_pesanan }}</strong></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Kepada</strong></td>
            <td width="35%">: {{ $pesanan->supplier->nama_supplier ?? '-' }}</td>
            <td width="20%"><strong>Tanggal Dibuat</strong></td>
            <td width="30%">: {{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Kontak Supplier</strong></td>
            <td>: {{ $pesanan->supplier->no_telepon ?? '-' }}</td>
            <td><strong>Referensi PR</strong></td>
            <td>: {{ $pesanan->id_pengajuan }}</td>
        </tr>
    </table>

    <p>Dengan hormat,<br>Bersama dokumen ini, kami memohon penawaran harga (quotation) untuk kebutuhan material berikut:</p>

    <table class="item-table">
        <thead>
            <tr>
                <th width="5%" style="text-align: center;">No</th>
                <th width="50%">Nama Material</th>
                <th width="20%" style="text-align: center;">Qty Dibutuhkan</th>
                <th width="25%">Harga Penawaran (Diisi Supplier)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan->detailPesanan as $index => $detail)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $detail->material->nama_material }}</td>
                <td style="text-align: center;">{{ $detail->jumlah_pesan }} {{ $detail->material->satuan }}</td>
                <td></td> {{-- Dikosongkan agar bisa diisi tulisan tangan/diketik oleh supplier --}}
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 11px; color: #555;">
        <em>* Mohon berikan penawaran harga terbaik Anda dengan mengisi kolom harga di atas atau melampirkan surat penawaran resmi dari perusahaan Anda.<br>
        * Harga yang ditawarkan mohon agar sudah mempertimbangkan ongkos kirim (jika ada) dan PPN.</em>
    </p>

    <div class="footer">
        <p>Hormat Kami,</p>
        <p style="margin-top: 50px; text-decoration: underline;"><strong>Tim Pengadaan & Pembelian</strong></p>
    </div>

</body>
</html>