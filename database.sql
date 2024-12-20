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
