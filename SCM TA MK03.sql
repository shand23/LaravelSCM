CREATE DATABASE scm_konstruksi_ta;
USE scm_konstruksi_ta;

CREATE TABLE users (
    id_user VARCHAR(20) PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    PASSWORD VARCHAR(255) NOT NULL,
    ROLE ENUM('Admin', 'Tim Pengadaan', 'Tim Pelaksanaan','Logistik', 'Top Manajemen') NOT NULL,
    jabatan VARCHAR(100),
    status_user ENUM('Aktif','Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
INSERT INTO users (id_user, nama_lengkap, email, PASSWORD, ROLE, jabatan, status_user, created_at, updated_at) VALUES
('USR0002', 'Budi Manajer', 'pm@scm.com', '$2y$12$W99GOiql83X4AqawXzqjSerCmE84DJtbhnEFsl4x7zH...', 'Manajer Proyek', 'Project Manager', 'Aktif', NOW(), NOW());

CREATE TABLE proyek (
    id_proyek VARCHAR(20) PRIMARY KEY,
    nama_proyek VARCHAR(150) NOT NULL,
    lokasi_proyek VARCHAR(150),
    deskripsi_proyek TEXT,
    tanggal_mulai DATE,
    tanggal_selesai DATE,
    status_proyek ENUM('Aktif','Selesai','Ditunda') DEFAULT 'Aktif',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE supplier (
    id_supplier VARCHAR(20) PRIMARY KEY,
    nama_supplier VARCHAR(150) NOT NULL,
    alamat TEXT,
    kota VARCHAR(100),
    kontak_person VARCHAR(100),
    no_telepon VARCHAR(20),
    email VARCHAR(100),
    status_supplier ENUM('Aktif','Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE kategori_material (
    id_kategori_material VARCHAR(20) PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    status_kategori ENUM('Aktif','Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE material (
    id_material VARCHAR(20) PRIMARY KEY,
    id_kategori_material VARCHAR(20),
    nama_material VARCHAR(150) NOT NULL,
    satuan VARCHAR(50) NOT NULL,
    spesifikasi TEXT,
    standar_kualitas VARCHAR(100),
    status_material ENUM('Aktif','Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_kategori_material) REFERENCES kategori_material(id_kategori_material)
);

CREATE TABLE penugasan_proyek (
    id_penugasan VARCHAR(20) PRIMARY KEY,
    id_user VARCHAR(20) NOT NULL,
    id_proyek VARCHAR(20) NOT NULL,
    peran_proyek VARCHAR(100), 
    tanggal_mulai DATE,
    tanggal_selesai DATE,
    status_penugasan ENUM('Aktif','Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_proyek) REFERENCES proyek(id_proyek) ON DELETE CASCADE
);

-- ==========================================
-- JALUR A: PERMINTAAN DARI LAPANGAN (PROYEK KE GUDANG)
-- ==========================================

CREATE TABLE permintaan_proyek (
    id_permintaan VARCHAR(20) PRIMARY KEY,
    id_proyek VARCHAR(20) NOT NULL,
    id_user VARCHAR(20) NOT NULL,
    tanggal_permintaan DATE NOT NULL,
    status_permintaan ENUM('Menunggu Persetujuan', 'Disetujui PM', 'Diproses Sebagian', 'Selesai', 'Ditolak') DEFAULT 'Menunggu Persetujuan',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_proyek) REFERENCES proyek(id_proyek),
     FOREIGN KEY (id_user) REFERENCES users(id_user)
);

CREATE TABLE detail_permintaan_proyek (
    id_detail_permintaan VARCHAR(20) PRIMARY KEY,
    id_permintaan VARCHAR(20) NOT NULL,
    id_material VARCHAR(20) NOT NULL,
    jumlah_diminta INT NOT NULL, 
    jumlah_terkirim INT DEFAULT 0, 
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_permintaan) REFERENCES permintaan_proyek(id_permintaan) ON DELETE CASCADE,
    FOREIGN KEY (id_material) REFERENCES material(id_material)
);



CREATE TABLE pengajuan_pembelian (
    id_pengajuan VARCHAR(20) PRIMARY KEY,
    id_user_logistik VARCHAR(20) NOT NULL,
    referensi_id_permintaan VARCHAR(20) NULL, -- Jika null berarti murni restock gudang
    tanggal_pengajuan DATE NOT NULL,
    status_pengajuan ENUM('Menunggu Pengadaan', 'Proses RFQ', 'PO Dibuat', 'Selesai') DEFAULT 'Menunggu Pengadaan',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_user_logistik) REFERENCES users(id_user),
    FOREIGN KEY (referensi_id_permintaan) REFERENCES permintaan_proyek(id_permintaan)
);

CREATE TABLE detail_pengajuan_pembelian (
    id_detail_pengajuan VARCHAR(20) PRIMARY KEY,
    id_pengajuan VARCHAR(20) NOT NULL,
    id_material VARCHAR(20) NOT NULL,
    jumlah_minta_beli INT NOT NULL, 
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_pengajuan) REFERENCES pengajuan_pembelian(id_pengajuan) ON DELETE CASCADE,
    FOREIGN KEY (id_material) REFERENCES material(id_material)
);


-- ==========================================
-- 1. TABEL PESANAN (RFQ / PERMINTAAN PENAWARAN)
-- ==========================================

CREATE TABLE pesanan (
    id_pesanan VARCHAR(20) PRIMARY KEY,
    id_pengajuan VARCHAR(20) NOT NULL,
    id_supplier VARCHAR(20) NOT NULL,
    id_user_pengadaan VARCHAR(20) NOT NULL,
    nomor_pesanan VARCHAR(100) UNIQUE, -- Contoh: RFQ/2026/03/001
    tanggal_pesanan DATE NOT NULL,
    -- Status disederhanakan: Draft -> Proses Negosiasi -> Berlanjut/Batal
    status_pesanan ENUM('Draft', 'Proses Negosiasi', 'Dibatalkan', 'Berlanjut ke Kontrak') DEFAULT 'Draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_pengajuan) REFERENCES pengajuan_pembelian(id_pengajuan),
    FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier),
    FOREIGN KEY (id_user_pengadaan) REFERENCES users(id_user)
);

CREATE TABLE detail_pesanan (
    id_detail_pesanan VARCHAR(20) PRIMARY KEY,
    id_pesanan VARCHAR(20) NOT NULL,
    
    id_material VARCHAR(20) NOT NULL,
    jumlah_pesan INT NOT NULL, -- Hanya jumlah, tanpa harga estimasi
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
    
    FOREIGN KEY (id_material) REFERENCES material(id_material)
);

-- ==========================================
-- 2. TABEL KONTRAK (PO FIX / KESEPAKATAN HARGA)
-- ==========================================

-- ==============================================================================
-- 1. TABEL KONTRAK (PO)
-- Menyimpan data legalitas dokumen, harga final, pajak, dan status kesepakatan
-- ==============================================================================
CREATE TABLE kontrak (
    id_kontrak VARCHAR(20) PRIMARY KEY,
    id_pesanan VARCHAR(20) NOT NULL, 
    id_supplier VARCHAR(20) NOT NULL, 
    id_user_pengadaan VARCHAR(20) NOT NULL,
    nomor_kontrak VARCHAR(100) UNIQUE,
    file_kontrak_path VARCHAR(255), 
    tanggal_kontrak DATE,
    
    total_harga_awal DECIMAL(15,2),
    total_harga_negosiasi DECIMAL(15,2),
    total_diskon DECIMAL(15,2),
    total_ongkir DECIMAL(15,2),
    total_ppn DECIMAL(15,2),
    total_nilai_kontrak DECIMAL(15,2), 
    
    status_kontrak ENUM('Draft', 'Disepakati', 'Batal') DEFAULT 'Draft',
    status_pengiriman ENUM('Menunggu', 'Pengiriman', 'Return', 'Selesai') DEFAULT 'Menunggu',
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan),
    FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier),
    FOREIGN KEY (id_user_pengadaan) REFERENCES users(id_user)
);

