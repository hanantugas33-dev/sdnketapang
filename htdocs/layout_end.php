-- ============================================================
-- DATABASE: sdn_ketapang
-- Dibuat untuk SDN Ketapang Website
-- ============================================================

CREATE DATABASE IF NOT EXISTS sdn_ketapang
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sdn_ketapang;

-- ============================================================
-- TABEL: profil_sekolah
-- ============================================================
CREATE TABLE IF NOT EXISTS profil_sekolah (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_sekolah VARCHAR(150) NOT NULL,
  npsn VARCHAR(20),
  nss VARCHAR(30),
  akreditasi ENUM('A','B','C','Belum') DEFAULT 'A',
  tahun_berdiri YEAR,
  alamat TEXT,
  kelurahan VARCHAR(100),
  kecamatan VARCHAR(100),
  kabupaten_kota VARCHAR(100),
  provinsi VARCHAR(100),
  kode_pos VARCHAR(10),
  telepon VARCHAR(20),
  email VARCHAR(100),
  website VARCHAR(150),
  total_siswa INT DEFAULT 0,
  total_guru INT DEFAULT 0,
  total_kelas INT DEFAULT 0,
  jam_operasional VARCHAR(200),
  lat DECIMAL(10,8),
  lng DECIMAL(11,8),
  maps_embed TEXT,
  visi TEXT,
  misi TEXT,
  tujuan TEXT,
  sejarah TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO profil_sekolah (
  nama_sekolah, npsn, akreditasi, tahun_berdiri,
  alamat, kelurahan, kecamatan, kabupaten_kota, provinsi, kode_pos,
  telepon, email, website,
  total_siswa, total_guru, total_kelas,
  jam_operasional,
  visi, misi, tujuan, sejarah
) VALUES (
  'SDN Ketapang', '', 'A', 1975,
  'Jl. Raya Ketapang RT 05/05', 'Ketapang', 'Cipondoh', 'Tangerang', 'Banten', '15147',
  '(082211617039)', 'sdnketapan9@sch.id', 'https://sdnketapang.sch.id',
  350, 22, 12,
  'Senin – Jumat: 07.00 – 13.30 WIB | Sabtu: 07.00 – 11.00 WIB',
  'Terwujudnya peserta didik yang beriman, berilmu, berprestasi, berbudaya, dan berwawasan lingkungan.',
  'Menyelenggarakan pembelajaran aktif, kreatif, efektif, dan menyenangkan.\nMengembangkan potensi siswa secara optimal dalam bidang akademik dan non-akademik.\nMenciptakan lingkungan sekolah yang bersih, nyaman, dan kondusif.\nMenjalin kerjasama yang harmonis antara sekolah, orang tua, dan masyarakat.\nMenanamkan nilai-nilai karakter bangsa dalam setiap kegiatan pembelajaran.',
  'Meningkatkan mutu pendidikan secara berkelanjutan.\nMenjadikan siswa yang berkarakter Profil Pelajar Pancasila.\nMeraih prestasi di tingkat kecamatan, kabupaten, dan provinsi.\nMenciptakan lulusan yang siap melanjutkan ke jenjang pendidikan lebih tinggi.',
  'SDN Ketapang berdiri pada tahun 1975 atas prakarsa pemerintah daerah setempat. Sejak awal berdirinya, sekolah ini berkomitmen memberikan pendidikan berkualitas bagi anak-anak di wilayah Ketapang. Dalam perjalanannya, SDN Ketapang telah mengalami berbagai perkembangan baik dari sisi infrastruktur, kualitas pengajar, maupun prestasi akademik dan non-akademik.'
);

-- ============================================================
-- TABEL: struktur_organisasi
-- ============================================================
CREATE TABLE IF NOT EXISTS struktur_organisasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  jabatan VARCHAR(100) NOT NULL,
  nip VARCHAR(30),
  foto VARCHAR(255) DEFAULT NULL,
  urutan INT DEFAULT 0,
  level VARCHAR(30) DEFAULT 'staff',
  parent_id INT DEFAULT NULL,
  is_active TINYINT DEFAULT 1
);

