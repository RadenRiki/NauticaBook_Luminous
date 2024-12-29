-- Create the database
CREATE DATABASE luminousdb;

-- Use the database
USE luminousdb;

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    referral VARCHAR(50)
);

-- Create the passengers table
CREATE TABLE passengers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    asal VARCHAR(255) NOT NULL,
    tujuan VARCHAR(255) NOT NULL,
    layanan VARCHAR(50) NOT NULL,
    tipe VARCHAR(50) NOT NULL,
    jumlah_penumpang INT NOT NULL,
    tanggal DATE NOT NULL,
    jam TIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create the cargo table
CREATE TABLE cargo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    asal VARCHAR(255) NOT NULL,
    tujuan VARCHAR(255) NOT NULL,
    jenis VARCHAR(50) NOT NULL,
    berat_kg FLOAT NOT NULL,
    tanggal DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- jangan lupa tambahin ini
ALTER TABLE passengers 
  ADD COLUMN nama_pemesan VARCHAR(255),
  ADD COLUMN email_pemesan VARCHAR(255),
  ADD COLUMN nomor_hp VARCHAR(20),
  ADD COLUMN detail_penumpang TEXT;

-- tambahin ini lagi
ALTER TABLE cargo 
    ADD COLUMN nama_pengirim VARCHAR(255),
    ADD COLUMN alamat_pengirim TEXT,
    ADD COLUMN kota_pengirim VARCHAR(255),
    ADD COLUMN kodepos_pengirim VARCHAR(20),
    ADD COLUMN telepon_pengirim VARCHAR(20),
    ADD COLUMN nama_penerima VARCHAR(255),
    ADD COLUMN alamat_penerima TEXT,
    ADD COLUMN kota_penerima VARCHAR(255),
    ADD COLUMN kodepos_penerima VARCHAR(20),
    ADD COLUMN telepon_penerima VARCHAR(20),
    ADD COLUMN catatan TEXT,
    ADD COLUMN status VARCHAR(50) DEFAULT 'aktif';

-- barcode penumpang
ALTER TABLE passengers DROP COLUMN barcode;

ALTER TABLE passengers
ADD COLUMN barcode VARCHAR(50);

UPDATE passengers 
SET barcode = CONCAT('TF', id, LEFT(MD5(id), 6))
WHERE barcode IS NULL;

ALTER TABLE passengers
MODIFY barcode VARCHAR(50) NOT NULL,
ADD UNIQUE INDEX (barcode);

-- barcode cargo
ALTER TABLE cargo DROP COLUMN barcode;

ALTER TABLE cargo
ADD COLUMN barcode VARCHAR(50);

UPDATE cargo 
SET barcode = CONCAT('TC', id, LEFT(MD5(id), 6))
WHERE barcode IS NULL;

ALTER TABLE cargo
MODIFY barcode VARCHAR(50) NOT NULL,
ADD UNIQUE INDEX (barcode);

-- ini untuk ada fitur tambah profile
ALTER TABLE users
ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL;

-- untuk referral code
ALTER TABLE users ADD COLUMN has_referral BOOLEAN DEFAULT FALSE;

-- Buat tabel tarif_layanan
CREATE TABLE tarif_layanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rute VARCHAR(50) NOT NULL,
    layanan VARCHAR(20) NOT NULL,
    tipe_tiket VARCHAR(50) NOT NULL,
    kategori VARCHAR(20) DEFAULT NULL,
    harga DECIMAL(10,2) NOT NULL,
    UNIQUE KEY unique_tarif (rute, layanan, tipe_tiket, kategori)
);

-- Merak-Bakauheni Regular
INSERT INTO tarif_layanan (rute, layanan, tipe_tiket, kategori, harga) VALUES
('merak-bakauheni', 'regular', 'Pejalan Kaki', 'dewasa', 22700),
('merak-bakauheni', 'regular', 'Pejalan Kaki', 'bayi', 1800),
('merak-bakauheni', 'regular', 'Pejalan Kaki', 'anak', 22700),
('merak-bakauheni', 'regular', 'Pejalan Kaki', 'lansia', 22700),
('merak-bakauheni', 'regular', 'Sepeda', NULL, 26500),
('merak-bakauheni', 'regular', 'Motor Kecil', NULL, 62100),
('merak-bakauheni', 'regular', 'Motor Besar', NULL, 133000),
('merak-bakauheni', 'regular', 'Mobil', NULL, 481800);

