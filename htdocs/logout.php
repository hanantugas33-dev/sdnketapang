# 🏫 SDN Ketapang — Website Sekolah

Website resmi SDN Ketapang dengan arsitektur **HTML + CSS + JS** terpisah dan backend **PHP + MySQL**.

---

## 📁 Struktur Folder

```
sdn-ketapang/
├── index.html              ← Halaman utama
├── database.sql            ← Schema + data awal MySQL
├── README.md
│
├── assets/
│   ├── css/
│   │   └── style.css       ← Semua styling
│   └── js/
│       └── script.js       ← Interaktivitas + fetch ke API
│
└── api/                    ← Backend PHP (REST API)
    ├── config.php          ← Koneksi database (edit di sini)
    ├── berita.php          ← GET /api/berita.php
    ├── guru.php            ← GET /api/guru.php
    ├── galeri.php          ← GET /api/galeri.php
    ├── fasilitas.php       ← GET /api/fasilitas.php
    ├── prestasi.php        ← GET /api/prestasi.php
    ├── profil.php          ← GET /api/profil.php
    └── buku_tamu.php       ← POST /api/buku_tamu.php
```

---

## ⚙️ Cara Setup

### 1. Kebutuhan Server
- PHP 7.4+ (disarankan PHP 8.x)
- MySQL 5.7+ atau MariaDB 10.3+
- Web server: Apache / Nginx / XAMPP / Laragon

### 2. Import Database
```sql
-- Di phpMyAdmin atau terminal MySQL:
mysql -u root -p < database.sql

-- Atau jalankan manual di phpMyAdmin:
-- File > Import > database.sql
```

### 3. Edit Konfigurasi Database
Buka file `api/config.php` dan sesuaikan:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // ← user MySQL kamu
define('DB_PASS', '');          // ← password MySQL kamu
define('DB_NAME', 'sdn_ketapang');
```

### 4. Jalankan di XAMPP / Laragon
- Letakkan folder `sdn-ketapang/` di `htdocs/` (XAMPP) atau `www/` (Laragon)
- Akses: `http://localhost/sdn-ketapang/`

---

## 🗄️ Tabel Database

| Tabel | Fungsi |
|-------|--------|
| `profil_sekolah` | Info sekolah, visi, misi, sejarah |
| `struktur_organisasi` | Bagan organisasi sekolah |
| `guru` | Data guru dan staff |
| `berita` | Berita, pengumuman, kegiatan |
| `galeri` | Foto-foto dokumentasi |
| `fasilitas` | Sarana dan prasarana |
| `ekskul` | Ekstrakurikuler + jadwal |
| `prestasi` | Prestasi siswa dan sekolah |
| `buku_tamu` | Pesan dari pengunjung website |
| `akreditasi` | Riwayat akreditasi sekolah |

---

## 🔌 API Endpoints

| Endpoint | Method | Parameter | Keterangan |
|----------|--------|-----------|------------|
| `/api/berita.php` | GET | `kategori`, `limit` | Daftar berita |
| `/api/guru.php` | GET | `jabatan` | Daftar guru/staff |
| `/api/galeri.php` | GET | `kategori`, `limit` | Daftar foto |
| `/api/fasilitas.php` | GET | — | Fasilitas + ekskul |
| `/api/prestasi.php` | GET | `limit` | Daftar prestasi |
| `/api/profil.php` | GET | `action` (stats/struktur/all) | Info sekolah |
| `/api/buku_tamu.php` | POST | JSON body | Simpan pesan tamu |

---

## 🖼️ Upload Foto (Opsional)

Untuk foto guru dan galeri, simpan file gambar di:
- `assets/img/guru/` untuk foto guru
- `assets/img/galeri/` untuk foto galeri

Lalu isi kolom `foto` / `gambar` di database dengan path relatif, contoh:
```
assets/img/guru/pak-budi.jpg
```

---

## 🛠️ Pengembangan Selanjutnya

- [ ] Halaman admin (CRUD berita, guru, galeri)
- [ ] Login admin dengan session PHP
- [ ] Upload gambar dari admin panel
- [ ] Pagination berita
- [ ] Detail berita (berita-detail.php)
- [ ] Integrasi Google Maps embed
- [ ] Notifikasi email buku tamu (PHPMailer)

---

## 📞 Kontak

Untuk pertanyaan teknis website, hubungi tim IT / pengembang.