INSERT INTO struktur_organisasi(id, nama, jabatan, nip, foto, urutan, level, parent_id) VALUES
(1, 'SUMIATI, S.Pd', 'PLt. Kepala Sekolah', '', 'assets/img/guru/sumiati.jpeg', 1, 'kepala', NULL),
(2, 'AHBERIAH, S.Pd', 'Guru Kelas', '', 'assets/img/guru/ahberiah.jpeg', 2, 'guru_kelas', 1),
(3, 'SITI JUHAENAH, S.Pd', 'Guru Kelas', '', 'assets/img/guru/sitijuhaenah.jpeg', 3, 'guru_kelas', 1),
(4, 'LISDAWATI, S.Pd', 'Guru Kelas', '', 'assets/img/guru/lisdawati.jpeg', 4, 'guru_kelas', 1),
(5, 'EKA PUSPITASARI, S.Pd.I', 'Guru PAI', '', 'assets/img/guru/ekapuspitasari.jpeg', 5, 'guru_pai', 1),
(6, 'HOLILAH, S.Pd', 'Guru Kelas', '', 'assets/img/guru/holilah.jpeg', 6, 'guru_kelas', 1),
(7, 'ARIYANIH, S.Pd', 'Guru Kelas', '', 'assets/img/guru/arniyanih.jpeg', 7, 'guru_kelas', 1),
(8, 'SUMYANI, S.Pd.I', 'Guru Kelas', '', 'assets/img/guru/sumyani.jpeg', 8, 'guru_kelas', 1),
(9, 'ERYANDI, S.Pd', 'Guru Kelas', '', 'assets/img/guru/eryandi.jpeg', 9, 'guru_kelas', 1),
(10, 'JUNI SRI RAHAYU, S. Pd', 'Guru Kelas', '', 'assets/img/guru/juni.jpeg', 10, 'guru_kelas', 1),

(11, 'DEDE NURHASANAH,, S.Pd', 'Guru Kelas', '', 'assets/img/guru/dedenurhasanah.jpeg', 11, 'guru_kelas', 1),

(12, 'NURAINI, S.Ag', 'Guru PAI', '', 'assets/img/guru/nuraini.jpeg', 12, 'guru_pai', 1),
(13, 'NUR AZIZAH, S.Pd', 'Guru Kelas', '', 'assets/img/guru/nurazizah.jpeg', 13, 'guru_kelas', 1),

(14, 'LINAH FAUZIAH, S.Pd', 'Guru Kelas', '', 'assets/img/guru/linahfauziah.jpeg', 14, 'guru_kelas', 1),

(15, 'DIFKY MAWARDI, S.Pd', 'Guru Kelas', '', 'assets/img/guru/difkymawardi.jpeg', 15, 'guru_kelas', 1),

(16, 'AHMDA ZULFAHMI', 'Administrasi', '', 'assets/img/guru/ahmdazulfahmi.jpeg', 16, 'staff', 1),

(17, 'MUHAMAD RIZKY FIRMANSYAH, S.Pd', 'Guru PJOK', '', 'assets/img/guru/muhamadrizky.jpeg', 17, 'guru_pjok', 1),

(18, 'ANGGIT DITTA HASANAH, S.Pd', 'Guru PJOK', '', 'assets/img/guru/anggita.jpeg', 18, 'guru_pjok', 1),

(19, 'YUNIARSIH, S.E', 'Guru Kelas', '', 'assets/img/guru/yuniarsih.jpeg', 19, 'guru_kelas', 1),
(20, 'MURSAN', 'Penjaga Keamanan', '', 'assets/img/guru/mursan.jpeg', 20, 'staff', 1),
(21, 'NAHALI', 'Kebersihan', '', 'assets/img/guru/nahali.jpeg', 21, 'staff', 1),
(22, 'SITI MAESAROH', 'Kebersihan', '', 'assets/img/guru/sitimaesaroh.jpeg', 22, 'staff', 1),
(23, 'WILDA AMELIA TUSOLIHA', 'Administrasi', '', 'assets/img/guru/wildaamelia.jpeg', 23, 'staff', 1),
(24, 'RIFQI FERIAL KAHFI', 'Pelatih TIK', '', 'assets/img/guru/rifqiferial.jpeg', 24, 'pelatih', 1),
(25, 'TOPIK HIDAYAT', 'Pelatih Marawis', '', 'assets/img/guru/topikhidayat.jpeg', 25, 'pelatih', 1),
(26, 'RAHMAT HIDAYAT', 'Pelatih Pramuka', '', 'assets/img/guru/rahmathidayat.jpeg', 26, 'pelatih', 1),
(27, 'SITI JUMENAH', 'Pelatih Pramuka', '', 'assets/img/guru/sitijumenah.jpeg', 27, 'pelatih', 1),
(28, 'YASMIN AMANAH PUTRI', 'Pelatih B. Inggris', '', 'assets/img/guru/yasminamanah.jpeg', 28, 'pelatih', 1),
(29, 'SITI NURHAYATI NISA', 'Pelatih B. Inggris', '', 'assets/img/guru/sitinurhayati.jpeg', 29, 'pelatih', 1);