-- Merak-Bakauheni Express
INSERT INTO tarif_layanan (rute, layanan, tipe_tiket, kategori, harga) VALUES
('merak-bakauheni', 'express', 'Pejalan Kaki', 'dewasa', 84000),
('merak-bakauheni', 'express', 'Pejalan Kaki', 'bayi', 4000),
('merak-bakauheni', 'express', 'Pejalan Kaki', 'anak', 84000),
('merak-bakauheni', 'express', 'Pejalan Kaki', 'lansia', 84000),
('merak-bakauheni', 'express', 'Sepeda', NULL, 85000),
('merak-bakauheni', 'express', 'Motor Kecil', NULL, 129677),
('merak-bakauheni', 'express', 'Motor Besar', NULL, 187853),
('merak-bakauheni', 'express', 'Mobil', NULL, 749128);

-- Bakauheni-Merak Regular
INSERT INTO tarif_layanan (rute, layanan, tipe_tiket, kategori, harga) VALUES
('bakauheni-merak', 'regular', 'Pejalan Kaki', 'dewasa', 22700),
('bakauheni-merak', 'regular', 'Pejalan Kaki', 'bayi', 1800),
('bakauheni-merak', 'regular', 'Pejalan Kaki', 'anak', 22700),
('bakauheni-merak', 'regular', 'Pejalan Kaki', 'lansia', 22700),
('bakauheni-merak', 'regular', 'Sepeda', NULL, 26500),
('bakauheni-merak', 'regular', 'Motor Kecil', NULL, 62100),
('bakauheni-merak', 'regular', 'Motor Besar', NULL, 133000),
('bakauheni-merak', 'regular', 'Mobil', NULL, 481800);

-- Bakauheni-Merak Express
INSERT INTO tarif_layanan (rute, layanan, tipe_tiket, kategori, harga) VALUES
('bakauheni-merak', 'express', 'Pejalan Kaki', 'dewasa', 84000),
('bakauheni-merak', 'express', 'Pejalan Kaki', 'bayi', 4000),
('bakauheni-merak', 'express', 'Pejalan Kaki', 'anak', 84000),
('bakauheni-merak', 'express', 'Pejalan Kaki', 'lansia', 84000),
('bakauheni-merak', 'express', 'Sepeda', NULL, 85000),
('bakauheni-merak', 'express', 'Motor Kecil', NULL, 129677),
('bakauheni-merak', 'express', 'Motor Besar', NULL, 187853),
('bakauheni-merak', 'express', 'Mobil', NULL, 749128);

-- Ketapang-Gilimanuk Regular
INSERT INTO tarif_layanan (rute, layanan, tipe_tiket, kategori, harga) VALUES
('ketapang-gilimanuk', 'regular', 'Pejalan Kaki', 'dewasa', 10600),
('ketapang-gilimanuk', 'regular', 'Pejalan Kaki', 'bayi', 1600),
('ketapang-gilimanuk', 'regular', 'Pejalan Kaki', 'anak', 10600),
('ketapang-gilimanuk', 'regular', 'Pejalan Kaki', 'lansia', 10600),
('ketapang-gilimanuk', 'regular', 'Sepeda', NULL, 11000),
('ketapang-gilimanuk', 'regular', 'Motor Kecil', NULL, 31600),
('ketapang-gilimanuk', 'regular', 'Motor Besar', NULL, 45000),
('ketapang-gilimanuk', 'regular', 'Mobil', NULL, 213400);

-- Ketapang-Gilimanuk Express
INSERT INTO tarif_layanan (rute, layanan, tipe_tiket, kategori, harga) VALUES
('ketapang-gilimanuk', 'express', 'Pejalan Kaki', 'dewasa', 35000),
('ketapang-gilimanuk', 'express', 'Pejalan Kaki', 'bayi', 3500),
('ketapang-gilimanuk', 'express', 'Pejalan Kaki', 'anak', 35000),
('ketapang-gilimanuk', 'express', 'Pejalan Kaki', 'lansia', 35000),
('ketapang-gilimanuk', 'express', 'Sepeda', NULL, 37000),
('ketapang-gilimanuk', 'express', 'Motor Kecil', NULL, 85000),
('ketapang-gilimanuk', 'express', 'Motor Besar', NULL, 120000),
('ketapang-gilimanuk', 'express', 'Mobil', NULL, 550000);