-- ==============================================================================
-- 2. TABEL DETAIL KONTRAK
-- Menyimpan rincian material yang dibeli berdasarkan kontrak (harga satuan final)
-- ==============================================================================
CREATE TABLE detail_kontrak (
    id_detail_kontrak VARCHAR(20) PRIMARY KEY,
    id_kontrak VARCHAR(20) NOT NULL,
    id_material VARCHAR(20) NOT NULL,
    
    jumlah_final INT NOT NULL, 
    harga_negosiasi_satuan DECIMAL(15,2) NOT NULL,
    
    -- Tracking jumlah fisik yang sudah diterima Gudang sejauh ini
    jumlah_diterima INT DEFAULT 0, 
    catatan_penerimaan TEXT NULL, 
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_kontrak) REFERENCES kontrak(id_kontrak) ON DELETE CASCADE,
    FOREIGN KEY (id_material) REFERENCES material(id_material)
);

-- ==============================================================================
-- 3. TABEL PENGIRIMAN
-- Dicatat saat Supplier menginformasikan barang sedang dikirim (via Truk/Kurir)
-- ==============================================================================
-- 1. Buat tabel induk (Header Pengiriman)
CREATE TABLE pengiriman (
    id_pengiriman VARCHAR(50) PRIMARY KEY,
    id_kontrak VARCHAR(50) NOT NULL,
    id_user_pengadaan VARCHAR(50) NOT NULL,
    tanggal_berangkat DATE NOT NULL,
    estimasi_tanggal_tiba DATE NOT NULL,
    keterangan VARCHAR(255) NULL,
    status_pengiriman ENUM('Pending', 'Dalam Perjalanan', 'Tiba di Lokasi', 'Return & Kirim Ulang', 'Selesai') DEFAULT 'Pending',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Buat tabel anak (Detail Barang bawaan truk)
-- 2. Buat tabel baru dengan tipe data VARCHAR yang benar
CREATE TABLE pengiriman_detail (
    id_pengiriman_detail VARCHAR(50) PRIMARY KEY,
    id_pengiriman VARCHAR(50) NOT NULL,
    id_detail_kontrak VARCHAR(20) NOT NULL,
    jumlah_dikirim INT NOT NULL, -- (Ubah ke DECIMAL(10,2) jika material Anda bisa desimal seperti 1.5 Ton)
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Relasi ke tabel pengiriman (induk)
    CONSTRAINT fk_pd_pengiriman FOREIGN KEY (id_pengiriman) 
        REFERENCES pengiriman(id_pengiriman) ON DELETE CASCADE,

    -- [TAMBAHAN] Relasi ke tabel detail_kontrak agar data selaras
    CONSTRAINT fk_pd_detail_kontrak FOREIGN KEY (id_detail_kontrak) 
        REFERENCES detail_kontrak(id_detail_kontrak) ON DELETE CASCADE
);

-- ==============================================================================
-- 4. TABEL PENERIMAAN MATERIAL
-- Dicatat oleh Tim Logistik/Gudang saat barang benar-benar tiba & diinspeksi
-- ==============================================================================
CREATE TABLE penerimaan_material (
    id_penerimaan VARCHAR(50) PRIMARY KEY,
    id_pengiriman VARCHAR(50) NOT NULL, -- Disesuaikan jadi 50 agar sama dengan tabel pengiriman
    id_user_penerima VARCHAR(50) NOT NULL, -- Disesuaikan jadi 50
    tanggal_terima DATE NOT NULL,
    nomor_surat_jalan VARCHAR(100),
    
    -- Status dibuat lebih relevan dengan kondisi lapangan
    status_penerimaan ENUM('Diterima Penuh', 'Diterima Sebagian', 'Return') NOT NULL,
    
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_terima_pengiriman FOREIGN KEY (id_pengiriman) REFERENCES pengiriman(id_pengiriman)
    -- CONSTRAINT fk_terima_user FOREIGN KEY (id_user_penerima) REFERENCES users(id_user) -- Uncomment jika id_user ukurannya sama
);

-- ==============================================================================
-- 5. TABEL DETAIL PENERIMAAN
-- Rincian barang yang dicek secara fisik (menentukan mana yang bagus & rusak)
-- ==============================================================================
CREATE TABLE detail_penerimaan (
    id_detail_terima VARCHAR(50) PRIMARY KEY,
    id_penerimaan VARCHAR(50) NOT NULL,
    id_pengiriman_detail VARCHAR(50) NOT NULL, -- Diperbaiki: Hilangkan label PRIMARY KEY di sini
    id_detail_kontrak VARCHAR(20) NOT NULL,
    
    -- id_material dihapus karena sudah diwakilkan oleh id_detail_kontrak, 
    -- agar tidak terjadi redudansi data.
    
    jumlah_bagus INT DEFAULT 0,
    jumlah_rusak INT DEFAULT 0,
    alasan_return TEXT NULL,
    foto_bukti_rusak VARCHAR(255) NULL,
    
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_dp_penerimaan FOREIGN KEY (id_penerimaan) REFERENCES penerimaan_material(id_penerimaan) ON DELETE CASCADE,
    CONSTRAINT fk_dp_pengiriman_det FOREIGN KEY (id_pengiriman_detail) REFERENCES pengiriman_detail(id_pengiriman_detail),
    CONSTRAINT fk_dp_detail_kontrak FOREIGN KEY (id_detail_kontrak) REFERENCES detail_kontrak(id_detail_kontrak)
);

-- ==============================================================================
-- 6. TABEL STOK BATCH (FIFO)
-- Barang bagus yang lolos QC (Detail Penerimaan) akan masuk ke sini sebagai Stok
-- ==============================================================================
CREATE TABLE stok_batch_fifo (
    id_stok VARCHAR(20) PRIMARY KEY,
    id_material VARCHAR(20) NOT NULL,
    id_lokasi VARCHAR(20),
    id_penerimaan VARCHAR(20), 
    tanggal_masuk DATE NOT NULL, 
    
    jumlah_awal INT NOT NULL,
    sisa_stok INT NOT NULL,
    
    status_stok ENUM('Tersedia', 'Habis') DEFAULT 'Tersedia',
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_material) REFERENCES material(id_material),
    FOREIGN KEY (id_lokasi) REFERENCES master_lokasi_rak(id_lokasi),
    FOREIGN KEY (id_penerimaan) REFERENCES penerimaan_material(id_penerimaan)
);

