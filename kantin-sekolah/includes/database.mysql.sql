CREATE DATABASE kantin_sekolah;
USE kantin_sekolah;

CREATE TABLE kantin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(100)
);

CREATE TABLE menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kantin_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    gambar VARCHAR(100),
    FOREIGN KEY (kantin_id) REFERENCES kantin(id)
);

CREATE TABLE pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100) NOT NULL,
    total_harga INT NOT NULL,
    tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pesanan_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    menu_id INT NOT NULL,
    quantity INT NOT NULL,
    harga_satuan INT NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id),
    FOREIGN KEY (menu_id) REFERENCES menu(id)
);

CREATE TABLE kontak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    pesan TEXT NOT NULL,
    tanggal_kirim DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Data contoh
INSERT INTO kantin (nama, deskripsi, gambar) VALUES 
('Kantin Ibu Rika', 'Kantin dengan berbagai menu nasi dan mie', 'kantin1.jpeg'),
('Kantin Batagor Mas Riki', 'Kantin spesialis jajanan khas Bandung', 'kantin2.jpeg'),
('Kantin Masakan Rumah bu Eka', 'Kantin dengan masakan rumahan yang lezat', 'kantin3.jpeg');


INSERT INTO menu (kantin_id, nama, harga, stok, gambar) VALUES
(1, 'Nasi Goreng', 15000, 20, 'nasi-goreng.jpg'),
(1, 'Mie Goreng', 12000, 15, 'mie-goreng.jpg'),
(1, 'Es Teh', 5000, 50, 'es-teh.jpg'),
(1, 'Es Jeruk', 7000, 40, 'es-jeruk.jpg'),
(2, 'Batagor', 10000, 30, 'batagor.jpg'),
(2, 'Siomay', 12000, 25, 'siomay.jpg'),
(2, 'Es Campur', 8000, 35, 'es-campur.jpg'),
(2, 'Es Cendol', 7000, 30, 'es-cendol.jpg'),
(3, 'Ayam Goreng', 18000, 15, 'ayam-goreng.jpg'),
(3, 'Sayur Asem', 10000, 20, 'sayur-asem.jpg'),
(3, 'Es Teh', 5000, 40, 'es-teh2.jpg'),
(3, 'Es Dawet', 6000, 30, 'es-dawet.jpg');