-- Gilimanuk-Ketapang Regular  
INSERT INTO tarif_layanan (rute, layanan, tipe_tiket, kategori, harga) VALUES
('gilimanuk-ketapang', 'regular', 'Pejalan Kaki', 'dewasa', 10600),
('gilimanuk-ketapang', 'regular', 'Pejalan Kaki', 'bayi', 1600),
('gilimanuk-ketapang', 'regular', 'Pejalan Kaki', 'anak', 10600),
('gilimanuk-ketapang', 'regular', 'Pejalan Kaki', 'lansia', 10600),
('gilimanuk-ketapang', 'regular', 'Sepeda', NULL, 11000),
('gilimanuk-ketapang', 'regular', 'Motor Kecil', NULL, 31600),
('gilimanuk-ketapang', 'regular', 'Motor Besar', NULL, 45000),
('gilimanuk-ketapang', 'regular', 'Mobil', NULL, 213400);

-- Gilimanuk-Ketapang Express
INSERT INTO tarif_layanan (rute, layanan, tipe_tiket, kategori, harga) VALUES
('gilimanuk-ketapang', 'express', 'Pejalan Kaki', 'dewasa', 35000),
('gilimanuk-ketapang', 'express', 'Pejalan Kaki', 'bayi', 3500),
('gilimanuk-ketapang', 'express', 'Pejalan Kaki', 'anak', 35000),
('gilimanuk-ketapang', 'express', 'Pejalan Kaki', 'lansia', 35000),
('gilimanuk-ketapang', 'express', 'Sepeda', NULL, 37000),
('gilimanuk-ketapang', 'express', 'Motor Kecil', NULL, 85000),
('gilimanuk-ketapang', 'express', 'Motor Besar', NULL, 120000),
('gilimanuk-ketapang', 'express', 'Mobil', NULL, 550000);

-- Buat barcode unik untuk tiket yang sudah ada (jika ada)
UPDATE passengers 
SET barcode = CONCAT('TF', id, LEFT(MD5(id), 6))
WHERE barcode IS NULL;

-- Tambahkan constraint UNIQUE untuk barcode
ALTER TABLE passengers
MODIFY barcode VARCHAR(50) NOT NULL,
ADD UNIQUE INDEX (barcode);

ALTER TABLE passengers 
ADD COLUMN total_harga DECIMAL(10,2) NOT NULL DEFAULT 0;

CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO admin (username, email, password) 
VALUES ('admin', 'admin@nauticabook.com', 'admin123');

CREATE TABLE tarif_cargo (
    id int NOT NULL AUTO_INCREMENT,
    jenis_barang varchar(50) NOT NULL,
    harga_per_kg decimal(10,2) NOT NULL,
    rute varchar(50) NOT NULL,
    aktif boolean DEFAULT true,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_tarif_cargo (jenis_barang, rute)
);

INSERT INTO tarif_cargo (jenis_barang, harga_per_kg, rute) VALUES
-- Rute Merak-Bakauheni
('Umum', 15000, 'merak-bakauheni'),
('Kosmetik dan Kecantikan', 20000, 'merak-bakauheni'),
('Kendaraan', 30000, 'merak-bakauheni'),
('Peralatan Rumah Tangga', 18000, 'merak-bakauheni'),
('Mesin ukuran besar', 25000, 'merak-bakauheni'),
('Elektronik', 35000, 'merak-bakauheni'),
('Furniture', 22000, 'merak-bakauheni'),
('Lainnya', 15000, 'merak-bakauheni'),

-- Rute Bakauheni-Merak (harga sama dengan sebaliknya)
('Umum', 15000, 'bakauheni-merak'),
('Kosmetik dan Kecantikan', 20000, 'bakauheni-merak'),
('Kendaraan', 30000, 'bakauheni-merak'),
('Peralatan Rumah Tangga', 18000, 'bakauheni-merak'),
('Mesin ukuran besar', 25000, 'bakauheni-merak'),
('Elektronik', 35000, 'bakauheni-merak'),
('Furniture', 22000, 'bakauheni-merak'),
('Lainnya', 15000, 'bakauheni-merak'),

-- Rute Ketapang-Gilimanuk (tarif lebih rendah karena jarak lebih dekat)
('Umum', 12000, 'ketapang-gilimanuk'),
('Kosmetik dan Kecantikan', 17000, 'ketapang-gilimanuk'),
('Kendaraan', 25000, 'ketapang-gilimanuk'),
('Peralatan Rumah Tangga', 15000, 'ketapang-gilimanuk'),
('Mesin ukuran besar', 22000, 'ketapang-gilimanuk'),
('Elektronik', 30000, 'ketapang-gilimanuk'),
('Furniture', 18000, 'ketapang-gilimanuk'),
('Lainnya', 12000, 'ketapang-gilimanuk'),