CREATE TABLE pengeluaran_stok_fifo (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_permintaan VARCHAR(20) NOT NULL,
    id_material VARCHAR(20) NOT NULL,
    id_stok VARCHAR(20) NOT NULL,
    jumlah_diambil INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
 CREATE TABLE penyesuaian_stok (
    id_penyesuaian VARCHAR(20) PRIMARY KEY,
    id_stok VARCHAR(20) NOT NULL, -- Merujuk ke batch mana yang barangnya rusak
    id_material VARCHAR(20) NOT NULL,
    id_user VARCHAR(20) NOT NULL, -- Siapa admin gudang yang melaporkan/menyetujui
    
    jenis_penyesuaian ENUM('Rusak', 'Hilang', 'Kadaluarsa', 'Selisih Opname') DEFAULT 'Rusak',
    jumlah_penyesuaian INT NOT NULL, -- Jumlah yang dikurangkan dari stok
    
    keterangan TEXT NOT NULL, -- Alasan detail (misal: "Kardus basah karena atap bocor")
    bukti_foto VARCHAR(255) NULL, -- (Opsional) path foto barang rusak jika diperlukan
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_stok) REFERENCES stok_batch_fifo(id_stok),
    FOREIGN KEY (id_material) REFERENCES material(id_material),
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);
    
    -- Relasi (Foreign Keys)
    FOREIGN KEY (id_permintaan) REFERENCES permintaan_proyek(id_permintaan) ON DELETE CASCADE,
    FOREIGN KEY (id_material) REFERENCES material(id_material) ON DELETE CASCADE,
    FOREIGN KEY (id_stok) REFERENCES stok_batch_fifo(id_stok) ON DELETE CASCADE
);

