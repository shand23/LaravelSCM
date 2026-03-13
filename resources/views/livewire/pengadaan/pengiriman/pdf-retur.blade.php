<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Retur - DO-{{ $infoDORetur->id_pengiriman ?? '-' }}</title>
    <style>
        body { 
            font-family: sans-serif; 
            font-size: 12px; 
            color: #333; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #991b1b; /* Warna merah gelap seperti di UI */
            padding-bottom: 10px; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 18px; 
            text-transform: uppercase; 
            color: #991b1b;
        }
        .info-table { 
            width: 100%; 
            margin-bottom: 20px; 
        }
        .info-table td { 
            padding: 4px; 
            vertical-align: top; 
        }
        .item-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .item-table th, .item-table td { 
            border: 1px solid #ccc; 
            padding: 8px; 
            text-align: left; 
            vertical-align: middle; 
        }
        .item-table th { 
            background-color: #fef2f2; /* Merah sangat muda */
            color: #991b1b;
            text-align: center; 
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-red { color: #dc2626; }
        .img-bukti { 
            width: 80px; 
            height: auto; 
            max-height: 80px; 
            border: 1px solid #ccc; 
            border-radius: 4px;
            padding: 2px; 
        }
        .footer { 
            margin-top: 40px; 
            text-align: right; 
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Barang Rusak / Ditolak</h1>
        <p>Delivery Order: <strong>{{ $infoDORetur->id_pengiriman ?? '-' }}</strong></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>ID Pengiriman</strong></td>
            <td width="35%">: {{ $infoDORetur->id_pengiriman ?? '-' }}</td>
            <td width="20%"><strong>Tanggal Cetak</strong></td>
            <td width="30%">: {{ now()->translatedFormat('d F Y H:i') }}</td>
        </tr>
    </table>

    <p style="color: #666; margin-bottom: 15px;">Berikut adalah rincian material yang dilaporkan rusak atau tidak sesuai saat penerimaan di lokasi:</p>

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
                <td class="text-bold">{{ $retur->nama_material ?? '-' }}</td>
                <td class="text-center text-bold text-red">{{ $retur->jumlah_rusak ?? 0 }}</td>
                <td style="font-style: italic; color: #555;">
                    "{{ $retur->catatan ?? 'Tidak ada catatan' }}"
                </td>
              <td class="text-center">
    @php
        $imgBase64 = null;
        $pathFile = 'Path belum diproses';
        $dbValue = $retur->foto_bukti_rusak ?? 'KOSONG';
        
        if (!empty($retur->foto_bukti_rusak)) {
            // Kita cek apa yang terjadi di sini
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
        {{-- Pesan Error Pelacak --}}
        <div style="font-size: 9px; color: red; text-align: left; word-break: break-all; border: 1px solid red; padding: 3px;">
            <strong>ERROR TRACKER:</strong><br>
            1. Isi Database: {{ $dbValue }}<br>
            2. Mencari di: {{ $pathFile }}<br>
            3. Hasil Cek: {{ file_exists($pathFile) ? 'File ADA tapi gagal diconvert' : 'File TIDAK DITEMUKAN di laptop/server' }}
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
        <p style="margin-top: 60px; text-decoration: underline;"><strong>Petugas Gudang / Penerima</strong></p>
    </div>

</body>
</html>