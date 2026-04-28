<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $kontrak->nomor_kontrak }}</title>
    <style>
        /* Menggunakan style yang sama dengan pdf-rfq */
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            color: #333; 
        }

        /* --- STYLING KOP SURAT (Identik dengan RFQ) --- */
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
        .kop-surat td.logo img { 
            max-width: 80px; 
            height: auto; 
        }
        .kop-surat td.info { 
            width: 70%; 
            text-align: center; 
            vertical-align: middle; 
        }
        .kop-surat h1 { 
            margin: 0; 
            font-size: 22px; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #1e3a8a; 
            letter-spacing: 1px;
        }
        .kop-surat p { 
            margin: 3px 0; 
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

        /* --- INFO SUPPLIER & TRANSAKSI --- */
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { vertical-align: top; padding: 4px; }
        .section-title { font-weight: bold; text-decoration: underline; }

        /* --- STYLING TABEL BARANG (Meniru RFQ) --- */
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th, .item-table td { 
            border: 1px solid #000; 
            padding: 8px; 
            text-align: left; 
        }
        .item-table th { 
            background-color: #f4f4f4; /* Abu-abu terang seperti RFQ */
            text-align: center; 
            text-transform: uppercase;
            font-size: 11px;
        }

        /* --- RINGKASAN HARGA --- */
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 5px; border: 1px solid #eee; }
        .total-row { background-color: #f8fafc; font-weight: bold; font-size: 13px; border-top: 2px solid #000 !important; }

        /* --- FOOTER --- */
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
                    <img src="{{ public_path('logo.png') }}" alt="Logo Perusahaan">
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
        <h2>PURCHASE ORDER (PO)</h2>
        <p style="margin-top: 5px; font-weight: bold;">No: {{ $kontrak->nomor_kontrak }}</p>
    </div>

    {{-- INFO SUPPLIER --}}
    <table class="info-table">
        <tr>
            <td width="55%">
                <span class="section-title">KEPADA SUPPLIER:</span><br>
                <strong>{{ $kontrak->supplier->nama_supplier }}</strong><br>
                {{ $kontrak->supplier->alamat_supplier }}<br>
                Telp: {{ $kontrak->supplier->no_telepon }}
            </td>
            <td width="45%">
                <table width="100%">
                    <tr>
                        <td width="45%"><strong>Tanggal PO</strong></td>
                        <td>: {{ \Carbon\Carbon::parse($kontrak->tanggal_kontrak)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Mata Uang</strong></td>
                        <td>: IDR (Rupiah)</td>
                    </tr>
                    {{-- Metode Bayar sudah dihapus dari sini --}}
                </table>
            </td>
        </tr>
    </table>

    {{-- TABEL ITEM --}}
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
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- RINGKASAN & KETERANGAN --}}
    <table width="100%">
        <tr>
            <td width="50%" style="vertical-align: top; padding-right: 20px;">
                <p><strong>Keterangan:</strong><br>
                {{ $kontrak->keterangan ?? 'Barang dikirim sesuai dengan jadwal yang disepakati.' }}</p>
                
                <div style="margin-top: 15px; font-size: 10px; color: #555;">
                    <strong>Syarat & Ketentuan:</strong>
                    <ul style="margin-top: 5px; padding-left: 15px;">
                        <li>Tagihan harus melampirkan salinan Purchase Order resmi ini.</li>
                        <li>Barang yang dikirim harus sesuai spesifikasi dan dalam kondisi baru.</li>
                        {{-- Metode pembayaran di list syarat juga sudah dihapus --}}
                    </ul>
                </div>
            </td>
            <td width="50%">
                <table class="summary-table">
                    <tr>
                        <td>Total Item</td>
                        <td style="text-align: right;">Rp {{ number_format($kontrak->detailKontrak->sum(function($item) { return $item->jumlah_final * $item->harga_negosiasi_satuan; }), 0, ',', '.') }}</td>
                    </tr>
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

    {{-- FOOTER TTD --}}
    <div class="footer-container">
        <table width="100%">
            <tr>
                <td width="40%" class="signature-box">
                    <p>Penerima (Supplier),</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">( ____________________ )</p>
                    <p style="font-size: 10px;">Stempel & Tanggal</p>
                </td>
                <td width="20%"></td>
                <td width="40%" class="signature-box">
                            <p>Yogyakarta, {{ \Carbon\Carbon::parse($kontrak->updated_at)->translatedFormat('d F Y') }}</p>
                    <p>Hormat Kami,</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ auth()->user()->nama_lengkap }}</p>
                    <p>{{ auth()->user()->ROLE ?? 'Tim Pengadaan' }}</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>