CREATE TABLE master_lokasi_rak (
    id_lokasi VARCHAR(20) PRIMARY KEY,
    nama_lokasi VARCHAR(50) NOT NULL, -- Contoh: "Gudang Utama - Rak A1"
    AREA VARCHAR(50), -- Contoh: "Semen & Mortar"
    keterangan TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

INSERT IGNORE INTO master_lokasi_rak (id_lokasi, nama_lokasi, AREA, keterangan, created_at, updated_at) VALUES
('LOC0001', 'Gudang Utama - Rak A1', 'Semen & Pasir', 'Area penyimpanan semen dan pasir', NOW(), NOW()),
('LOC0002', 'Gudang Utama - Rak A2', 'Semen & Pasir', 'Area tambahan untuk semen dan mortar', NOW(), NOW()),
('LOC0003', 'Gudang Utama - Rak B1', 'Besi & Baja', 'Area penyimpanan besi tulangan dan baja', NOW(), NOW()),
('LOC0004', 'Gudang Utama - Rak C1', 'Kayu & Multiplek', 'Area penyimpanan kayu konstruksi dan multiplek', NOW(), NOW()),
('LOC0005', 'Gudang Utama - Rak D1', 'Batu & Bata', 'Area penyimpanan batu bata dan agregat kasar', NOW(), NOW()),
('LOC0006', 'Gudang Utama - Rak E1', 'Cat & Thinner', 'Area penyimpanan cat, thinner, dan bahan finishing', NOW(), NOW()),
('LOC0007', 'Gudang Utama - Rak F1', 'Pipa & Plumbing', 'Area penyimpanan pipa PVC, paralon, dan perlengkapan plumbing', NOW(), NOW()),
('LOC0008', 'Gudang Utama - Rak G1', 'Kabel & Elektrikal', 'Area penyimpanan kabel listrik dan komponen elektrikal', NOW(), NOW()),
('LOC0009', 'Gudang Utama - Rak H1', 'Keramik & Granit', 'Area penyimpanan keramik dan granit', NOW(), NOW()),
('LOC0010', 'Gudang Utama - Rak I1', 'Atap & Plafon', 'Area penyimpanan material atap dan plafon', NOW(), NOW()),
('LOC0011', 'Gudang Utama - Rak J1', 'Paku & Baut', 'Area penyimpanan paku, baut, dan pengikat', NOW(), NOW());

-- ==========================================
-- 4. PENGGUNAAN LAPANGAN (AD 5) & INVOICE
-- ==========================================

CREATE TABLE penggunaan_material (
    id_penggunaan VARCHAR(20) PRIMARY KEY,
    id_permintaan VARCHAR(20) NOT NULL, -- (BARU) Mengikat laporan ini ke permintaan mana
    id_proyek VARCHAR(20) NOT NULL,
    id_user_pelaksana VARCHAR(20) NOT NULL,
    tanggal_laporan DATE NOT NULL,
    area_pekerjaan VARCHAR(150), 
    keterangan_umum TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_permintaan) REFERENCES permintaan_proyek(id_permintaan), -- (BARU)
    FOREIGN KEY (id_proyek) REFERENCES proyek(id_proyek),
    FOREIGN KEY (id_user_pelaksana) REFERENCES users(id_user)
);

