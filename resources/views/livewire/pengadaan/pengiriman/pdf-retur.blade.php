<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Retur - DO-{{ $doRetur->id_pengiriman ?? '-' }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            color: #333; 
            line-height: 1.5;
        }

        /* --- STYLING KOP SURAT --- */
        .kop-surat { 
            width: 100%; 
            margin-bottom: 20px; 
            border-bottom: 3px double #000; 
            padding-bottom: 10px; 
        }
        .kop-surat table { width: 100%; border-collapse: collapse; }
        .kop-surat td.logo { width: 15%; text-align: center; vertical-align: middle; }
        .kop-surat td.info { width: 70%; text-align: center; vertical-align: middle; }
        .kop-surat h1 { margin: 0; font-size: 20px; font-weight: bold; text-transform: uppercase; color: #1e3a8a; }
        .kop-surat p { margin: 2px 0; font-size: 11px; color: #444; }

        /* --- JUDUL DOKUMEN --- */
        .title-plain { text-align: center; margin: 20px 0; }
        .title-plain h2 { margin: 0; font-size: 18px; text-decoration: underline; text-transform: uppercase; }

        /* --- INFO TRANSAKSI --- */
        .section-title { font-weight: bold; text-decoration: underline; margin-bottom: 8px; display: block; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { vertical-align: top; padding: 4px; }

        /* --- STYLING TABEL BARANG --- */
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th, .item-table td { border: 1px solid #000; padding: 8px; }
        .item-table th { background-color: #f4f4f4; text-align: center; text-transform: uppercase; font-size: 11px; }

        .img-bukti { 
            max-width: 120px; 
            height: auto; 
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 2px;
        }

        /* --- FOOTER TTD --- */
        .footer-container { margin-top: 30px; width: 100%; }
        .signature-box { text-align: center; }
        .signature-space { height: 65px; }
        .signature-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="kop-surat">
        <table>
            <tr>
                <td class="logo">
                    @php
                        $path = public_path('logo.png');
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = @file_get_contents($path);
                        $base64Logo = $data ? 'data:image/' . $type . ';base64,' . base64_encode($data) : '';
                    @endphp
                    @if($base64Logo)
                        <img src="{{ $base64Logo }}" alt="Logo" style="max-width: 80px;">
                    @else
                        <div style="font-weight: bold; color: red;">LOGO</div>
                    @endif
                </td>
                <td class="info">
                    <h1>PT. SWEVEL UNIVERSAL MEDIA</h1>
                    <p>Jl. Mijil No.98, Karangjati, Sinduadi, Kec. Mlati,</p>
                    <p>Kabupaten Sleman, Daerah Istimewa Yogyakarta 55284</p>
                    <p>Telp: (0274) 511067</p>
                </td>
                <td style="width: 15%;"></td>
            </tr>
        </table>
    </div>

    {{-- JUDUL DOKUMEN --}}
    <div class="title-plain">
        <h2>LAPORAN BARANG RUSAK / DITOLAK</h2>
        <p style="margin-top: 5px; font-weight: bold;">Ref: DO-{{ $doRetur->id_pengiriman ?? '-' }}</p>
    </div>

    {{-- I. INFORMASI PENGIRIMAN --}}
    <span class="section-title">I. INFORMASI PENGIRIMAN</span>
    <table class="info-table">
        <tr>
            <td width="55%">
                <table width="100%">
                    <tr>
                        <td width="35%"><strong>ID Pengiriman</strong></td>
                        <td>: {{ $doRetur->id_pengiriman ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nomor PO</strong></td>
                        <td>: {{ $doRetur->kontrak->nomor_kontrak ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td width="45%">
                <table width="100%">
                    <tr>
                        <td width="45%"><strong>Tgl. Status Retur</strong></td>
                        <td>: {{ \Carbon\Carbon::parse($doRetur->updated_at)->translatedFormat('d F Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="color: #333; margin-bottom: 15px; font-style: italic;">
        *Berikut adalah rincian material yang dilaporkan rusak atau tidak sesuai saat penerimaan di lokasi per tanggal {{ \Carbon\Carbon::parse($doRetur->updated_at)->translatedFormat('d F Y') }}:
    </p>

    {{-- II. RINCIAN MATERIAL RUSAK --}}
    <span class="section-title">II. RINCIAN MATERIAL</span>
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
            @forelse($dataDetailRetur ?? [] as $index => $retur)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td><strong>{{ $retur->nama_material ?? '-' }}</strong></td>
                <td style="text-align: center;"><strong>{{ $retur->jumlah_rusak ?? 0 }}</strong></td>
                <td style="font-style: italic;">
                    "{{ $retur->alasan_return ?? 'Tidak ada catatan' }}"
                </td>
                <td style="text-align: center;">
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
                        <img src="{{ $imgBase64 }}" class="img-bukti">
                    @else
                        <span style="font-size: 10px; color: #999; font-style: italic;">Tanpa Lampiran</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; color: #777;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER TTD (TANGGAL BERDASARKAN WAKTU STATUS BERUBAH) --}}
    <div class="footer-container">
        <table width="100%">
            <tr>
                <td width="65%"></td>
                <td width="35%" class="signature-box">
                    {{-- Tanggal di sini menggunakan waktu database (updated_at) --}}
                    <p>Yogyakarta, {{ \Carbon\Carbon::parse($doRetur->updated_at)->translatedFormat('d F Y') }}</p>
                    <p>Dibuat Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'Petugas Gudang' }}</p>
                    <p>Staf Logistik / Penerima</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>