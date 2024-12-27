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