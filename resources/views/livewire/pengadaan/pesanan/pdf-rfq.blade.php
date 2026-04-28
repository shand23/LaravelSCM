<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Request for Quotation - {{ $pesanan->nomor_pesanan }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            color: #333; 
        }

        /* --- STYLING KOP SURAT --- */
        .kop-surat { 
            width: 100%; 
            margin-bottom: 20px; 
            /* Garis bawah tebal ganda khas kop surat resmi */
            border-bottom: 3px double #000; 
            padding-bottom: 10px; 
        }
        .kop-surat table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .kop-surat td.logo { 
            width: 15%; 
            text-align: center; 
            vertical-align: middle; 
        }
        /* Pastikan ukuran logo pas, tidak terlalu besar */
        .kop-surat td.logo img { 
            max-width: 80px; 
            height: auto; 
        }
        .kop-surat td.info { 
            width: 70%; 
            text-align: center; 
            vertical-align: middle; 
        }
        .kop-surat td.spacer {
            width: 15%; /* Penyeimbang agar teks benar-benar di tengah */
        }
        .kop-surat h1 { 
            margin: 0; 
            font-size: 22px; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #1e3a8a; /* Warna biru gelap profesional */
            letter-spacing: 1px;
        }
        .kop-surat p { 
            margin: 3px 0; 
            font-size: 11px; 
            color: #444;
        }

        /* --- STYLING JUDUL DOKUMEN --- */
        .title-doc { 
            text-align: center; 
            margin-bottom: 25px; 
        }
        .title-doc h2 { 
            margin: 0; 
            font-size: 16px; 
            text-decoration: underline; 
            text-transform: uppercase; 
        }
        .title-doc p { 
            margin: 5px 0 0 0; 
            font-weight: bold; 
        }

        /* --- STYLING TABEL & KONTEN --- */
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px; vertical-align: top; }
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th, .item-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .item-table th { background-color: #f4f4f4; text-align: center; }
        
        .footer { margin-top: 40px; text-align: right; padding-right: 30px; }
        .signature { margin-top: 60px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="kop-surat">
        <table>
            <tr>
                <td class="logo">
                    {{-- CATATAN: Gunakan public_path() agar library PDF bisa membaca file lokal --}}
                    <img src="{{ public_path('logo.png') }}" alt="Logo Perusahaan">
                </td>
                <td class="info">
                    <h1>PT. SWEVEL UNIVERSAL MEDIA</h1>
                    <p>Jl. Mijil No.98, Karangjati, Sinduadi, Kec. Mlati,</p>
                    <p>Kabupaten Sleman, Daerah Istimewa Yogyakarta 55284</p>
                    <p>Telp: (0274) 511067</p>

                </td>
                <td class="spacer"></td>
            </tr>
        </table>
    </div>

    {{-- JUDUL DOKUMEN --}}
    <div class="title-doc">
        <h2>Request for Quotation (RFQ)</h2>
        <p>No. Dokumen: {{ $pesanan->nomor_pesanan }}</p>
    </div>

    {{-- INFORMASI SUPPLIER --}}
    <table class="info-table">
        <tr>
            <td width="15%"><strong>Kepada</strong></td>
            <td width="35%">: {{ $pesanan->supplier->nama_supplier ?? '-' }}</td>
            <td width="20%"><strong>Tanggal Dibuat</strong></td>
            <td width="30%">: {{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Kontak</strong></td>
            <td>: {{ $pesanan->supplier->no_telepon ?? '-' }}</td>
            <td><strong>Referensi PR</strong></td>
            <td>: {{ $pesanan->id_pengajuan }}</td>
        </tr>
    </table>

    <p style="margin-bottom: 15px; line-height: 1.5;">
        Dengan hormat,<br>
        Bersama dokumen ini, kami memohon penawaran harga (<i>quotation</i>) untuk kebutuhan material berikut:
    </p>

    {{-- TABEL BARANG --}}
    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Nama Material</th>
                <th width="20%">Qty Dibutuhkan</th>
                <th width="30%">Harga Penawaran (Diisi Supplier)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan->detailPesanan as $index => $detail)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $detail->material->nama_material }}</td>
                <td style="text-align: center;">{{ $detail->jumlah_pesan }} {{ $detail->material->satuan }}</td>
                <td></td> {{-- Dikosongkan agar bisa diisi oleh supplier --}}
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 11px; color: #555; line-height: 1.4;">
        <em><strong>Catatan:</strong><br>
        * Mohon berikan penawaran harga terbaik Anda dengan mengisi kolom harga di atas atau melampirkan surat penawaran resmi dari perusahaan Anda.<br>
        * Harga yang ditawarkan mohon agar sudah mempertimbangkan ongkos kirim (jika ada) dan PPN.</em>
    </p>

    {{-- FOOTER / TTD --}}
    <div class="footer">
                            <p>Yogyakarta, {{ \Carbon\Carbon::parse($pesanan->updated_at)->translatedFormat('d F Y') }}</p>

        <p>Hormat Kami,</p>
        <div class="signature" style="margin-top: 50px; font-weight: bold; text-decoration: underline;">
            {{ auth()->user()->nama_lengkap }}
        </div>
        <p style="margin-top: 5px; color: #666;">
            {{ auth()->user()->ROLE }}
        </p>
    </div>

</body>
</html>