<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
<meta name="theme-color" content="#B71C1C"/>
<meta name="mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
<meta name="format-detection" content="telephone=no"/>
<title>SDN Ketapang — Sekolah Dasar Negeri Ketapang</title>
<meta name="description" content="Website resmi SDN Ketapang - Sekolah Dasar Negeri Ketapang. Informasi profil, berita, guru, galeri, dan pendaftaran siswa baru."/>
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700;1,500&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body>

<!-- TOPBAR -->
<div id="topbar">
  <span>📍 Jl. Raya Ketapang RT 05/05, Ketapang, Cipondoh, Tangerang,Banten</span>
  <div class="tb-right">
    <span>📞 082211617039</span>
    <span>✉️ sdnketapan9@gmail.com</span>
  </div>
</div>

<!-- NAVBAR -->
<nav id="navbar">
  <div class="nav-brand">
    <div class="nav-name">
      SDN Ketapang
      <small>Sekolah Dasar Negeri</small>
    </div>
  </div>
  <ul class="nav-menu" id="navMenu">
    <li><a href="#berita" class="nav-link">Berita</a></li>
    <li><a href="#profil" class="nav-link">Profil</a></li>
    <li><a href="#guru" class="nav-link">Guru</a></li>
    <li><a href="#galeri" class="nav-link">Galeri</a></li>
    <li><a href="#fasilitas" class="nav-link">Fasilitas</a></li>
    <li><a href="#prestasi" class="nav-link">Prestasi</a></li>
    <li><a href="#buku-tamu" class="nav-link">Kontak</a></li>
  </ul>
  <button class="nav-cta" onclick="scrollToSection('#buku-tamu')">Hubungi Kami</button>
  <button class="hamburger" id="hamburger" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- HERO — background foto sekolah -->
<!-- HERO -->
<section id="hero">

  <div class="hero-bg-school">
  <img src="assets/foto.jpg" alt="Foto Sekolah">
</div>

<div class="hero-overlay"></div>

  <div class="hero-grid container">
    <div class="hero-text">

      <h1>
        Selamat Datang di<br>
        <em>SDN Ketapang</em>
      </h1>
    <div class="hero-badge">⭐ Akreditasi A | Berdiri Sejak 1975</div>

      <p>
        Membentuk generasi cerdas, berkarakter, dan berprestasi.
        Bersama kami, setiap anak berkembang menjadi pribadi yang
        unggul dan berakhlak mulia.
      </p>

      <div class="hero-btns">
        <a href="#profil" class="btn-primary">Tentang Sekolah</a>
        <a href="#buku-tamu" class="btn-outline">Hubungi Kami</a>
      </div>
    </div>

  </div>

</section>

<!-- PEMBIASAAN SISWA -->
<section id="pembiasaan" class="section">
  <div class="container">
    <div class="sec-tag">Program Harian</div>
    <h2 class="sec-title">Pembiasaan<br>Siswa</h2>
    <div class="bar"></div>
    <p class="sec-desc">Kegiatan rutin pembiasaan akhlak mulia dan kedisiplinan yang dilaksanakan setiap hari sekolah.</p>
    <div class="pembiasaan-grid">
      <div class="pb-card">
        <div class="pb-icon">🕌</div>
        <div class="pb-content">
          <div class="pb-time">07.45 – 07.15 WIB</div>
          <h4>Sholat Dhuha Berjamaah</h4>
          <p>Dilaksanakan setiap hari Senin – Jumat sebelum kegiatan belajar dimulai di Musholla sekolah. Dipimpin bergiliran oleh guru agama dan wali kelas.</p>
        </div>
      </div>
      <div class="pb-card">
        <div class="pb-icon">📖</div>
        <div class="pb-content">
          <div class="pb-time">07.00 – 07.15 WIB</div>
          <h4>Tadarus Al-Qur'an</h4>
          <p>Membaca Al-Qur'an bersama selama 15 menit setiap pagi sebagai pembiasaan cinta terhadap kitab suci sebelum memulai pelajaran.</p>
        </div>
      </div>
      <div class="pb-card">
        <div class="pb-icon">🏫</div>
        <div class="pb-content">
          <div class="pb-time">Setiap Senin</div>
          <h4>Upacara Bendera</h4>
          <p>Upacara rutin setiap Senin pagi untuk menumbuhkan rasa nasionalisme, disiplin, dan cinta tanah air pada seluruh warga sekolah.</p>
        </div>
      </div>
      <div class="pb-card">
        <div class="pb-icon">📚</div>
        <div class="pb-content">
          <div class="pb-time">15 Menit Sebelum Belajar</div>
          <h4>Literasi Pagi</h4>
          <p>Program membaca buku non-pelajaran selama 15 menit setiap hari sebagai upaya meningkatkan budaya literasi siswa.</p>
        </div>
      </div>
      <div class="pb-card">
        <div class="pb-icon">🧹</div>
        <div class="pb-content">
          <div class="pb-time">Setiap Hari</div>
          <h4>Piket Kebersihan Kelas</h4>
          <p>Siswa bergiliran membersihkan kelas sebelum dan sesudah pelajaran untuk menumbuhkan rasa tanggung jawab dan kepedulian lingkungan.</p>
        </div>
      </div>
      <div class="pb-card">
        <div class="pb-icon">🤝</div>
        <div class="pb-content">
          <div class="pb-time">Setiap Hari</div>
          <h4>5S (Senyum, Salam, Sapa, Sopan, Santun)</h4>
          <p>Pembiasaan karakter unggul melalui budaya 5S kepada guru, sesama siswa, dan warga sekolah setiap hari.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- BERITA & PENGUMUMAN -->