CREATE TABLE detail_penggunaan_material (
    id_detail_penggunaan VARCHAR(20) PRIMARY KEY,
    id_penggunaan VARCHAR(20) NOT NULL,
    id_material VARCHAR(20) NOT NULL,
    jumlah_terpasang_riil INT NOT NULL,
    jumlah_rusak_lapangan INT DEFAULT 0,
    jumlah_sisa_material INT DEFAULT 0,
    catatan_khusus TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_penggunaan) REFERENCES penggunaan_material(id_penggunaan) ON DELETE CASCADE,
    FOREIGN KEY (id_material) REFERENCES material(id_material)
);

CREATE TABLE invoice_pembelian (
    id_invoice VARCHAR(20) PRIMARY KEY,
    id_kontrak VARCHAR(20) NOT NULL, -- Relasi ke tabel PO/Kontrak
    nomor_invoice_supplier VARCHAR(100) NOT NULL, -- Nomor asli dari kertas invoice supplier
    tanggal_invoice DATE NOT NULL,
    jatuh_tempo DATE NOT NULL,
    total_tagihan DECIMAL(15,2) NOT NULL, -- Nominal yang harus dibayar
    status_invoice ENUM('Menunggu Pembayaran', 'Dibayar Sebagian', 'Lunas', 'Dibatalkan') DEFAULT 'Menunggu Pembayaran',
    file_invoice VARCHAR(255), -- Path untuk menyimpan file PDF/Gambar yang di-upload
    catatan TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_kontrak) REFERENCES kontrak(id_kontrak) ON DELETE RESTRICT
);

ALTER TABLE invoice_pembelian 
ADD COLUMN id_user VARCHAR(20) AFTER id_kontrak;

ALTER TABLE invoice_pembelian
ADD CONSTRAINT fk_invoice_user 
FOREIGN KEY (id_user) REFERENCES users(id_user) 
ON DELETE RESTRICT;

-- ==========================================
-- 5. PENGELOLAAN MAINTENANCE MATERIAL
-- ==========================================

Tabel Inventaris (Cek Gudang & FIFO)
CREATE TABLE penyesuaian_stok_rusak (
    id_penyesuaian VARCHAR(20) PRIMARY KEY,
    id_stok VARCHAR(20) NOT NULL, -- Merujuk ke batch FIFO tertentu
    id_user_logistik VARCHAR(20) NOT NULL,
    tanggal_penyesuaian DATE NOT NULL,
    jumlah_rusak_gudang INT NOT NULL, -- Jumlah yang akan memotong sisa_stok di FIFO
    alasan_kerusakan TEXT, -- Contoh: "Lembap", "Pecah Tertindih", "Kadaluarsa"
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_stok) REFERENCES stok_batch_fifo(id_stok) ON DELETE CASCADE,
    FOREIGN KEY (id_user_logistik) REFERENCES users(id_user)
);

CREATE TABLE alokasi_penggunaan_fifo (
    id_alokasi VARCHAR(20) PRIMARY KEY,
    id_detail_penggunaan VARCHAR(20) NOT NULL, -- Mengarah ke material yang dipakai
    id_stok VARCHAR(20) NOT NULL, -- Mengarah ke Batch FIFO yang dipotong
    jumlah_diambil INT NOT NULL, -- Berapa qty yang diambil dari batch ini
    created_at TIMESTAMP NULL,
    FOREIGN KEY (id_detail_penggunaan) REFERENCES detail_penggunaan_material(id_detail_penggunaan) ON DELETE CASCADE,
    FOREIGN KEY (id_stok) REFERENCES stok_batch_fifo(id_stok)
);


SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE penerimaan_material;
DROP TABLE pengiriman;
DROP TABLE kontrak;
DROP TABLE pesanan;
DROP TABLE pengajuan_material;

SET FOREIGN_KEY_CHECKS = 1;


-- ==========================================
-- 2. MASTER DATA SUPPLIER (5 Vendor Realistis)
-- ==========================================
INSERT IGNORE INTO supplier (id_supplier, nama_supplier, alamat, kota, kontak_person, no_telepon, email, status_supplier, created_at, updated_at) VALUES
('SUP0001', 'PT Bangun Jaya Indo', 'Jl. Industri No 1', 'Jakarta', 'Andi', '081234567890', 'sales@bangunjaya.com', 'Aktif', NOW(), NOW()),
('SUP0002', 'CV Baja Besi Kuat', 'Jl. Logam No 25', 'Tangerang', 'Bowo Kusumo', '081987654321', 'order@bajakuat.com', 'Aktif', NOW(), NOW()),
('SUP0003', 'UD Sumber Alam (Material Alam)', 'Jl. Raya Bogor Km 30', 'Depok', 'Haji Lulung', '085612349876', 'sumberalam@gmail.com', 'Aktif', NOW(), NOW()),
('SUP0004', 'PT Warna Agung Nusantara', 'Kawasan Industri Cikarang', 'Bekasi', 'Sinta', '0218901234', 'corporate@warnaagung.co.id', 'Aktif', NOW(), NOW()),
('SUP0005', 'Toko Besi & Listrik Sinar Makmur', 'Jl. Matraman Raya No 10', 'Jakarta Timur', 'Koh Aseng', '0218509988', 'sinarmakmur@yahoo.com', 'Aktif', NOW(), NOW());


-- ==========================================
-- 3. KATEGORI MATERIAL (10 Kategori Umum)
-- ==========================================
INSERT IGNORE INTO kategori_material (id_kategori_material, nama_kategori, deskripsi, created_at, updated_at) VALUES
('CAT0001', 'Semen & Pasir', 'Bahan pengikat dan agregat halus untuk beton dan plester', NOW(), NOW()),
('CAT0002', 'Besi & Baja', 'Material tulangan beton dan struktur baja ringan/berat', NOW(), NOW()),
('CAT0003', 'Kayu & Multiplek', 'Material kayu untuk begisting, perancah, dan rangka', NOW(), NOW()),
('CAT0004', 'Batu & Bata', 'Material pasangan dinding, pondasi, dan agregat kasar', NOW(), NOW()),
('CAT0005', 'Cat & Thinner', 'Material pelapis, pelindung, dan finishing dinding/besi', NOW(), NOW()),
('CAT0006', 'Pipa & Plumbing', 'Material saluran air bersih, kotor, dan perlengkapan sanitasi', NOW(), NOW()),
('CAT0007', 'Kabel & Elektrikal', 'Material instalasi listrik dan komponen pendukungnya', NOW(), NOW()),
('CAT0008', 'Keramik & Granit', 'Material penutup lantai dan dinding', NOW(), NOW()),
('CAT0009', 'Atap & Plafon', 'Material penutup atas bangunan dan rangka plafon', NOW(), NOW()),
('CAT0010', 'Paku & Baut', 'Material pengikat dan penyambung berbagai elemen konstruksi', NOW(), NOW());


-- ==========================================
-- 4. MASTER MATERIAL (20 Material Berbagai Jenis)
-- ==========================================
INSERT IGNORE INTO material (id_material, id_kategori_material, nama_material, satuan, spesifikasi, standar_kualitas, created_at, updated_at) VALUES
-- Kategori: Semen & Pasir
('MAT0001', 'CAT0001', 'Semen Portland Tiga Roda', 'Sak (50kg)', 'PC Tipe 1', 'SNI', NOW(), NOW()),
('MAT0002', 'CAT0001', 'Semen Gresik', 'Sak (40kg)', 'PCC', 'SNI', NOW(), NOW()),
('MAT0003', 'CAT0001', 'Pasir Lumajang', 'M3', 'Pasir Cor Bersih (Kasar)', 'Lokal Terbaik', NOW(), NOW()),
('MAT0004', 'CAT0001', 'Pasir Pasang/Cileungsi', 'M3', 'Pasir untuk Plesteran', 'Lokal', NOW(), NOW()),

