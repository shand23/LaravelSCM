<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Retur - DO-{{ $doRetur->id_pengiriman ?? '-' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px; vertical-align: top; }
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th, .item-table td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: middle; }
        .item-table th { background-color: #f4f4f4; text-align: center; }
        .footer { margin-top: 40px; text-align: right; }
        .signature { margin-top: 60px; border-top: 1px solid #333; display: inline-block; padding-top: 5px; width: 200px; text-align: center;}
        
        .text-center { text-align: center; }
        .img-bukti { width: 80px; height: auto; max-height: 80px; border: 1px solid #ccc; border-radius: 4px; padding: 2px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Barang Rusak / Ditolak</h1>
        <p>Delivery Order: <strong>{{ $doRetur->id_pengiriman ?? '-' }}</strong></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>ID Pengiriman</strong></td>
            <td width="35%">: {{ $doRetur->id_pengiriman ?? '-' }}</td>
            <td width="20%"><strong>Tanggal Cetak</strong></td>
            <td width="30%">: {{ now()->translatedFormat('d F Y H:i') }}</td>
        </tr>
        <tr>
            {{-- Karena fungsi Anda menggunakan with('kontrak'), kita bisa tampilkan Nomor PO juga jika ada --}}
            <td><strong>Nomor PO</strong></td>
            <td>: {{ $doRetur->kontrak->nomor_kontrak ?? '-' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <p style="color: #333; margin-bottom: 15px;">Berikut adalah rincian material yang dilaporkan rusak atau tidak sesuai saat penerimaan di lokasi:</p>

    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Material</th>
                <th width="15%">Jml Rusak</th>
                <th width="25%">Catatan Petugas</th>
                <th width="20%">Foto Bukti</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataDetailRetur as $index => $retur)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $retur->nama_material ?? '-' }}</strong></td>
                <td class="text-center"><strong>{{ $retur->jumlah_rusak ?? 0 }}</strong></td>
                <td style="font-style: italic;">
                    "{{ $retur->alasan_return ?? 'Tidak ada catatan' }}"
                </td>
                <td class="text-center">
                    @php
                        $imgBase64 = null;
                        
                        if (!empty($retur->foto_bukti_rusak)) {
                            $pathFile = storage_path('app/public/' . $retur->foto_bukti_rusak);
                            
                            if (file_exists($pathFile)) {
                                $fileData = file_get_contents($pathFile);
                                $extension = pathinfo($pathFile, PATHINFO_EXTENSION);
                                $imgBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($fileData);
                            }
                        }
                    @endphp

                    @if($imgBase64)
                        <img src="{{ $imgBase64 }}" class="img-bukti" alt="Bukti Rusak">
                    @else
                        <div style="font-size: 10px; color: #555; text-align: center; border: 1px dashed #ccc; padding: 5px;">
                            <em>Tanpa Lampiran</em>
                        </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px; color: #777; font-style: italic;">
                    Data rincian material rusak tidak ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Mengetahui,</p>
        <div class="signature">
            <strong>Petugas Gudang / Penerima</strong>
        </div>
    </div>

</body>
</html>