<section class="section" id="berita">
  <div class="container">
    <div class="sec-tag">Informasi Terkini</div>
    <h2 class="sec-title">Berita &amp;<br>Pengumuman</h2>
    <div class="bar"></div>
    <div class="berita-tabs">
      <button class="tab-btn active" onclick="filterBerita('semua',this)">Semua</button>
      <button class="tab-btn" onclick="filterBerita('berita',this)">Berita</button>
      <button class="tab-btn" onclick="filterBerita('pengumuman',this)">Pengumuman</button>
      <button class="tab-btn" onclick="filterBerita('kegiatan',this)">Kegiatan</button>
    </div>
    <div class="berita-grid" id="beritaGrid">
      <div class="loading-state" style="grid-column:1/-1">
        <div class="loading-spinner"></div>Memuat berita...
      </div>
    </div>
  </div>
</section>

<!-- PROFIL SEKOLAH -->
<section class="section" id="profil">
  <div class="container">
    <div class="sec-tag">Tentang Kami</div>
    <h2 class="sec-title">Profil<br>Sekolah</h2>
    <div class="bar"></div>
    <div class="profil-grid">
      <div class="profil-nav">
        <button class="profil-nav-btn active" onclick="showPanel('sejarah',this)">
          <span class="icon">📜</span> Sejarah Singkat
        </button>
        <button class="profil-nav-btn" onclick="showPanel('visi',this)">
          <span class="icon">🔭</span> Visi
        </button>
        <button class="profil-nav-btn" onclick="showPanel('misi',this)">
          <span class="icon">🎯</span> Misi
        </button>
        <button class="profil-nav-btn" onclick="showPanel('tujuan',this)">
          <span class="icon">🏆</span> Tujuan
        </button>
        <button class="profil-nav-btn" onclick="showPanel('struktur',this)">
          <span class="icon">🏛️</span> Data personil SD KETAPANG
        </button>
        <button class="profil-nav-btn" onclick="showPanel('akreditasi',this)">
          <span class="icon">⭐</span> Akreditasi
        </button>
      </div>
      <div class="profil-content">
        <div class="profil-panel active" id="panel-sejarah">
          <div class="loading-state"><div class="loading-spinner"></div>Memuat data...</div>
        </div>
        <div class="profil-panel" id="panel-visi">
          <div class="loading-state"><div class="loading-spinner"></div>Memuat data...</div>
        </div>
        <div class="profil-panel" id="panel-misi">
          <div class="loading-state"><div class="loading-spinner"></div>Memuat data...</div>
        </div>
        <div class="profil-panel" id="panel-tujuan">
          <div class="loading-state"><div class="loading-spinner"></div>Memuat data...</div>
        </div>
        <div class="profil-panel" id="panel-struktur">
          <div class="loading-state"><div class="loading-spinner"></div>Memuat data...</div>
        </div>
        <div class="profil-panel" id="panel-akreditasi">
          <div class="loading-state"><div class="loading-spinner"></div>Memuat data...</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- GURU & STAFF -->
