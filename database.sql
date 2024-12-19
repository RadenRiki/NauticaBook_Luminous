CREATE DATABASE luminousdb;

USE luminousdb;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    referral VARCHAR(50)
);

CREATE TABLE passengers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asal VARCHAR(255) NOT NULL,
    tujuan VARCHAR(255) NOT NULL,
    layanan VARCHAR(50) NOT NULL,
    tipe VARCHAR(50) NOT NULL,
    jumlah_penumpang INT NOT NULL,
    tanggal DATE NOT NULL,
    jam TIME NOT NULL
);

CREATE TABLE cargo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asal VARCHAR(255) NOT NULL,
    tujuan VARCHAR(255) NOT NULL,
    jenis VARCHAR(50) NOT NULL,
    berat_kg FLOAT NOT NULL,
    tanggal DATE NOT NULL
);
