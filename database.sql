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

-- ALTER TABLE passengers
-- ADD COLUMN nama_pemesan VARCHAR(255),
-- ADD COLUMN email_pemesan VARCHAR(255),
-- ADD COLUMN nomor_hp VARCHAR(20),
-- ADD COLUMN detail_penumpang TEXT;