<section class="section" id="guru">
  <div class="container">
    <div class="sec-tag">Tenaga Pendidik</div>
    <h2 class="sec-title">Data Personil<br>SDN Ketapang</h2>
    <div class="bar"></div>
    <div class="guru-filter">
      <button class="filter-btn active" onclick="filterGuru('semua',this)">Semua</button>
      <button class="filter-btn" onclick="filterGuru('kepala',this)">Kepala Sekolah</button>
      <button class="filter-btn" onclick="filterGuru('guru',this)">Guru</button>
      <button class="filter-btn" onclick="filterGuru('tata_usaha',this)">Tata Usaha</button>
      <button class="filter-btn" onclick="filterGuru('staff',this)">Staff</button>
    </div>
    <div class="guru-grid" id="guruGrid">
      <div class="loading-state" style="grid-column:1/-1">
        <div class="loading-spinner"></div>Memuat data guru...
      </div>
    </div>
  </div>
</section>

<!-- GALERI -->
<section class="section" id="galeri">
  <div class="container">
    <div class="sec-tag">Dokumentasi</div>
    <h2 class="sec-title">Galeri<br>Foto</h2>
    <div class="bar"></div>
    <div class="galeri-tabs">
      <button class="galeri-tab active" onclick="filterGaleri('semua',this)">Semua</button>
      <button class="galeri-tab" onclick="filterGaleri('kegiatan',this)">Kegiatan</button>
      <button class="galeri-tab" onclick="filterGaleri('prestasi',this)">Prestasi</button>
      <button class="galeri-tab" onclick="filterGaleri('fasilitas',this)">Fasilitas</button>
      <button class="galeri-tab" onclick="filterGaleri('ekskul',this)">Ekskul</button>
    </div>
    <div class="galeri-grid" id="galeriGrid">
      <div class="loading-state" style="grid-column:1/-1;color:rgba(149, 149, 149, 0.5)">
        <div class="loading-spinner"></div>Memuat galeri...
      </div>
    </div>
  </div>
</section>

<!-- FASILITAS & EKSKUL -->
<section class="section" id="fasilitas">
  <div class="container">
    <div class="sec-tag">Sarana &amp; Pengembangan</div>
    <h2 class="sec-title">Fasilitas &amp;<br>Ekstrakurikuler</h2>
    <div class="bar"></div>
    <div class="fas-ekskul-grid">
      <div>
        <h3 style="font-family:'Lora',serif;font-size:20px;margin-bottom:4px;">Fasilitas Sekolah</h3>
        <p style="color:var(--muted);font-size:14px;">Sarana pendukung kegiatan belajar mengajar yang lengkap dan terawat.</p>
        <div class="fas-list" id="fasilitasList">
          <div class="loading-state"><div class="loading-spinner"></div></div>
        </div>
      </div>
      <div class="ekskul-wrap">
        <h3 style="font-family:'Lora',serif;font-size:20px;margin-bottom:4px;">Ekstrakurikuler Aktif</h3>
        <p style="color:var(--muted);font-size:14px;margin-bottom:20px;">Pengembangan bakat dan minat di luar kegiatan akademik.</p>
        <div class="ekskul-chips" id="ekskulChips">
          <div class="loading-state"><div class="loading-spinner"></div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PRESTASI -->
<section class="section" id="prestasi">
  <div class="container">
    <div class="sec-tag">Kebanggaan Kami</div>
    <h2 class="sec-title">Prestasi<br>Sekolah</h2>
    <div class="bar"></div>
    <p class="sec-desc">Pencapaian yang diraih oleh siswa-siswi dan sekolah dalam berbagai bidang.</p>
    <div class="prestasi-grid" id="prestasiGrid">
      <div class="loading-state" style="grid-column:1/-1">
        <div class="loading-spinner"></div>Memuat data prestasi...
      </div>
    </div>
  </div>
</section>