-- Kategori: Besi & Baja
('MAT0005', 'CAT0002', 'Besi Beton Ulir 13mm (Krakatau Steel)', 'Batang (12m)', 'Ulir TS 420', 'SNI', NOW(), NOW()),
('MAT0006', 'CAT0002', 'Besi Beton Polos 8mm', 'Batang (12m)', 'Polos TP 280', 'SNI', NOW(), NOW()),
('MAT0007', 'CAT0002', 'Baja Ringan Canal C 75', 'Batang (6m)', 'Tebal 0.75mm (Galvalum)', 'SNI', NOW(), NOW()),
('MAT0008', 'CAT0002', 'Kawat Bendrat', 'Roll (20kg)', 'Kawat pengikat tulangan', 'Standar', NOW(), NOW()),

-- Kategori: Kayu & Multiplek
('MAT0009', 'CAT0003', 'Triplek/Multiplek 12mm', 'Lembar', 'Ukuran 122x244 cm', 'Lokal', NOW(), NOW()),
('MAT0010', 'CAT0003', 'Kayu Meranti 4x6', 'Batang (4m)', 'Kayu Begisting Tahan Air', 'Lokal', NOW(), NOW()),

-- Kategori: Batu & Bata
('MAT0011', 'CAT0004', 'Bata Ringan (Hebel)', 'M3', 'Ukuran 60x20x10 cm', 'SNI', NOW(), NOW()),
('MAT0012', 'CAT0004', 'Bata Merah Press', 'Pcs', 'Ukuran Standar', 'Lokal', NOW(), NOW()),
('MAT0013', 'CAT0004', 'Batu Pecah / Split 1/2', 'M3', 'Batu Cor / Agregat Kasar', 'Lokal', NOW(), NOW()),

-- Kategori: Cat & Thinner
('MAT0014', 'CAT0005', 'Cat Tembok Interior Dulux', 'Pail (20L)', 'Warna Putih Bersih', 'Standar Pabrik', NOW(), NOW()),
('MAT0015', 'CAT0005', 'Cat Besi Nippon Paint', 'Kaleng (1kg)', 'Warna Hitam Anti Karat', 'Standar Pabrik', NOW(), NOW()),

-- Kategori: Pipa & Plumbing
('MAT0016', 'CAT0006', 'Pipa PVC Wavin 4 inch', 'Batang (4m)', 'Tipe AW (Tebal, untuk air kotor)', 'SNI', NOW(), NOW()),
('MAT0017', 'CAT0006', 'Pipa PVC Wavin 1/2 inch', 'Batang (4m)', 'Tipe AW (untuk air bersih)', 'SNI', NOW(), NOW()),

-- Kategori: Kabel & Elektrikal
('MAT0018', 'CAT0007', 'Kabel NYM 3x2.5mm Eterna', 'Roll (50m)', 'Kabel instalasi dalam', 'SNI/LMK', NOW(), NOW()),

-- Kategori: Keramik & Granit
('MAT0019', 'CAT0008', 'Granit Roman 60x60', 'Dus (1.44m2)', 'Warna Cream Polos (KW 1)', 'SNI', NOW(), NOW()),

-- Kategori: Atap & Plafon
('MAT0020', 'CAT0009', 'Papan Gypsum Jayaboard 9mm', 'Lembar', 'Ukuran 120x240 cm', 'Standar Pabrik', NOW(), NOW());


INSERT INTO proyek (id_proyek, nama_proyek, lokasi_proyek, deskripsi_proyek, tanggal_mulai, tanggal_selesai, status_proyek, created_at, updated_at) VALUES
('PRY0001', 'Pembangunan Apartemen Senja', 'Jakarta Selatan', 'Proyek 10 Lantai', '2026-03-01', '2026-08-31', 'Aktif', NOW(), NOW()),
('PRY0002', 'Renovasi Gedung Rektorat Kampus ABC', 'Bandung', 'Pekerjaan interior dan sipil', '2026-03-15', '2026-07-15', 'Aktif', NOW(), NOW()),
('PRY0003', 'Pembangunan Perumahan Harmoni (Tahap 1)', 'Depok', 'Pembangunan 50 Unit Rumah Tipe 45', '2026-04-01', '2026-08-01', 'Aktif', NOW(), NOW()),
('PRY0004', 'Gedung Perkantoran Sudirman Tower', 'Jakarta Pusat', 'Proyek High-Rise 25 Lantai', '2026-05-01', '2026-08-31', 'Ditunda', NOW(), NOW()),
('PRY0005', 'Infrastruktur Jembatan Antar Desa', 'Bantul, Yogyakarta', 'Pekerjaan struktur baja dan beton', '2026-03-01', '2026-04-30', 'Selesai', NOW(), NOW());

USE scm_konstruksi_ta;

