<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->id_invoice }}</title>
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
        .kop-surat table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .kop-surat td.logo { 
            width: 15%; 
            text-align: center; 
            vertical-align: middle; 
        }
        .kop-surat td.info { 
            width: 70%; 
            text-align: center; 
            vertical-align: middle; 
        }
        .kop-surat h1 { 
            margin: 0; 
            font-size: 20px; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #1e3a8a; 
        }
        .kop-surat p { 
            margin: 2px 0; 
            font-size: 11px; 
            color: #444;
        }

        /* --- JUDUL DOKUMEN --- */
        .title-plain { 
            text-align: center; 
            margin: 20px 0; 
        }
        .title-plain h2 { 
            margin: 0; 
            font-size: 18px; 
            text-decoration: underline; 
            text-transform: uppercase; 
        }

        /* --- INFO TRANSAKSI --- */
        .section-title { 
            font-weight: bold; 
            text-decoration: underline; 
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
        }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { vertical-align: top; padding: 4px; }

        /* --- STYLING TABEL BARANG --- */
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th, .item-table td { 
            border: 1px solid #000; 
            padding: 8px; 
        }
        .item-table th { 
            background-color: #f4f4f4; 
            text-align: center; 
            text-transform: uppercase;
            font-size: 11px;
        }

        /* --- LAMPIRAN FOTO --- */
        .photo-container { 
            margin-top: 20px; 
            text-align: center; 
            border: 1px dashed #ccc;
            padding: 15px;
        }
        .photo-container img { 
            max-width: 100%; 
            height: auto; 
            max-height: 450px; 
        }

        /* --- FOOTER TTD --- */
        .footer-container { margin-top: 30px; width: 100%; }
        .signature-box { text-align: center; }
        .signature-space { height: 60px; }
        .signature-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; }

        .page-break { page-break-after: always; }
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
                        $base64 = $data ? 'data:image/' . $type . ';base64,' . base64_encode($data) : '';
                    @endphp
                    @if($base64)
                        <img src="{{ $base64 }}" alt="Logo" style="max-width: 80px;">
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
        <h2>LAPORAN PEMBAYARAN INVOICE</h2>
        <p style="margin-top: 5px; font-weight: bold;">No. Invoice: {{ $invoice->id_invoice }}</p>
    </div>

    {{-- I. INFORMASI TRANSAKSI --}}
    <span class="section-title">I. INFORMASI TRANSAKSI</span>
    <table class="info-table">
        <tr>
            <td width="55%">
                <strong>DIBAYARKAN KEPADA:</strong><br>
                {{ $invoice->kontrak->supplier->nama_supplier }}<br>
                {{ $invoice->kontrak->supplier->alamat_supplier }}<br>
                Telp: {{ $invoice->kontrak->supplier->no_telepon }}
            </td>
            <td width="45%">
                <table width="100%">
<tr>
                        <td width="40%"><strong>Tgl. Invoice</strong></td>
                        <td>: {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jatuh Tempo</strong></td>
                        <td>: {{ \Carbon\Carbon::parse($invoice->jatuh_tempo)->translatedFormat('d F Y') }}</td>
                    </tr>                    <tr>
                        <td><strong>No. Kontrak</strong></td>
                        <td>: {{ $invoice->kontrak->nomor_kontrak }}</td>
                    </tr>
                    {{-- Status Dihapus Sesuai Permintaan --}}
                </table>
            </td>
        </tr>
    </table>

    {{-- II. RINCIAN ITEM --}}
    <span class="section-title">II. RINCIAN MATERIAL</span>
    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Nama Material</th>
                <th width="10%">Qty</th>
                <th width="15%">Harga Satuan</th>
                <th width="25%">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->kontrak->detailKontrak as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->material->nama_material }}</td>
                <td style="text-align: center;">{{ $item->jumlah_final }} {{ $item->material->satuan }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->harga_negosiasi_satuan, 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->jumlah_final * $item->harga_negosiasi_satuan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold; background-color: #f9fafb;">TOTAL TAGIHAN</td>
                <td style="text-align: right; font-weight: bold; background-color: #f9fafb;">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- III. LAMPIRAN BUKTI --}}
    @if($imageData || $fileType === 'pdf')
    <div class="page-break"></div>
    <span class="section-title">III. LAMPIRAN BUKTI FISIK INVOICE</span>
    <div class="photo-container">
        @if($imageData)
            <p style="margin-bottom: 10px;">Foto Bukti Invoice Supplier:</p>
            <img src="{{ $imageData }}">
        @elseif($fileType === 'pdf')
            <p style="color: #1e3a8a; font-style: italic; margin-top: 50px;">
                [ Bukti pembayaran terlampir dalam format PDF di dalam sistem SCM. ]
            </p>
        @endif
    </div>
    @endif

    {{-- FOOTER TTD (HANYA SATU DI SEBELAH KANAN) --}}
    <div class="footer-container">
        <table width="100%">
            <tr>
                {{-- Kolom kosong untuk mendorong TTD ke kanan --}}
                <td width="65%"></td>
                
                <td width="35%" class="signature-box">
<p>Yogyakarta, {{ \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('d F Y') }}</p>                    <p>Dibuat Oleh,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ Auth::user()->nama_lengkap ?? Auth::user()->name }}</p>
                    <p>Staf Pengadaan</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>