<!-- BUKU TAMU / KONTAK -->
<section class="section" id="buku-tamu">
  <div class="container">
    <div class="sec-tag">Hubungi Kami</div>
    <h2 class="sec-title">Buku Tamu &amp;<br>Kontak Digital</h2>
    <div class="bar"></div>
    <p class="sec-desc">Kirimkan pesan, pertanyaan, atau saran Anda langsung kepada kami.</p>
    <div class="form-grid">
      <div class="form-wrap">
        <h3 style="font-family:'Lora',serif;color:rgb(10, 8, 8);margin-bottom:24px;">Kirim Pesan</h3>
        <div class="form-group">
          <label>Nama Lengkap *</label>
          <input type="text" id="f-nama" placeholder="Masukkan nama lengkap Anda"/>
        </div>
        <div class="form-group">
          <label>Nomor HP / WhatsApp</label>
          <input type="tel" id="f-hp" placeholder="Contoh: 0812XXXXXXXX"/>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" id="f-email" placeholder="nama@email.com"/>
        </div>
        <div class="form-group">
          <label>Keperluan *</label>
          <select id="f-keperluan">
            <option value="">-- Pilih keperluan --</option>
            <option>Informasi Pendaftaran / PPDB</option>
            <option>Pertanyaan tentang Kurikulum</option>
            <option>Pertanyaan tentang Fasilitas</option>
            <option>Kerjasama / Instansi</option>
            <option>Saran &amp; Masukan</option>
            <option>Lainnya</option>
          </select>
        </div>
        <div class="form-group">
          <label>Pesan *</label>
          <textarea id="f-pesan" placeholder="Tuliskan pesan atau pertanyaan Anda di sini..."></textarea>
        </div>
        <button class="form-submit" onclick="submitForm()">Kirim Pesan ✉️</button>
        <div class="success-msg" id="successMsg">
          ✅ Pesan Anda berhasil dikirim! Kami akan merespons dalam 1–2 hari kerja.
        </div>
        <div class="error-msg" id="errorMsg"></div>
      </div>
      <div class="form-info">
        <h3>Informasi Kontak</h3>
        <div class="info-item">
          <div class="info-icon">📍</div>
          <div class="info-detail">
            <h4>Alamat</h4>
            <p>Jl. Raya Ketapang RT 05/05,<br>Ketapang, Kec. Cipondoh,<br>Kab. Tangerang, Banten<br>Kode Pos: 15147</p>
          </div>
        </div>
        <div class="info-item">
          <div class="info-icon">📞</div>
          <div class="info-detail">
            <h4>Telepon</h4>
            <p>(082211617039)</p>
          </div>
        </div>
        <div class="info-item">
          <div class="info-icon">✉️</div>
          <div class="info-detail">
            <h4>Email Resmi</h4>
            <p>sdnketapan9@gmail.com</p>
          </div>
        </div>
        <div class="info-item">
          <div class="info-icon">⏰</div>
          <div class="info-detail">
            <h4>Jam Operasional</h4>
            <p>Senin – Jumat: 07.00 – 13.30 WIB<br>Sabtu: 07.00 – 11.00 WIB</p>
          </div>
        </div>
        <div id="kontak">
          <div class="map-frame">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.6052531416385!2d106.6941895!3d-6.1835535!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f9b478cfcbf7%3A0x43491718707b726e!2sSekolah%20Dasar%20Negeri%20Ketapang!5e0!3m2!1sid!2sid!4v1781436109787!5m2!1sid!2sid" 
            width="100%" 
            height="400" 
            style="border:0;" 
            allowfullscreen="" >
            </iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div>
    <div class="f-brand">SDN Ketapang</div>
    <div style="font-size:12px;margin-top:4px;">© 2025 SDN Ketapang. Semua hak dilindungi.</div>
  </div>
  <div class="f-links">
    <a href="#berita">Berita</a>
    <a href="#profil">Profil</a>
    <a href="#guru">Guru</a>
    <a href="#galeri">Galeri</a>
    <a href="#buku-tamu">Kontak</a>
  </div>
</footer>

<!-- MODAL GALERI -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModal(event)">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <div class="modal-img-placeholder" id="modalImg">🏫</div>
    <h3 id="modalTitle">Judul Foto</h3>
    <p id="modalDesc">Deskripsi foto.</p>
  </div>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>
