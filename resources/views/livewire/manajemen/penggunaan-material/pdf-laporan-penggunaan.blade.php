<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penggunaan Material</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px; 
            color: #333; 
            line-height: 1.5;
        }

        /* --- STYLING KOP SURAT (Sesuai Invoice) --- */
        .kop-surat { 
            width: 100%; 
            margin-bottom: 20px; 
            border-bottom: 3px double #000; 
            padding-bottom: 10px; 
        }
        .kop-surat table { width: 100%; border-collapse: collapse; }
        .kop-surat td.logo { width: 15%; text-align: center; vertical-align: middle; }
        .kop-surat td.info { width: 85%; text-align: center; vertical-align: middle; }
        .kop-surat h1 { margin: 0; font-size: 18px; font-weight: bold; text-transform: uppercase; color: #1e3a8a; }
        .kop-surat p { margin: 2px 0; font-size: 10px; }

        /* --- SECTION TITLE (Sesuai Invoice) --- */
        .section-title { 
            background: #f3f4f6; 
            padding: 6px 10px; 
            font-weight: bold; 
            border-left: 4px solid #1d4ed8; 
            margin: 20px 0 10px 0;
            text-transform: uppercase;
            color: #1e3a8a;
        }

        /* --- STAT CARDS (Menggunakan Table agar tidak corrupt) --- */
        .stat-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .stat-card { border: 1px solid #ddd; padding: 10px; text-align: center; background: #ffffff; }
        .stat-value { font-size: 16px; font-weight: bold; color: #1d4ed8; }
        .stat-label { font-size: 9px; color: #666; text-transform: uppercase; margin-bottom: 4px; }

        /* --- TABLE STYLE (Sesuai Invoice) --- */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.data-table th { 
            background: #1d4ed8; 
            color: white; 
            padding: 8px; 
            border: 1px solid #ddd; 
            text-align: left; 
            text-transform: uppercase;
            font-size: 10px;
        }
        table.data-table td { 
            padding: 8px; 
            border: 1px solid #ddd; 
            vertical-align: top; 
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* --- FOOTER TTD --- */
        .footer-container { margin-top: 30px; }
        .signature-box { text-align: center; }
    </style>
</head>
<body>
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
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin:0; text-transform: uppercase; font-size: 14px;">Laporan Monitoring Penggunaan Material</h2>
        @if($filterStartDate && $filterEndDate)
            <p style="margin:5px 0 0 0;">Periode: {{ $filterStartDate }} s/d {{ $filterEndDate }}</p>
        @endif
    </div>

    <div class="section-title">I. Ringkasan Statistik Penggunaan</div>
    <table class="stat-table">
        <tr>
            <td class="stat-card" width="33%">
                <div class="stat-label">Total Terpasang</div>
                <div class="stat-value">{{ number_format($totalTerpasang) }}</div>
            </td>
            <td class="stat-card" width="33%">
                <div class="stat-label">Total Rusak</div>
                <div class="stat-value" style="color: #ef4444;">{{ number_format($totalRusak) }}</div>
            </td>
            <td class="stat-card" width="33%">
                <div class="stat-label">Total Sisa</div>
                <div class="stat-value" style="color: #f59e0b;">{{ number_format($totalSisa) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">II. Tren Penggunaan Bulanan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">Terpasang</th>
                <th class="text-right">Rusak</th>
                <th class="text-right">Sisa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trenBulan as $t)
            <tr>
                <td>{{ $t->bulan }}</td>
                <td class="text-right">{{ number_format($t->total_terpasang) }}</td>
                <td class="text-right">{{ number_format($t->total_rusak) }}</td>
                <td class="text-right">{{ number_format($t->total_sisa) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">III. Top 5 Kategori Material Terpasang</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Kategori Material</th>
                <th class="text-right">Jumlah Terpasang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kategoriData as $k)
            <tr>
                <td><strong>{{ $k->nama_kategori }}</strong></td>
                <td class="text-right">{{ number_format($k->total_terpasang) }} Unit</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">IV. Daftar Rincian Laporan Penggunaan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">Tgl Laporan</th>
                <th width="25%">Proyek</th>
                <th width="25%">Pelaksana</th>
                <th width="35%">Area Pekerjaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listLaporan as $lp)
            <tr>
                <td class="text-center">{{ \Carbon\Carbon::parse($lp->tanggal_laporan)->format('d/m/Y') }}</td>
                <td>{{ $lp->proyek->nama_proyek ?? '-' }}</td>
                <td>{{ $lp->pelaksana->nama_lengkap ?? '-' }}</td>
                <td>{{ $lp->area_pekerjaan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-container">
        <table width="100%">
            <tr>
                <td width="65%"></td>
                <td width="35%" class="signature-box">
                    <p>Yogyakarta, {{ now()->translatedFormat('d F Y') }}</p>
                    <p style="margin-bottom: 60px;">Dicetak Oleh,</p>
                    <p><strong>( {{ Auth::user()->nama_lengkap }} )</strong></p>
                    <p style="font-size: 9px; color: #666;">Manajemen Operasional</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>