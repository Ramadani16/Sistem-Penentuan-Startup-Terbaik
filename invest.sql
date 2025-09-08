CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'startup') NOT NULL DEFAULT 'startup',
  nama_lengkap VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  status ENUM('pending', 'verified') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO users (username, password, role, nama_lengkap, email, status) VALUES
('admin', '$2y$10$h4pk9jDoW4y1vq2FkO0/4eePoEo4mVDo8qF3gEPqYm.lefFw52K6i', 'admin', 'Administrator Utama', 'admin@example.com', 'verified');


CREATE TABLE startup_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  nama_startup VARCHAR(100) NOT NULL,
  deskripsi TEXT NOT NULL,
  bidang_usaha VARCHAR(100) NOT NULL,
  tahun_berdiri YEAR NOT NULL,
  lokasi VARCHAR(100) NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE kriteria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(10) NOT NULL UNIQUE,
  nama_kriteria VARCHAR(100) NOT NULL,
  bobot DECIMAL(5,2) NOT NULL,
  sifat ENUM('benefit', 'cost') NOT NULL
);


INSERT INTO kriteria (kode, nama_kriteria, bobot, sifat) VALUES
('C1', 'Inovasi Produk / Jasa', 20.00, 'benefit');
INSERT INTO kriteria (kode, nama_kriteria, bobot, sifat) VALUES
('C2', 'Potensi Pasar', 20.00, 'benefit');
INSERT INTO kriteria (kode, nama_kriteria, bobot, sifat) VALUES
('C3', 'Sustainability (Kondisi dan Proyeksi Keuangan)', 15.00, 'benefit');
INSERT INTO kriteria (kode, nama_kriteria, bobot, sifat) VALUES
('C4', 'Reputasi Tim Management', 15.00, 'benefit');
INSERT INTO kriteria (kode, nama_kriteria, bobot, sifat) VALUES
('C5', 'Tingkat Risiko', 15.00, 'cost');
INSERT INTO kriteria (kode, nama_kriteria, bobot, sifat) VALUES
('C6', 'Harga / Valuasi Perusahaan / Kebutuhan Dana', 15.00, 'cost');

CREATE TABLE penilaian (
  id INT AUTO_INCREMENT PRIMARY KEY,
  startup_id INT NOT NULL,
  kriteria_id INT NOT NULL,
  nilai DECIMAL(5,2) NOT NULL,
  FOREIGN KEY (startup_id) REFERENCES startup_profiles(id) ON DELETE CASCADE,
  FOREIGN KEY (kriteria_id) REFERENCES kriteria(id) ON DELETE CASCADE,
  UNIQUE (startup_id, kriteria_id)
);

CREATE TABLE hasil_moora (
  id INT AUTO_INCREMENT PRIMARY KEY,
  startup_id INT NOT NULL,
  nilai_akhir DECIMAL(10,4) NOT NULL,
  ranking INT,
  tanggal_proses DATE,
  FOREIGN KEY (startup_id) REFERENCES startup_profiles(id) ON DELETE CASCADE
);

CREATE TABLE kuisioner_startup (
    id INT AUTO_INCREMENT PRIMARY KEY,
    startup_id INT NOT NULL, 
    inovasi_produk INT,
    potensi_pasar INT,
    sustainability INT,
    reputasi_tim INT,
    tingkat_risiko ENUM('rendah', 'sedang', 'tinggi'),
    valuasi_perusahaan DECIMAL(15,2),
    tanggal_pengisian TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (startup_id) REFERENCES startup_profiles(id) ON DELETE CASCADE
);