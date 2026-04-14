<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $pengiriman->id_pengiriman }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-title {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .info-table td {
            vertical-align: top;
            padding: 4px 0;
        }
        .label {
            font-weight: bold;
            width: 120px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .content-table th, .content-table td {
            border: 1px solid #bdc3c7;
            padding: 8px 10px;
        }
        .content-table th {
            background-color: #ecf0f1;
            font-weight: bold;
            text-align: left;
            color: #2c3e50;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .signature-table {
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }
        .signature-box {
            width: 30%;
            display: inline-block;
        }
        .signature-space {
            height: 80px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="60%">
                <h1 class="header-title">SURAT JALAN (DELIVERY ORDER)</h1>
                <p style="margin: 5px 0 0 0; color: #7f8c8d;">Dokumen Resmi Pengiriman Material Proyek</p>
            </td>
            <td width="40%" class="text-right">
                <h2 style="margin: 0; font-size: 18px;">ID: {{ $pengiriman->id_pengiriman }}</h2>
            </td>
        </tr>
    </table>
<table class="info-table">
        <tr>
            <td width="50%">
                <table width="100%">
                    <tr>
                        <td class="label">Nama Supplier</td>
                        <td>: <strong>{{ $pengiriman->kontrak->supplier->nama_supplier ?? 'Supplier Tidak Terdaftar' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Nomor PO/Kontrak</td>
                        <td>: {{ $pengiriman->id_kontrak ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            
            <td width="50%">
                <table width="100%">
                    <tr>
                        <td class="label">Tanggal Kirim</td>
                        <td>: {{ \Carbon\Carbon::parse($pengiriman->tanggal_berangkat)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Estimasi</td>
                        <td>: {{ $pengiriman->estimasi_tanggal_tiba ? \Carbon\Carbon::parse($pengiriman->estimasi_tanggal_tiba)->format('d F Y') : '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="50%">Nama Material</th>
                <th class="text-center" width="15%">Satuan</th>
                <th class="text-right" width="30%">Jumlah Dikirim</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengiriman->detailPengiriman as $index => $detail)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $detail->detailKontrak->material->nama_material ?? 'Material Tidak Ditemukan' }}</td>
                <td class="text-center">{{ $detail->detailKontrak->material->satuan ?? '-' }}</td>
                <td class="text-right"><strong>{{ number_format($detail->jumlah_dikirim, 0, ',', '.') }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="padding: 20px;">Tidak ada detail barang untuk pengiriman ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td class="signature-box">
                <p><strong>Pihak Pengirim (Vendor/Ekspedisi)</strong></p>
                <div class="signature-space"></div>
                <p>___________________________</p>
            </td>
            <td class="signature-box">
                <p><strong>Mengetahui (Logistik)</strong></p>
                <div class="signature-space"></div>
                <p>___________________________</p>
            </td>
            <td class="signature-box">
                <p><strong>Pihak Penerima (Proyek)</strong></p>
                <div class="signature-space"></div>
                <p>___________________________</p>
            </td>
        </tr>
    </table>

</body>
</html>