-- ==========================================
-- 1. PENGAJUAN MASA LALU (Tgl 1 Maret 2026)
-- ==========================================
-- Simulasi: Pelaksana mengajukan Semen dan Besi yang belum ada di gudang, sehingga statusnya diteruskan ke Pengadaan (Selesai).
INSERT INTO pengajuan_material (id_pengajuan, id_proyek, id_user_pengaju, tanggal_pengajuan, tipe_pengajuan, status_pengajuan, catatan_pengajuan, created_at, updated_at) VALUES
('PGJ0010', 'PRY0001', 'USR0006', '2026-03-01', 'Permintaan Pelaksanaan', 'Selesai', 'Kebutuhan awal proyek', NOW(), NOW());

INSERT INTO detail_pengajuan_material (id_detail_pengajuan, id_pengajuan, id_material, jumlah_diajukan, jumlah_dipenuhi_gudang, jumlah_dibelikan, created_at, updated_at) VALUES
('DPG0010', 'PGJ0010', 'MAT0001', 100, 0, 100, NOW(), NOW()), -- Semen 100 sak (minta dibelikan 100)
('DPG0011', 'PGJ0010', 'MAT0005', 50, 0, 50, NOW(), NOW());  -- Besi 50 batang (minta dibelikan 50)


-- ==========================================
-- 2. PEMBUATAN PO OLEH PENGADAAN (Tgl 2 Maret 2026)
-- ==========================================
-- Simulasi: Tim Pengadaan membuat PO ke Supplier.
INSERT INTO pesanan (id_pesanan, id_pengajuan, id_supplier, id_user_pengadaan, nomor_pesanan, tanggal_pesanan, target_tanggal_kirim, total_nilai_pesanan, status_pesanan, created_at, updated_at) VALUES
('PO0010', 'PGJ0010', 'SUP0001', 'USR0003', 'PO/SCM/03/001', '2026-03-02', '2026-03-06', 10000000.00, 'Selesai', NOW(), NOW());

INSERT INTO detail_pesanan (id_detail_pesanan, id_pesanan, id_material, jumlah_pesan, jumlah_telah_diterima, harga_negosiasi_satuan, created_at, updated_at) VALUES
('DPO0010', 'PO0010', 'MAT0001', 100, 100, 50000.00, NOW(), NOW()), -- Semen
('DPO0011', 'PO0010', 'MAT0005', 50, 50, 100000.00, NOW(), NOW());  -- Besi


-- ==========================================
-- 3. PENGIRIMAN DARI SUPPLIER (Tgl 4 Maret 2026)
-- ==========================================
INSERT INTO pengiriman (id_pengiriman, id_pesanan, id_user_pengadaan, tanggal_berangkat, estimasi_tanggal_tiba, nama_supir, plat_kendaraan, status_pengiriman, created_at, updated_at) VALUES 
('KRM0010', 'PO0010', 'USR0003', '2026-03-04', '2026-03-05', 'Budi Hartono', 'B 9988 XYZ', 'Selesai', NOW(), NOW());


-- ==========================================
-- 4. PENERIMAAN LOGISTIK & MASUK STOK (Tgl 5 Maret 2026)
-- ==========================================
-- Simulasi: Logistik menerima barang dan mencatatnya sebagai STOK TERSEDIA.
INSERT INTO penerimaan_material (id_penerimaan, id_pengiriman, id_user_penerima, tanggal_terima, nomor_surat_jalan, status_penerimaan, created_at, updated_at) VALUES
('TRM0010', 'KRM0010', 'USR0005', '2026-03-05', 'SJ/SUP1/03/001', 'Diterima Penuh', NOW(), NOW());

INSERT INTO detail_penerimaan (id_detail_terima, id_penerimaan, id_material, jumlah_bagus, jumlah_rusak, created_at, updated_at) VALUES
('DTR0010', 'TRM0010', 'MAT0001', 100, 0, NOW(), NOW()),
('DTR0011', 'TRM0010', 'MAT0005', 50, 0, NOW(), NOW());

-- INI YANG PALING PENTING: Barang masuk ke tabel STOK GUDANG
INSERT INTO stok_batch_fifo (id_stok, id_material, id_penerimaan, tanggal_masuk, jumlah_awal, sisa_stok, lokasi_rak, status_stok, created_at, updated_at) VALUES
('STK0010', 'MAT0001', 'TRM0010', '2026-03-05', 100, 100, 'Rak A-1', 'Tersedia', NOW(), NOW()), -- Ada 100 sak semen di Rak A-1
('STK0011', 'MAT0005', 'TRM0010', '2026-03-05', 50, 50, 'Rak B-1', 'Tersedia', NOW(), NOW());    -- Ada 50 batang besi di Rak B-1