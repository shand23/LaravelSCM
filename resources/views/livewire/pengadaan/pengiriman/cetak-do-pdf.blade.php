<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - DO-{{ $pengiriman->id_pengiriman }}</title>
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
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .item-table th, .item-table td { border: 1px solid #000; padding: 8px; }
        .item-table th { background-color: #f4f4f4; text-align: center; text-transform: uppercase; font-size: 11px; }

        /* --- FOOTER TTD --- */
        .footer-container { margin-top: 40px; width: 100%; }
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
        <h2>SURAT JALAN (DELIVERY ORDER)</h2>
        <p style="margin-top: 5px; font-weight: bold;">ID: {{ $pengiriman->id_pengiriman }}</p>
    </div>

    {{-- I. INFORMASI PENGIRIMAN --}}
    <span class="section-title">I. INFORMASI PENGIRIMAN</span>
    <table class="info-table">
        <tr>
            <td width="55%">
                <strong>DARI SUPPLIER / VENDOR:</strong><br>
                {{ $pengiriman->kontrak->supplier->nama_supplier ?? 'N/A' }}<br>
                {{ $pengiriman->kontrak->supplier->alamat_supplier ?? '-' }}<br>
                Telp: {{ $pengiriman->kontrak->supplier->no_telepon ?? '-' }}
            </td>
            <td width="45%">
                <table width="100%">
                    <tr>
                        <td width="45%"><strong>No. PO/Kontrak</strong></td>
                        <td>: {{ $pengiriman->id_kontrak ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tgl. Kirim</strong></td>
                        <td>: {{ \Carbon\Carbon::parse($pengiriman->tanggal_berangkat)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Estimasi Tiba</strong></td>
                        <td>: {{ $pengiriman->estimasi_tanggal_tiba ? \Carbon\Carbon::parse($pengiriman->estimasi_tanggal_tiba)->translatedFormat('d F Y') : '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- II. RINCIAN MATERIAL --}}
    <span class="section-title">II. RINCIAN MATERIAL</span>
    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Nama Material</th>
                <th width="20%">Satuan</th>
                <th width="30%">Jumlah Dikirim</th>
            </tr>
        </thead>
        <tbody>
            {{-- Menggunakan safety guard ?? [] agar tidak error jika data kosong --}}
            @forelse($pengiriman->detailPengiriman ?? [] as $index => $detail)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $detail->detailKontrak->material->nama_material ?? 'Material Tidak Ditemukan' }}</td>
                <td style="text-align: center;">{{ $detail->detailKontrak->material->satuan ?? '-' }}</td>
                <td style="text-align: right;"><strong>{{ number_format($detail->jumlah_dikirim, 0, ',', '.') }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #777; font-style: italic;">
                    Tidak ada detail barang untuk pengiriman ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER TTD (3 KOLOM UNTUK SURAT JALAN) --}}
    <div class="footer-container">
        <table width="100%">
            <tr>
                <td width="33%" class="signature-box">
                    <p>Dibuat Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ Auth::user()->nama_lengkap ?? Auth::user()->name }}</p>
                    <p>Staf Pengadaan</p>
                </td>
                <td width="33%" class="signature-box">
                    <p>Pihak Pengirim (Vendor / Sopir),</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">( ____________________ )</p>
                    <p>Tanda Tangan & Nama Terang</p>
                </td>
                <td width="33%" class="signature-box">
                    <p>Pihak Penerima (Logistik),</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">( ____________________ )</p>
                    <p>Tanda Tangan & Stempel</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>