-- ============================================================
-- TABEL: guru
-- ============================================================
CREATE TABLE IF NOT EXISTS guru (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  nip VARCHAR(30),
  jabatan VARCHAR(100) NOT NULL,
  jabatan_singkat ENUM('kepala','guru','staff','tata_usaha','honor') DEFAULT 'guru',
  mapel VARCHAR(100),
  bidang VARCHAR(100),
  kelas VARCHAR(50),
  pendidikan VARCHAR(100),
  foto VARCHAR(255) DEFAULT NULL,
  email VARCHAR(100),
  is_active TINYINT DEFAULT 1,
  urutan INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO guru(nama, jabatan, jabatan_singkat, foto, urutan)VALUES

('SUMIATI, S.Pd','PLt. Kepala Sekolah','kepala','assets/img/guru/sumiati.jpeg',1),
('AHBERIAH, S.Pd','Guru Kelas','guru','assets/img/guru/ahberiah.jpeg',2),
('SITI JUHAENAH, S.Pd','Guru Kelas','guru','assets/img/guru/sitijuhaenah.jpeg',3),
('LISDAWATI, S.Pd','Guru Kelas','guru','assets/img/guru/lisdawati.jpeg',4),
('EKA PUSPITASARI, S.Pd.I','Guru PAI','guru','assets/img/guru/ekapuspitasari.jpeg',5),
('HOLILAH, S.Pd','Guru Kelas','guru','assets/img/guru/holilah.jpeg',6),
('ARIYANIH, S.Pd','Guru Kelas','guru','assets/img/guru/arniyanih.jpeg',7),
('SUMYANI, S.Pd.I','Guru Kelas','guru','assets/img/guru/sumyani.jpeg',8),
('ERYANDI, S.Pd','Guru Kelas','guru','assets/img/guru/eryandi.jpeg',9),
('JUNI SRI RAHAYU, S.Pd','Guru Kelas','guru','assets/img/guru/juni.jpeg',10),
('DEDE NURHASANAH, S.Pd','Guru Kelas','guru','assets/img/guru/dedenurhasanah.jpeg',11),
('NURAINI, S.Ag','Guru PAI','guru','assets/img/guru/nuraini.jpeg',12),
('NUR AZIZAH, S.Pd','Guru Kelas','guru','assets/img/guru/nurazizah.jpeg',13),
('LINAH FAUZIAH, S.Pd','Guru Kelas','guru','assets/img/guru/linah_fauziah.jpeg',14),
('DIFKY MAWARDI, S.Pd','Guru Kelas','guru','assets/img/guru/difkymawardi.jpeg',15),
('AHMDA ZULFAHMI','Administrasi','tata_usaha','assets/img/guru/ahmdazulfahmi.jpeg',16),
('MUHAMAD RIZKY FIRMANSYAH, S.Pd','Guru PJOK','guru','assets/img/guru/muhamadrizky.jpeg',17),
('ANGGIT DITTA HASANAH, S.Pd','Guru PJOK','guru','assets/img/guru/anggit_ditta.jpeg',18),
('YUNIARSIH, S.E','Guru Kelas','guru','assets/img/guru/yuniarsih.jpeg',19),
('MURSAN','Penjaga Keamanan','staff','assets/img/guru/mursan.jpeg',20),
('NAHALI','Kebersihan','staff','assets/img/guru/nahali.jpeg',21),
('SITI MAESAROH','Kebersihan','staff','assets/img/guru/siti_maesaroh.jpeg',22),
('WILDA AMELIA TUSOLIHA','Administrasi','tata_usaha','assets/img/guru/wilda_amelia.jpeg',23),
('RIFQI FERIAL KAHFI','Pelatih TIK','honor','assets/img/guru/rifqiferial.jpeg',24),
('TOPIK HIDAYAT','Pelatih Marawis','honor','assets/img/guru/topikhidayat.jpeg',25),
('RAHMAT HIDAYAT','Pelatih Pramuka','honor','assets/img/guru/rahmathidayat.jpeg',26),
('SITI JUMENAH','Pelatih Pramuka','honor','assets/img/guru/sitijumenah.jpeg',27),
('YASMIN AMANAH PUTRI','Pelatih B. Inggris','honor','assets/img/guru/yasminamanah.jpeg',28),
('SITI NURHAYATI NISA','Pelatih B. Inggris','honor','assets/img/guru/sitinurhayati.jpeg',29);
-- ============================================================
-- TABEL: berita
-- ============================================================
CREATE TABLE IF NOT EXISTS berita (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE,
  kategori ENUM('berita','pengumuman','kegiatan') DEFAULT 'berita',
  ringkasan TEXT,
  konten LONGTEXT,
  gambar VARCHAR(255),
  icon VARCHAR(10) DEFAULT '📰',
  penulis VARCHAR(100) DEFAULT 'Admin',
  status ENUM('draft','publish') DEFAULT 'publish',
  views INT DEFAULT 0,
  tanggal DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO berita (judul, slug, kategori, ringkasan, gambar, tanggal, status) VALUES
('Jadwal PPDB Tahun Ajaran 2025/2026 Resmi Dibuka', 'ppdb-2025-2026', 'pengumuman', 'Pendaftaran PPDB dibuka mulai 1 Juni hingga 30 Juni 2025. Persyaratan dan tata cara pendaftaran dapat diunduh di website sekolah atau diambil langsung di kantor TU.', NULL, '2025-05-15', 'publish'),
('Siswa SDN Ketapang Raih Juara I Olimpiade Matematika Kecamatan', 'juara-olimpiade-matematika', 'berita', 'Kebanggaan bagi SDN Ketapang! Riko Andrianto kelas V berhasil meraih Juara I dalam Olimpiade Matematika tingkat Kecamatan yang diikuti 45 peserta dari 15 sekolah.', NULL, '2025-05-10', 'publish'),
('Peringatan Hari Kartini: Pentas Seni dan Lomba Busana Daerah', 'hari-kartini-2025', 'kegiatan', 'SDN Ketapang menggelar serangkaian kegiatan memperingati Hari Kartini dengan penuh semangat dan kreativitas. Seluruh siswa mengenakan busana daerah dari berbagai penjuru Nusantara.', NULL, '2025-04-21', 'publish'),
('Jadwal Ujian Kenaikan Kelas Semester Genap 2024/2025', 'ukk-semester-genap-2025', 'pengumuman', 'Kepada seluruh siswa agar mempersiapkan diri menghadapi Ujian Kenaikan Kelas yang akan dilaksanakan pada 9–13 Juni 2025. Jadwal lengkap dapat dilihat di papan pengumuman sekolah.', NULL, '2025-05-01', 'publish'),
('Kemah Pramuka Gabungan Se-Kecamatan di Bumi Perkemahan Riam Kanan', 'kemah-pramuka-2025', 'kegiatan', 'Siswa kelas IV–VI mengikuti Kemah Pramuka Gabungan selama 3 hari 2 malam. Berbagai kegiatan kepramukaan diikuti dengan antusias tinggi.', NULL, '2025-04-05', 'publish'),
('Program Literasi "Satu Hari Satu Buku" Resmi Diluncurkan', 'literasi-satu-hari-satu-buku', 'berita', 'SDN Ketapang meluncurkan program literasi baru untuk meningkatkan minat baca siswa. Program ini didukung penuh oleh komite sekolah dan orang tua murid.', NULL, '2025-03-20', 'publish');

-- ============================================================
-- TABEL: galeri
-- ============================================================
CREATE TABLE IF NOT EXISTS galeri (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) NOT NULL,
  deskripsi TEXT,
  kategori ENUM('kegiatan','prestasi','fasilitas','ekskul','umum') DEFAULT 'kegiatan',
  gambar VARCHAR(255),
  icon VARCHAR(10) DEFAULT '🏫',
  is_large TINYINT DEFAULT 0,
  urutan INT DEFAULT 0,
  status ENUM('aktif','nonaktif') DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO galeri (judul, deskripsi, kategori, icon, is_large, urutan) VALUES
('Upacara Bendera Senin', 'Upacara bendera rutin setiap Senin pagi di lapangan SDN Ketapang', 'kegiatan', '🏫', 1, 1),
('Juara Olimpiade Sains', 'Siswa SDN Ketapang meraih juara dalam olimpiade sains tingkat kabupaten', 'prestasi', '🏆', 0, 2),
('Lab Komputer', 'Fasilitas laboratorium komputer yang dilengkapi dengan perangkat modern', 'fasilitas', '🖥️', 0, 3),
('Pentas Seni Hari Kartini', 'Penampilan siswa dalam peringatan Hari Kartini dengan busana daerah', 'kegiatan', '🎭', 0, 4),
('Latihan Drumband', 'Latihan rutin drumband SDN Ketapang persiapan pawai', 'ekskul', '🥁', 0, 5),
('Kegiatan Pramuka', 'Kegiatan kepramukaan siswa kelas IV-VI', 'ekskul', '⚜️', 0, 6),
('Lapangan Olahraga', 'Lapangan multifungsi untuk upacara, olahraga, dan kegiatan sekolah', 'fasilitas', '⚽', 0, 7),
('Lomba Mewarnai', 'Lomba mewarnai tingkat kecamatan yang diikuti siswa kelas I-III', 'prestasi', '🎨', 0, 8);

-- ============================================================
-- TABEL: fasilitas
-- ============================================================
CREATE TABLE IF NOT EXISTS fasilitas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  deskripsi VARCHAR(200),
  icon VARCHAR(10) DEFAULT '🏫',
  foto VARCHAR(255) DEFAULT NULL,
  jumlah INT DEFAULT 1,
  kondisi ENUM('baik','cukup','rusak') DEFAULT 'baik',
  urutan INT DEFAULT 0,
  is_active TINYINT DEFAULT 1
);

-- Upgrade: tambah kolom foto jika belum ada
ALTER TABLE fasilitas ADD COLUMN IF NOT EXISTS foto VARCHAR(255) DEFAULT NULL AFTER icon;

INSERT INTO fasilitas (nama, deskripsi, icon, jumlah, urutan) VALUES
('Ruang Kelas', '12 ruang kelas berventilasi baik dan bersih', '📚', 12, 1),
('Perpustakaan', 'Koleksi 3.500+ buku pelajaran dan buku cerita', '📖', 1, 2),
('Lab Komputer', '25 unit komputer untuk pembelajaran TIK', '🖥️', 1, 3),
('Lapangan Olahraga', 'Lapangan multifungsi untuk upacara dan olahraga', '⚽', 1, 4),
('Musholla', 'Tempat ibadah dan praktik keagamaan siswa', '🕌', 1, 5),
('Kantin Sekolah', 'Menyediakan makanan sehat dan bergizi', '🍽️', 1, 6),
('Ruang UKS', 'Unit Kesehatan Sekolah dengan perlengkapan P3K lengkap', '🏥', 1, 7),
('Toilet Siswa', 'Toilet bersih terpisah putra-putri', '🚻', 4, 8),
('Ruang Guru', 'Ruang kerja dan istirahat tenaga pendidik', '🪑', 1, 9),
('Gudang & TU', 'Ruang penyimpanan dan administrasi tata usaha', '📁', 2, 10);

-- ============================================================
-- TABEL: ekskul
-- ============================================================
CREATE TABLE IF NOT EXISTS ekskul (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  icon VARCHAR(10) DEFAULT '🎯',
  foto VARCHAR(255) DEFAULT NULL,
  hari VARCHAR(20) NOT NULL,
  jam_mulai TIME,
  jam_selesai TIME,
  pembina VARCHAR(100),
  deskripsi TEXT,
  is_active TINYINT DEFAULT 1,
  urutan INT DEFAULT 0
);

-- Upgrade: tambah kolom foto jika belum ada
ALTER TABLE ekskul ADD COLUMN IF NOT EXISTS foto VARCHAR(255) DEFAULT NULL AFTER icon;

INSERT INTO ekskul (nama, icon, hari, jam_mulai, jam_selesai, pembina, urutan) VALUES
('Pramuka', '⚜️', "Jum'at", '14:00:00', '16:00:00', 'Kak Ahmad Fauzi', 1),
('Drumband', '🥁', 'Sabtu', '08:00:00', '10:00:00', 'Pak Rudi', 2),
('Sepak Bola', '⚽', 'Rabu', '15:00:00', '17:00:00', 'Pak Yuni Astuti', 3),
('Badminton', '🏸', 'Kamis', '15:00:00', '17:00:00', 'Pak Yuni Astuti', 4),
('Seni Lukis', '🎨', 'Selasa', '14:00:00', '16:00:00', 'Bu Dewi', 5),
('Tari Daerah', '💃', 'Rabu', '14:00:00', '16:00:00', 'Bu Rina', 6),
('Tahfidz', '📖', 'Senin', '06:30:00', '07:15:00', 'Ustadz Malik', 7),
('TIK / Komputer', '🖥️', 'Kamis', '14:00:00', '16:00:00', 'Pak Hendra', 8),
('Seni Baca Al-Qur\'an', '🎤', 'Senin', '14:00:00', '16:00:00', 'Ustadz Malik', 9);

-- ============================================================
-- TABEL: prestasi
-- ============================================================
CREATE TABLE IF NOT EXISTS prestasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_prestasi VARCHAR(200) NOT NULL,
  deskripsi VARCHAR(300),
  tingkat ENUM('Kecamatan','Kabupaten/Kota','Provinsi','Nasional','Internasional') DEFAULT 'Kecamatan',
  juara VARCHAR(50),
  medali VARCHAR(5) DEFAULT '🏆',
  gambar VARCHAR(255) DEFAULT NULL,
  raih_oleh VARCHAR(150),
  tahun YEAR NOT NULL,
  kategori ENUM('akademik','olahraga','seni','agama','pramuka','lainnya') DEFAULT 'akademik',
  is_featured TINYINT DEFAULT 0,
  urutan INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Upgrade: tambah kolom gambar jika belum ada
ALTER TABLE prestasi ADD COLUMN IF NOT EXISTS gambar VARCHAR(255) DEFAULT NULL AFTER medali;

INSERT INTO prestasi (nama_prestasi, deskripsi, tingkat, juara, medali, raih_oleh, tahun, kategori, is_featured, urutan) VALUES
('Olimpiade Matematika', 'Diraih oleh Riko Andrianto (Kelas V) — Juara I', 'Kecamatan', 'Juara I', '🥇', 'Riko Andrianto', 2025, 'akademik', 1, 1),
('Lomba Pidato Bahasa Indonesia', 'Diraih oleh Sari Lestari (Kelas VI) — Juara II', 'Kabupaten/Kota', 'Juara II', '🥈', 'Sari Lestari', 2024, 'akademik', 1, 2),
('Pawai Drumband HUT RI', 'Tim Drumband SDN Ketapang — Peringkat 3 Terbaik', 'Kecamatan', 'Juara III', '🥉', 'Tim Drumband', 2024, 'seni', 0, 3),
('Lomba Cerdas Cermat PAI', 'Diraih oleh Tim kelas VI — Juara I Tingkat Kecamatan', 'Kecamatan', 'Juara I', '🏆', 'Tim Kelas VI', 2024, 'agama', 1, 4),
('O2SN Cabang Lari 100m', 'Diraih oleh Doni Pratama (Kelas V) — Juara II', 'Kecamatan', 'Juara II', '🎖️', 'Doni Pratama', 2023, 'olahraga', 0, 5),
('Lomba Mewarnai Tingkat SD', 'Diraih oleh Putri Maharani (Kelas II) — Juara I', 'Kecamatan', 'Juara I', '⭐', 'Putri Maharani', 2023, 'seni', 0, 6);

-- ============================================================
-- TABEL: buku_tamu
-- ============================================================
CREATE TABLE IF NOT EXISTS buku_tamu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  hp VARCHAR(20),
  email VARCHAR(100),
  keperluan VARCHAR(100) NOT NULL,
  pesan TEXT NOT NULL,
  ip_address VARCHAR(45),
  status ENUM('belum_dibaca','dibaca','dibalas') DEFAULT 'belum_dibaca',
  balasan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================================
-- TABEL: akreditasi
-- ============================================================
CREATE TABLE IF NOT EXISTS akreditasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nilai ENUM('A','B','C') NOT NULL,
  skor DECIMAL(5,2),
  predikat VARCHAR(50),
  tanggal_penilaian DATE,
  berlaku_sampai DATE,
  nomor_sk VARCHAR(100),
  lembaga VARCHAR(100) DEFAULT 'BAN-S/M',
  keterangan TEXT,
  dokumen_url VARCHAR(255)
);