-- Rute Gilimanuk-Ketapang
('Umum', 12000, 'gilimanuk-ketapang'),
('Kosmetik dan Kecantikan', 17000, 'gilimanuk-ketapang'),
('Kendaraan', 25000, 'gilimanuk-ketapang'),
('Peralatan Rumah Tangga', 15000, 'gilimanuk-ketapang'),
('Mesin ukuran besar', 22000, 'gilimanuk-ketapang'),
('Elektronik', 30000, 'gilimanuk-ketapang'),
('Furniture', 18000, 'gilimanuk-ketapang'),
('Lainnya', 12000, 'gilimanuk-ketapang'),

-- Rute dengan Tanjung Priok (tarif lebih tinggi karena pelabuhan besar)
('Umum', 18000, 'tanjungpriok-merak'),
('Kosmetik dan Kecantikan', 23000, 'tanjungpriok-merak'),
('Kendaraan', 33000, 'tanjungpriok-merak'),
('Peralatan Rumah Tangga', 21000, 'tanjungpriok-merak'),
('Mesin ukuran besar', 28000, 'tanjungpriok-merak'),
('Elektronik', 38000, 'tanjungpriok-merak'),
('Furniture', 25000, 'tanjungpriok-merak'),
('Lainnya', 18000, 'tanjungpriok-merak'),

-- Lanjutan untuk rute-rute yang belum ada sebelumnya...

-- Rute dengan Tanjung Priok - Bakauheni
('Umum', 18000, 'tanjungpriok-bakauheni'),
('Kosmetik dan Kecantikan', 23000, 'tanjungpriok-bakauheni'),
('Kendaraan', 33000, 'tanjungpriok-bakauheni'),
('Peralatan Rumah Tangga', 21000, 'tanjungpriok-bakauheni'),
('Mesin ukuran besar', 28000, 'tanjungpriok-bakauheni'),
('Elektronik', 38000, 'tanjungpriok-bakauheni'),
('Furniture', 25000, 'tanjungpriok-bakauheni'),
('Lainnya', 18000, 'tanjungpriok-bakauheni'),

-- Rute Tanjung Priok - Tanjung Perak (rute panjang, harga lebih tinggi)
('Umum', 20000, 'tanjungpriok-tanjungperak'),
('Kosmetik dan Kecantikan', 25000, 'tanjungpriok-tanjungperak'),
('Kendaraan', 35000, 'tanjungpriok-tanjungperak'),
('Peralatan Rumah Tangga', 23000, 'tanjungpriok-tanjungperak'),
('Mesin ukuran besar', 30000, 'tanjungpriok-tanjungperak'),
('Elektronik', 40000, 'tanjungpriok-tanjungperak'),
('Furniture', 27000, 'tanjungpriok-tanjungperak'),
('Lainnya', 20000, 'tanjungpriok-tanjungperak'),

-- Rute Tanjung Perak - Tanjung Priok
('Umum', 20000, 'tanjungperak-tanjungpriok'),
('Kosmetik dan Kecantikan', 25000, 'tanjungperak-tanjungpriok'),
('Kendaraan', 35000, 'tanjungperak-tanjungpriok'),
('Peralatan Rumah Tangga', 23000, 'tanjungperak-tanjungpriok'),
('Mesin ukuran besar', 30000, 'tanjungperak-tanjungpriok'),
('Elektronik', 40000, 'tanjungperak-tanjungpriok'),
('Furniture', 27000, 'tanjungperak-tanjungpriok'),
('Lainnya', 20000, 'tanjungperak-tanjungpriok'),

-- Rute Tanjung Perak - Merak
('Umum', 19000, 'tanjungperak-merak'),
('Kosmetik dan Kecantikan', 24000, 'tanjungperak-merak'),
('Kendaraan', 34000, 'tanjungperak-merak'),
('Peralatan Rumah Tangga', 22000, 'tanjungperak-merak'),
('Mesin ukuran besar', 29000, 'tanjungperak-merak'),
('Elektronik', 39000, 'tanjungperak-merak'),
('Furniture', 26000, 'tanjungperak-merak'),
('Lainnya', 19000, 'tanjungperak-merak'),

