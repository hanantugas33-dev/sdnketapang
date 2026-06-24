<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Detail Berita — SDN Ketapang</title>
<meta name="description" content="Detail berita dan pengumuman SDN Ketapang"/>
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700;1,500&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="assets/css/style.css"/>
<style>
  /* ===== DETAIL PAGE STYLES ===== */
  .detail-wrapper {
    max-width: 860px;
    margin: 0 auto;
    padding: 40px 20px 60px;
  }
  .breadcrumb {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }
  .breadcrumb a {
    color: var(--red);
    text-decoration: none;
    font-weight: 500;
  }
  .breadcrumb a:hover { text-decoration: underline; }
  .breadcrumb span { color: var(--muted); }

  .detail-header { margin-bottom: 28px; }
  .detail-cat {
    display: inline-block;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    text-transform: capitalize;
    margin-bottom: 14px;
  }
  .detail-title {
    font-family: 'Lora', serif;
    font-size: clamp(22px, 4vw, 34px);
    font-weight: 700;
    color: var(--dark);
    line-height: 1.35;
    margin-bottom: 14px;
  }
  .detail-meta {
    font-size: 13px;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
  }
  .detail-meta span { display: flex; align-items: center; gap: 5px; }

  .detail-cover {
    width: 100%;
    height: 380px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 36px;
    background: var(--cream);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 72px;
  }
  .detail-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 12px;
    display: block;
  }
  .detail-cover-emoji {
    width: 100%;
    height: 380px;
    background: var(--cream);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 80px;
    margin-bottom: 36px;
  }

  .detail-body {
    font-size: 16px;
    line-height: 1.85;
    color: var(--text);
  }
  .detail-body p { margin-bottom: 18px; }
  .detail-body h2, .detail-body h3 {
    font-family: 'Lora', serif;
    color: var(--dark);
    margin: 28px 0 12px;
  }
  .detail-divider {
    border: none;
    border-top: 1px solid #e5e7eb;
    margin: 40px 0;
  }

  /* ===== BERITA TERKAIT ===== */
  .terkait-section { margin-top: 48px; }
  .terkait-title {
    font-family: 'Lora', serif;
    font-size: 22px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 24px;
    position: relative;
    padding-left: 14px;
  }
  .terkait-title::before {
    content: '';
    position: absolute;
    left: 0; top: 4px;
    width: 4px; height: 80%;
    background: var(--red);
    border-radius: 2px;
  }
  .terkait-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 20px;
  }
  .terkait-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    text-decoration: none;
    color: inherit;
    display: block;
  }
  .terkait-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 28px rgba(0,0,0,.12);
  }
  .terkait-img {
    height: 140px;
    background: var(--cream);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    overflow: hidden;
  }
  .terkait-img img { width: 100%; height: 100%; object-fit: cover; }
  .terkait-body { padding: 14px 16px 16px; }
  .terkait-body h5 {
    font-family: 'Lora', serif;
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    line-height: 1.4;
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .terkait-body small { font-size: 12px; color: var(--muted); }

  /* ===== BACK BUTTON ===== */
  .btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--red);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s;
    margin-bottom: 32px;
  }
  .btn-back:hover { background: var(--red2); }

  /* ===== LOADING / ERROR STATES ===== */
  .page-loading, .page-error {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 50vh;
    gap: 16px;
    color: var(--muted);
    text-align: center;
  }
  .page-loading .loading-spinner {
    width: 40px; height: 40px;
    border: 3px solid #e5e7eb;
    border-top-color: var(--red);
    border-radius: 50%;
    animation: spin .7s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
  .page-error h3 { font-size: 48px; margin-bottom: 4px; }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 600px) {
    .detail-cover, .detail-cover-emoji { height: 220px; }
    .terkait-grid { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>

<!-- TOPBAR -->
<div id="topbar">
  <span>📍 Jl. Ketapang No. 1, Ketapang</span>
  <div class="tb-right">
    <span>📞 (0534) 123456</span>
    <span>✉️ sdnketapang@sch.id</span>
  </div>
</div>

<!-- NAVBAR -->
<nav id="navbar">
  <div class="nav-brand">
    <div class="nav-logo">SDN<br>KTP</div>
    <div class="nav-name">
      SDN Ketapang
      <small>Sekolah Dasar Negeri</small>
    </div>
  </div>
  <ul class="nav-menu" id="navMenu">
    <li><a href="index.html#berita" class="nav-link">Berita</a></li>
    <li><a href="index.html#profil" class="nav-link">Profil</a></li>
    <li><a href="index.html#guru" class="nav-link">Guru</a></li>
    <li><a href="index.html#galeri" class="nav-link">Galeri</a></li>
    <li><a href="index.html#fasilitas" class="nav-link">Fasilitas</a></li>
    <li><a href="index.html#prestasi" class="nav-link">Prestasi</a></li>
    <li><a href="index.html#buku-tamu" class="nav-link">Kontak</a></li>
  </ul>
  <button class="nav-cta" onclick="window.location.href='index.html#buku-tamu'">Hubungi Kami</button>
  <button class="hamburger" id="hamburger" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- MAIN CONTENT -->
<main style="padding-top: 80px; background: var(--white); min-height: 100vh;">
  <div class="detail-wrapper" id="mainContent">
    <div class="page-loading">
      <div class="loading-spinner"></div>
      <p>Memuat berita...</p>
    </div>
  </div>
</main>

<!-- FOOTER -->
<footer style="background:var(--dark2);color:#aaa;padding:28px 20px;text-align:center;font-size:13px;">
  <p style="margin:0;">© 2025 SDN Ketapang — Sekolah Dasar Negeri Ketapang. Hak Cipta Dilindungi.</p>
</footer>

<script>
const API_BASE = 'api/';
const catClass = { berita: 'cat-berita', pengumuman: 'cat-pengumuman', kegiatan: 'cat-kegiatan' };

function toggleMenu() {
  document.getElementById('navMenu').classList.toggle('open');
}

// Ambil ID dari query string
function getParam(key) {
  return new URLSearchParams(window.location.search).get(key);
}

// Render gambar atau emoji
function renderCover(berita) {
  if (berita.gambar) {
    return `<div class="detail-cover"><img src="uploads/${berita.gambar}" alt="${berita.judul}" onerror="this.parentElement.innerHTML='<span style=font-size:72px>${berita.icon || '📰'}</span>'"/></div>`;
  }
  return `<div class="detail-cover-emoji">${berita.icon || '📰'}</div>`;
}

// Render berita terkait
function renderTerkait(list) {
  if (!list || list.length === 0) return '';
  const cards = list.map(b => {
    const imgEl = b.gambar
      ? `<img src="uploads/${b.gambar}" alt="${b.judul}" onerror="this.parentElement.innerHTML='<span style=font-size:36px>${b.icon || '📰'}</span>'">`
      : (b.icon || '📰');
    return `
      <a href="berita-detail.php?id=${b.id}" class="terkait-card">
        <div class="terkait-img">${imgEl}</div>
        <div class="terkait-body">
          <span class="detail-cat ${catClass[b.kategori] || 'cat-berita'}" style="font-size:11px;padding:3px 10px;">${b.kategori}</span>
          <h5>${b.judul}</h5>
          <small>📅 ${b.tanggal_format}</small>
        </div>
      </a>`;
  }).join('');
  return `
    <hr class="detail-divider"/>
    <div class="terkait-section">
      <h3 class="terkait-title">Berita Terkait</h3>
      <div class="terkait-grid">${cards}</div>
    </div>`;
}

// Format isi berita: bungkus newline jadi paragraf
function formatIsi(isi) {
  if (!isi) return '<p><em>Tidak ada konten tersedia.</em></p>';
  // Jika sudah ada tag HTML, tampilkan langsung
  if (/<[a-z][\s\S]*>/i.test(isi)) return isi;
  // Jika plain text, bungkus tiap paragraf
  return isi.split(/\n\n+/).map(p => `<p>${p.replace(/\n/g, '<br>')}</p>`).join('');
}

async function loadDetail() {
  const id = getParam('id');
  const container = document.getElementById('mainContent');

  if (!id || isNaN(id)) {
    container.innerHTML = `
      <div class="page-error">
        <h3>⚠️</h3>
        <p>ID berita tidak valid.</p>
        <a href="index.html#berita" class="btn-back">← Kembali ke Beranda</a>
      </div>`;
    return;
  }

  try {
    const res = await fetch(`${API_BASE}berita_detail.php?id=${id}`);
    if (!res.ok) throw new Error(res.status);
    const b = await res.json();

    if (b.error) throw new Error(b.error);

    // Update title
    document.title = `${b.judul} — SDN Ketapang`;

    container.innerHTML = `
      <nav class="breadcrumb">
        <a href="index.html">Beranda</a>
        <span>›</span>
        <a href="index.html#berita">Berita & Pengumuman</a>
        <span>›</span>
        <span>${b.judul}</span>
      </nav>

      <a href="index.html#berita" class="btn-back">← Kembali</a>

      <div class="detail-header">
        <span class="detail-cat ${catClass[b.kategori] || 'cat-berita'}">${b.kategori.charAt(0).toUpperCase() + b.kategori.slice(1)}</span>
        <h1 class="detail-title">${b.judul}</h1>
        <div class="detail-meta">
          <span>📅 ${b.tanggal_format}</span>
          <span>🏫 SDN Ketapang</span>
        </div>
      </div>

      ${renderCover(b)}

      <div class="detail-body">
        ${formatIsi(b.konten || b.ringkasan)}
      </div>

      ${renderTerkait(b.terkait)}
    `;
  } catch (err) {
    container.innerHTML = `
      <div class="page-error">
        <h3>😕</h3>
        <p>Berita tidak ditemukan atau terjadi kesalahan.</p>
        <small style="display:block;margin-bottom:20px;">${err.message}</small>
        <a href="index.html#berita" class="btn-back">← Kembali ke Beranda</a>
      </div>`;
  }
}

loadDetail();
</script>
</body>
</html>