INSERT INTO akreditasi (nilai, skor, predikat, tanggal_penilaian, berlaku_sampai, lembaga) VALUES
('A', 91.50, 'Unggul', '2022-09-15', '2027-09-14', 'BAN-S/M');

-- ============================================================
-- TABEL: data_siswa
-- ============================================================
CREATE TABLE IF NOT EXISTS data_siswa (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tahun_ajaran VARCHAR(12)   NOT NULL DEFAULT '2025/2026',
  kelas        VARCHAR(20)   NOT NULL,
  tingkat      TINYINT       NOT NULL DEFAULT 1,
  rombel       VARCHAR(5)    NOT NULL DEFAULT 'A',
  laki_laki    SMALLINT      NOT NULL DEFAULT 0,
  perempuan    SMALLINT      NOT NULL DEFAULT 0,
  wali_kelas   VARCHAR(100)  DEFAULT NULL,
  catatan      TEXT          DEFAULT NULL,
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_tahun_kelas (tahun_ajaran, kelas)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO data_siswa (tahun_ajaran, kelas, tingkat, rombel, laki_laki, perempuan) VALUES
('2025/2026', '1A', 1, 'A', 15, 15),
('2025/2026', '1B', 1, 'B', 14, 16),
('2025/2026', '2A', 2, 'A', 16, 14),
('2025/2026', '2B', 2, 'B', 15, 15),
('2025/2026', '3A', 3, 'A', 14, 16),
('2025/2026', '3B', 3, 'B', 15, 15),
('2025/2026', '4A', 4, 'A', 16, 14),
('2025/2026', '4B', 4, 'B', 15, 15),
('2025/2026', '5A', 5, 'A', 14, 16),
('2025/2026', '5B', 5, 'B', 15, 14),
('2025/2026', '6A', 6, 'A', 16, 14),
('2025/2026', '6B', 6, 'B', 14, 16);

-- ============================================================
-- INDEXES untuk performa
-- ============================================================
CREATE INDEX idx_berita_kategori ON berita(kategori);
CREATE INDEX idx_berita_status ON berita(status);
CREATE INDEX idx_berita_tanggal ON berita(tanggal DESC);
CREATE INDEX idx_guru_jabatan ON guru(jabatan_singkat);
CREATE INDEX idx_galeri_kategori ON galeri(kategori);
CREATE INDEX idx_prestasi_tahun ON prestasi(tahun DESC);
CREATE INDEX idx_buku_tamu_status ON buku_tamu(status);

-- ============================================================
-- VIEW: berita_terbaru (shortcut untuk query umum)
-- ============================================================
CREATE OR REPLACE VIEW berita_terbaru AS
SELECT id, judul, slug, kategori, ringkasan, gambar, icon, tanggal,
       DATE_FORMAT(tanggal, '%d %M %Y') AS tanggal_format
FROM berita
WHERE status = 'publish'
ORDER BY tanggal DESC;

-- ============================================================
-- SELESAI
-- ============================================================
SELECT 'Database sdn_ketapang berhasil dibuat!' AS status;