-- Rute Tanjung Perak - Bakauheni
('Umum', 19000, 'tanjungperak-bakauheni'),
('Kosmetik dan Kecantikan', 24000, 'tanjungperak-bakauheni'),
('Kendaraan', 34000, 'tanjungperak-bakauheni'),
('Peralatan Rumah Tangga', 22000, 'tanjungperak-bakauheni'),
('Mesin ukuran besar', 29000, 'tanjungperak-bakauheni'),
('Elektronik', 39000, 'tanjungperak-bakauheni'),
('Furniture', 26000, 'tanjungperak-bakauheni'),
('Lainnya', 19000, 'tanjungperak-bakauheni'),

-- Rute Tanjung Perak - Ketapang
('Umum', 16000, 'tanjungperak-ketapang'),
('Kosmetik dan Kecantikan', 21000, 'tanjungperak-ketapang'),
('Kendaraan', 31000, 'tanjungperak-ketapang'),
('Peralatan Rumah Tangga', 19000, 'tanjungperak-ketapang'),
('Mesin ukuran besar', 26000, 'tanjungperak-ketapang'),
('Elektronik', 36000, 'tanjungperak-ketapang'),
('Furniture', 23000, 'tanjungperak-ketapang'),
('Lainnya', 16000, 'tanjungperak-ketapang'),

-- Rute Tanjung Perak - Gilimanuk
('Umum', 16000, 'tanjungperak-gilimanuk'),
('Kosmetik dan Kecantikan', 21000, 'tanjungperak-gilimanuk'),
('Kendaraan', 31000, 'tanjungperak-gilimanuk'),
('Peralatan Rumah Tangga', 19000, 'tanjungperak-gilimanuk'),
('Mesin ukuran besar', 26000, 'tanjungperak-gilimanuk'),
('Elektronik', 36000, 'tanjungperak-gilimanuk'),
('Furniture', 23000, 'tanjungperak-gilimanuk'),
('Lainnya', 16000, 'tanjungperak-gilimanuk'),

-- Rute Merak - Tanjung Priok
('Umum', 18000, 'merak-tanjungpriok'),
('Kosmetik dan Kecantikan', 23000, 'merak-tanjungpriok'),
('Kendaraan', 33000, 'merak-tanjungpriok'),
('Peralatan Rumah Tangga', 21000, 'merak-tanjungpriok'),
('Mesin ukuran besar', 28000, 'merak-tanjungpriok'),
('Elektronik', 38000, 'merak-tanjungpriok'),
('Furniture', 25000, 'merak-tanjungpriok'),
('Lainnya', 18000, 'merak-tanjungpriok'),

-- Rute Merak - Tanjung Perak
('Umum', 19000, 'merak-tanjungperak'),
('Kosmetik dan Kecantikan', 24000, 'merak-tanjungperak'),
('Kendaraan', 34000, 'merak-tanjungperak'),
('Peralatan Rumah Tangga', 22000, 'merak-tanjungperak'),
('Mesin ukuran besar', 29000, 'merak-tanjungperak'),
('Elektronik', 39000, 'merak-tanjungperak'),
('Furniture', 26000, 'merak-tanjungperak'),
('Lainnya', 19000, 'merak-tanjungperak'),

-- Rute Bakauheni - Tanjung Priok
('Umum', 18000, 'bakauheni-tanjungpriok'),
('Kosmetik dan Kecantikan', 23000, 'bakauheni-tanjungpriok'),
('Kendaraan', 33000, 'bakauheni-tanjungpriok'),
('Peralatan Rumah Tangga', 21000, 'bakauheni-tanjungpriok'),
('Mesin ukuran besar', 28000, 'bakauheni-tanjungpriok'),
('Elektronik', 38000, 'bakauheni-tanjungpriok'),
('Furniture', 25000, 'bakauheni-tanjungpriok'),
('Lainnya', 18000, 'bakauheni-tanjungpriok'),

-- Rute Bakauheni - Tanjung Perak
('Umum', 19000, 'bakauheni-tanjungperak'),
('Kosmetik dan Kecantikan', 24000, 'bakauheni-tanjungperak'),
('Kendaraan', 34000, 'bakauheni-tanjungperak'),
('Peralatan Rumah Tangga', 22000, 'bakauheni-tanjungperak'),
('Mesin ukuran besar', 29000, 'bakauheni-tanjungperak'),
('Elektronik', 39000, 'bakauheni-tanjungperak'),
('Furniture', 26000, 'bakauheni-tanjungperak'),
('Lainnya', 19000, 'bakauheni-tanjungperak');

