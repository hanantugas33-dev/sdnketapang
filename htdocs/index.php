// ===== CONFIG =====
const API_BASE = 'api/';

// ===== UTILS =====
function loading(html) {
  return `<div class="loading-state"><div class="loading-spinner"></div>${html || 'Memuat data...'}</div>`;
}
function errorState(msg) {
  return `<div class="error-state">⚠️ ${msg || 'Gagal memuat data. Coba lagi.'}</div>`;
}
async function apiFetch(endpoint) {
  const res = await fetch(API_BASE + endpoint);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

// ===== HAMBURGER =====
function toggleMenu() {
  const menu = document.getElementById('navMenu');
  const ham  = document.getElementById('hamburger');
  const isOpen = menu.classList.toggle('open');
  ham.setAttribute('aria-expanded', isOpen);
  document.body.style.overflow = isOpen ? 'hidden' : '';
}
document.addEventListener('click', function(e) {
  const menu = document.getElementById('navMenu');
  const ham  = document.getElementById('hamburger');
  if (menu && ham && menu.classList.contains('open') &&
      !menu.contains(e.target) && !ham.contains(e.target)) {
    menu.classList.remove('open');
    ham.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }
});

// ===== SMOOTH SCROLL =====
function scrollToSection(id) {
  const el = document.querySelector(id);
  if (!el) return;
  const navH = document.getElementById('navbar')?.offsetHeight || 68;
  const top  = el.getBoundingClientRect().top + window.pageYOffset - navH;
  window.scrollTo({ top, behavior: 'smooth' });
}
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const t = document.querySelector(a.getAttribute('href'));
    if (t) {
      e.preventDefault();
      const navH = document.getElementById('navbar')?.offsetHeight || 68;
      const top  = t.getBoundingClientRect().top + window.pageYOffset - navH;
      window.scrollTo({ top, behavior: 'smooth' });
      const menu = document.getElementById('navMenu');
      menu.classList.remove('open');
      document.getElementById('hamburger').setAttribute('aria-expanded','false');
      document.body.style.overflow = '';
    }
  });
});

// ===== ACTIVE NAV =====
const sections = document.querySelectorAll('section[id],div[id]');
const navLinks = document.querySelectorAll('.nav-link');
const obs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      navLinks.forEach(l => {
        l.classList.toggle('active', l.getAttribute('href') === '#' + e.target.id);
      });
    }
  });
}, { threshold: .3 });
sections.forEach(s => obs.observe(s));

// ===== PROFIL PANEL =====
function showPanel(id, btn) {
  document.querySelectorAll('.profil-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.profil-nav-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('panel-' + id).classList.add('active');
  btn.classList.add('active');
}

// ===== MODAL =====
function openModal(title, desc, imgSrc) {
  document.getElementById('modalTitle').textContent = title;
  document.getElementById('modalDesc').textContent = desc;
  const imgEl = document.getElementById('modalImg');
  if (imgSrc && imgSrc.startsWith('http')) {
    imgEl.innerHTML = `<img src="${imgSrc}" alt="${title}">`;
  } else {
    imgEl.textContent = imgSrc || '🏫';
  }
  document.getElementById('modalOverlay').classList.add('open');
}
function closeModal(e) {
  if (!e || e.target === document.getElementById('modalOverlay'))
    document.getElementById('modalOverlay').classList.remove('open');
}

// ===== SCROLL ANIMATE =====
function initScrollAnimate() {
  const animObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.style.opacity = '1';
        e.target.style.transform = 'translateY(0)';
      }
    });
  }, { threshold: .1 });
  document.querySelectorAll('.berita-card,.guru-card,.prestasi-card,.fas-item,.pb-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity .5s ease, transform .5s ease';
    animObs.observe(el);
  });
}

// ===== GOLONGAN HELPER =====
// Deduce approximate PNS golongan from NIP
function getGolongan(nip) {
  if (!nip) return null;
  const thn = parseInt(nip.substring(0, 4));
  const golMap = [
    { thn: 2000, gol: 'IV/a', cls: 'golongan-iv', label: 'Gol. IV/a' },
    { thn: 2005, gol: 'III/d', cls: 'golongan-iii', label: 'Gol. III/d' },
    { thn: 2008, gol: 'III/c', cls: 'golongan-iii', label: 'Gol. III/c' },
    { thn: 2010, gol: 'III/b', cls: 'golongan-iii', label: 'Gol. III/b' },
    { thn: 2013, gol: 'III/a', cls: 'golongan-iii', label: 'Gol. III/a' },
    { thn: 2015, gol: 'II/d', cls: 'golongan-ii', label: 'Gol. II/d' },
  ];
  // Find closest
  let result = { cls: 'golongan-iii', label: 'Gol. III/b' };
  for (const g of golMap) {
    if (thn <= g.thn) { result = g; break; }
  }
  return result;
}

// ===== STATIC FALLBACK DATA STRUKTUR ORGANISASI =====
const STRUKTUR_DEFAULT = [
  {id:1,  nama:'Budi Santoso, S.Pd., M.M.',   jabatan:'Kepala Sekolah',              nip:'197001012000031001', level:'kepala',      urutan:1},
  {id:2,  nama:'Drs. H. Supriyanto',           jabatan:'Komite Sekolah',              nip:null,                 level:'komite',      urutan:2},
  {id:3,  nama:'Siti Rahayu, S.Pd.',           jabatan:'Wakil Kepala Sekolah',        nip:'197502022003012002', level:'wakil',       urutan:3},
  {id:4,  nama:'Dewi Anggraeni, S.E.',         jabatan:'Tata Usaha (TU)',             nip:'198507072010012004', level:'staff',       urutan:4},
  {id:5,  nama:'Ahmad Fauzi, S.Pd.',           jabatan:'Bendahara Sekolah',           nip:'198003032006011003', level:'staff',       urutan:5},
  {id:6,  nama:'Rizki Pratama, A.Md.',         jabatan:'Operator Sekolah',            nip:null,                 level:'staff',       urutan:6},
  {id:7,  nama:'Hendra Gunawan, S.Pd.',        jabatan:'Koord. Kurikulum',            nip:'198202152009011005', level:'koordinator', urutan:7},
  {id:8,  nama:'Nurul Hidayah, S.Pd.',         jabatan:'Koord. Kesiswaan',            nip:'198405202010012007', level:'koordinator', urutan:8},
  {id:9,  nama:'Bambang Sutrisno, S.Pd.',      jabatan:'Koord. Sarana & Prasarana',   nip:'198108032008011004', level:'koordinator', urutan:9},
  {id:10, nama:'Sri Mulyani, S.Pd.',           jabatan:'Guru Kelas 1',                nip:'198601012011012010', level:'guru_kelas',  urutan:10},
  {id:11, nama:'Ratna Dewi, S.Pd.',            jabatan:'Guru Kelas 2',                nip:'198703052012012011', level:'guru_kelas',  urutan:11},
  {id:12, nama:'Agus Salim, S.Pd.',            jabatan:'Guru Kelas 3',                nip:'198504172011011012', level:'guru_kelas',  urutan:12},
  {id:13, nama:'Rina Wulandari, S.Pd.',        jabatan:'Guru Kelas 4',                nip:'198802242013012013', level:'guru_kelas',  urutan:13},
  {id:14, nama:'Doni Firmansyah, S.Pd.',       jabatan:'Guru Kelas 5',                nip:'198906112014011014', level:'guru_kelas',  urutan:14},
  {id:15, nama:'Fitri Handayani, S.Pd.',       jabatan:'Guru Kelas 6',                nip:'199001072015012015', level:'guru_kelas',  urutan:15},
  {id:16, nama:'Ustadz Mahmud, S.Pd.I.',       jabatan:'Pendidikan Agama',            nip:'198307052010011016', level:'guru_mapel',  urutan:16},
  {id:17, nama:'Eko Prasetyo, S.Pd.',          jabatan:'PJOK',                        nip:'198410122011011017', level:'guru_mapel',  urutan:17},
  {id:18, nama:'Linda Susanti, S.Pd.',         jabatan:'Bahasa Inggris',              nip:null,                 level:'guru_mapel',  urutan:18},
  {id:19, nama:'Yuni Astuti, S.Pd.',           jabatan:'Seni Budaya',                 nip:null,                 level:'guru_mapel',  urutan:19},
  {id:20, nama:'Sari Indah, S.Pd.',            jabatan:'Perpustakaan',                nip:null,                 level:'penunjang',   urutan:20},
  {id:21, nama:'Dr. Hadi Kusuma',              jabatan:'UKS',                         nip:null,                 level:'penunjang',   urutan:21},
  {id:22, nama:'Samsudin & Joko S.',           jabatan:'Kebersihan & Keamanan',       nip:null,                 level:'penunjang',   urutan:22},
  {id:24, nama:'± 180 Siswa SDN Ketapang',     jabatan:'SISWA',                       nip:null,                 level:'siswa',       urutan:24},
];

// ===== LOAD PROFIL (visi, misi, tujuan, sejarah, struktur) =====
async function loadProfil() {
  let profil, struktur, resp;
  try {
    [profil, struktur, resp] = await Promise.all([
      apiFetch('profil.php?action=visi_misi'),
      apiFetch('profil.php?action=struktur'),
      apiFetch('profil.php'),
    ]);
  } catch (e) {
    console.warn('Profil load failed, using static fallback:', e.message);
    struktur = STRUKTUR_DEFAULT;
    profil = {};
    resp = {};
  }

  if (!struktur || !struktur.length) struktur = STRUKTUR_DEFAULT;

  // Sejarah
  const panelSejarah = document.getElementById('panel-sejarah');
  if (panelSejarah) {
    const txt = profil.sejarah || 'SDN Ketapang berdiri pada tahun 1975 atas prakarsa pemerintah daerah setempat. Sejak awal berdirinya, sekolah ini berkomitmen memberikan pendidikan berkualitas bagi anak-anak di wilayah Ketapang.';
    panelSejarah.innerHTML = `<h3>Sejarah Singkat SDN Ketapang</h3>` +
      txt.split('\n').filter(l => l.trim()).map(l => `<p>${l}</p>`).join('');
  }

  // Visi
  const panelVisi = document.getElementById('panel-visi');
  if (panelVisi) {
    const visi = profil.visi || 'Terwujudnya peserta didik yang beriman, berilmu, berprestasi, berbudaya, dan berwawasan lingkungan.';
    panelVisi.innerHTML = `<h3>Visi Sekolah</h3><p>${visi}</p>`;
  }

  // Misi — bernomor
  const panelMisi = document.getElementById('panel-misi');
  if (panelMisi) {
    const misiTxt = profil.misi || 'Menyelenggarakan pembelajaran aktif, kreatif, efektif, dan menyenangkan.\nMengembangkan potensi siswa secara optimal dalam bidang akademik dan non-akademik.\nMenciptakan lingkungan sekolah yang bersih, nyaman, dan kondusif.\nMenjalin kerjasama yang harmonis antara sekolah, orang tua, dan masyarakat.\nMenanamkan nilai-nilai karakter bangsa dalam setiap kegiatan pembelajaran.';
    const items = misiTxt.split('\n').filter(l => l.trim());
    panelMisi.innerHTML = `<h3>Misi Sekolah</h3><ol class="vm-list-numbered">` +
      items.map(i => `<li>${i}</li>`).join('') + `</ol>`;
  }

  // Tujuan — bernomor
  const panelTujuan = document.getElementById('panel-tujuan');
  if (panelTujuan) {
    const tujuanTxt = profil.tujuan || 'Meningkatkan mutu pendidikan secara berkelanjutan.\nMenjadikan siswa yang berkarakter Profil Pelajar Pancasila.\nMeraih prestasi di tingkat kecamatan, kabupaten, dan provinsi.\nMenciptakan lulusan yang siap melanjutkan ke jenjang pendidikan lebih tinggi.';
    const items = tujuanTxt.split('\n').filter(l => l.trim());
    panelTujuan.innerHTML = `<h3>Tujuan Sekolah</h3><ol class="vm-list-numbered">` +
      items.map(i => `<li>${i}</li>`).join('') + `</ol>`;
  }

  // Struktur organisasi — dengan golongan
  const panelStruktur = document.getElementById('panel-struktur');
  if (panelStruktur && struktur.length) {

    function initials(nama) {
      const parts = nama.replace(/[,\.]/g,'').split(' ').filter(w => w.length > 1 && !/^(S|M|A|dr|Drs|Hj?|Ir|SE|ST|SH|SKM|SKep|SPd|SPdI|MM|MSi|MPd)\\.?$/i.test(w));
      return parts.slice(0,2).map(w => w[0].toUpperCase()).join('') || nama.slice(0,2).toUpperCase();
    }

    function card(s, extraClass='') {
      const av = s.level === 'siswa' ? '🎒' : initials(s.nama);
      const gol = s.nip ? getGolongan(s.nip) : null;
      const golBadge = gol ? `<span class="golongan-badge ${gol.cls}">${gol.label}</span>` : (s.nip ? '' : `<span class="golongan-badge golongan-ptk">PTK/Honor</span>`);
      return `
        <div class="oc-card ${s.level} ${extraClass}">
          <div class="oc-av">${av}</div>
          <div class="oc-jab">${s.jabatan}</div>
          <div class="oc-nm">${s.nama}</div>
          ${s.nip ? `<div class="oc-nip">NIP: ${s.nip}</div>` : ''}
          ${s.level !== 'siswa' && s.level !== 'komite' ? golBadge : ''}
        </div>`;
    }

    function row(nodes, extraRowClass='') {
      if (!nodes.length) return '';
      const cls = nodes.length === 1 ? 'one' : nodes.length === 2 ? 'two' : nodes.length === 3 ? 'three' : '';
      return `
        <div class="oc-line-v"></div>
        <div class="oc-row ${cls} ${extraRowClass}">
          ${nodes.map(s => `<div class="oc-node">${card(s)}</div>`).join('')}
        </div>`;
    }

    function groupRow(nodes, labelText, extraRowClass='') {
      if (!nodes.length) return '';
      return `
        <div class="oc-line-v"></div>
        <div class="oc-group">
          <span class="oc-group-label">${labelText}</span>
        </div>
        <div class="oc-grid ${extraRowClass}">
          ${nodes.map(s => `<div class="oc-node">${card(s)}</div>`).join('')}
        </div>`;
    }

    const get = (level) => struktur.filter(s => s.level === level);

    // Render dulu tanpa data siswa (sementara)
    function renderStruktur(totalSiswa) {
      const labelSiswa = totalSiswa > 0 ? `${totalSiswa} Siswa` : 'Data Siswa';
      const siswaNodes = get('siswa').length
        ? get('siswa').map(s => ({ ...s, nama: labelSiswa, jabatan: 'SISWA' }))
        : [{ id: 99, nama: labelSiswa, jabatan: 'SISWA', nip: null, level: 'siswa', urutan: 99 }];

      panelStruktur.innerHTML = `
        <h3>Struktur Organisasi SDN Ketapang</h3>
        <p style="font-size:13px;color:var(--muted);margin-bottom:20px;">Golongan PNS tercantum pada setiap jabatan berdasarkan data kepegawaian.</p>
        <div class="org-box">
          <div class="org-chart">
            <div class="oc-node" style="padding-top:0;">
              ${card(get('kepala')[0])}
            </div>
            ${row([...get('komite'), ...get('wakil')])}
            ${row(get('staff'))}
            ${row(get('koordinator'))}
            ${groupRow(get('guru_kelas'), 'Guru Kelas')}
            ${groupRow(get('guru_mapel'), 'Guru Mata Pelajaran')}
            ${groupRow(get('guru_pjok'), 'Guru PJOK')}
            ${groupRow(get('pelatih'), 'Pelatih Ekskul')}
            ${groupRow(get('penunjang'), 'Tenaga Penunjang')}
            ${row(siswaNodes)}
          </div>
        </div>`;
    }

    // Tampilkan dulu dengan fallback, lalu update setelah dapat data siswa
    renderStruktur(0);

    // Ambil total siswa real dari API
    apiFetch('siswa.php?action=total')
      .then(d => { renderStruktur(d.total || 0); })
      .catch(() => {
        // Fallback: coba ambil dari stats profil
        apiFetch('profil.php?action=stats')
          .then(d => renderStruktur(d.total_siswa || 0))
          .catch(() => {});
      });

    const kepalaNode = panelStruktur.querySelector('.oc-node');
    if (kepalaNode) kepalaNode.style.setProperty('padding-top','0');
  }

  // Akreditasi
  try {
    const akr = resp.akreditasi;
    const panelAkr = document.getElementById('panel-akreditasi');
    if (panelAkr) {
      if (akr) {
        const predikat = akr.nilai >= 91 ? 'UNGGUL' : akr.nilai >= 81 ? 'BAIK SEKALI' : 'BAIK';
        const berlaku = akr.berlaku_dari && akr.berlaku_sampai
          ? `Berlaku: ${akr.berlaku_dari} – ${akr.berlaku_sampai}`
          : '';
        panelAkr.innerHTML = `
          <h3>Akreditasi Sekolah</h3>
          <div style="text-align:center;padding:32px;">
            <div style="font-size:80px;font-family:'Lora',serif;color:var(--red);font-weight:700;">${akr.nilai_huruf || 'A'}</div>
            <div style="font-size:18px;font-weight:600;margin:8px 0;">Predikat ${predikat}</div>
            <div style="color:var(--muted);font-size:14px;">Nilai: ${akr.nilai || '—'} | BAN-S/M</div>
            ${berlaku ? `<div style="color:var(--muted);font-size:13px;margin-top:8px;">${berlaku}</div>` : ''}
          </div>`;
      } else {
        const p = resp.profil;
        panelAkr.innerHTML = `
          <h3>Akreditasi Sekolah</h3>
          <div style="text-align:center;padding:32px;">
            <div style="font-size:80px;font-family:'Lora',serif;color:var(--red);font-weight:700;">${p?.akreditasi || 'A'}</div>
            <div style="font-size:18px;font-weight:600;margin:8px 0;">BAN-S/M</div>
          </div>`;
      }
    }
  } catch (e) {
    console.warn('Akreditasi load failed:', e.message);
  }
}

// ===== LOAD HERO STATS =====
async function loadHeroStats() {
  try {
    const data = await apiFetch('profil.php?action=stats');
    document.getElementById('statTahun').textContent = data.tahun_berdiri || '—';
    document.getElementById('statSiswa').textContent = data.total_siswa || '—';
    document.getElementById('statGuru').textContent = data.total_guru || '—';
    document.getElementById('statAkreditasi').textContent = data.akreditasi || 'A';
  } catch (e) {
    console.warn('Stats load failed:', e.message);
    // Tampilkan data statis jika API gagal
    document.getElementById('statTahun').textContent = '1975';
    document.getElementById('statSiswa').textContent = '350';
    document.getElementById('statGuru').textContent = '22';
    document.getElementById('statAkreditasi').textContent = 'A';
  }
}

// ===== LOAD & RENDER BERITA =====
let beritaData = [];
async function loadBerita() {
  const grid = document.getElementById('beritaGrid');
  grid.innerHTML = loading();
  try {
    beritaData = await apiFetch('berita.php');
    renderBerita('semua');
  } catch (e) {
    grid.innerHTML = errorState('Gagal memuat berita.');
  }
}

function renderBerita(cat) {
  const grid = document.getElementById('beritaGrid');
  const filtered = cat === 'semua' ? beritaData : beritaData.filter(b => b.kategori === cat);
  if (!filtered.length) {
    grid.innerHTML = `<div class="loading-state" style="grid-column:1/-1">Tidak ada konten untuk kategori ini.</div>`;
    return;
  }
  grid.innerHTML = filtered.map(b => {
    const catClass = { berita: 'cat-berita', pengumuman: 'cat-pengumuman', kegiatan: 'cat-kegiatan' }[b.kategori] || 'cat-berita';
    const imgHtml = b.gambar
      ? `<img src="${b.gambar}" alt="${b.judul}">`
      : `<span style="font-size:36px">${b.icon || '📰'}</span>`;
    return `
      <div class="berita-card" data-cat="${b.kategori}">
        <div class="berita-img">${imgHtml}</div>
        <div class="berita-body">
          <span class="berita-cat ${catClass}">${b.kategori.charAt(0).toUpperCase() + b.kategori.slice(1)}</span>
          <h4>${b.judul}</h4>
          <p>${b.ringkasan}</p>
        </div>
        <div class="berita-footer">
          <span class="berita-date">📅 ${b.tanggal_format}</span>
          <a href="berita-detail.php?id=${b.id}" class="berita-link">Selengkapnya →</a>
        </div>
      </div>`;
  }).join('');
  initScrollAnimate();
}

function filterBerita(cat, btn) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderBerita(cat);
}

// ===== LOAD & RENDER GURU =====
let guruData = [];
async function loadGuru() {
  const grid = document.getElementById('guruGrid');
  grid.innerHTML = loading();
  try {
    guruData = await apiFetch('guru.php');
    renderGuru('semua');
  } catch (e) {
    grid.innerHTML = errorState('Gagal memuat data guru.');
  }
}

function renderGuru(role) {
  const grid = document.getElementById('guruGrid');
  const filtered = role === 'semua' ? guruData : guruData.filter(g => g.jabatan_singkat === role);
  if (!filtered.length) {
    grid.innerHTML = `<div class="loading-state" style="grid-column:1/-1">Tidak ada guru untuk kategori ini.</div>`;
    return;
  }
  grid.innerHTML = filtered.map(g => {
    const fotoHtml = g.foto
      ? `<img src="${g.foto}" alt="${g.nama}">`
      : `<span>👤</span>`;
    const gol = g.nip ? getGolongan(g.nip) : null;
    const golBadge = gol
      ? `<span class="golongan-badge ${gol.cls}" style="margin-top:6px;">${gol.label}</span>`
      : `<span class="golongan-badge golongan-ptk" style="margin-top:6px;">PTK/Honor</span>`;
    return `
      <div class="guru-card" data-role="${g.jabatan_singkat}">
        <div class="guru-foto">
          ${fotoHtml}
          <div class="guru-badge">${g.jabatan}</div>
        </div>
        <div class="guru-info">
          <h4>${g.nama}</h4>
          <div class="nip">NIP: ${g.nip || '-'}</div>
          <span class="guru-mapel">${g.mapel || g.bidang}</span>
          ${golBadge}
        </div>
      </div>`;
  }).join('');
  initScrollAnimate();
}

function filterGuru(role, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderGuru(role);
}

// ===== LOAD & RENDER GALERI =====
let galeriData = [];
async function loadGaleri() {
  const grid = document.getElementById('galeriGrid');
  grid.innerHTML = loading();
  try {
    galeriData = await apiFetch('galeri.php');
    renderGaleri('semua');
  } catch (e) {
    grid.innerHTML = errorState('Gagal memuat galeri.');
  }
}

function renderGaleri(cat) {
  const grid = document.getElementById('galeriGrid');
  const filtered = cat === 'semua' ? galeriData : galeriData.filter(g => g.kategori === cat);
  if (!filtered.length) {
    grid.innerHTML = `<div class="loading-state" style="grid-column:1/-1;color:rgba(255,255,255,.5)">Tidak ada foto untuk kategori ini.</div>`;
    return;
  }
  grid.innerHTML = filtered.map((g, i) => {
    const isLarge = g.is_large || i === 0;
    const imgHtml = g.gambar
      ? `<img src="${g.gambar}" alt="${g.judul}">`
      : `<span>${g.icon || '🏫'}</span><p>${g.judul}</p>`;
    return `
      <div class="galeri-item ${isLarge ? 'large' : ''}" data-gcat="${g.kategori}"
           onclick="openModal('${g.judul.replace(/'/g,"\\'")}','${(g.deskripsi||'').replace(/'/g,"\\'")}','${g.gambar || g.icon || '🏫'}')">
        ${imgHtml}
        <div class="overlay">🔍 Lihat</div>
      </div>`;
  }).join('');
}

function filterGaleri(cat, btn) {
  document.querySelectorAll('.galeri-tab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderGaleri(cat);
}

// ===== LOAD FASILITAS =====
async function loadFasilitas() {
  try {
    const data = await apiFetch('fasilitas.php');
    const list = document.getElementById('fasilitasList');
    if (data.fasilitas && data.fasilitas.length) {
      list.innerHTML = data.fasilitas.map(f => {
        const fotoSrc = f.foto
          ? `assets/img/fasilitas/${f.foto}`
          : null;
        return `
          <div class="fas-item fas-item-foto">
            <div class="fas-foto">
              ${fotoSrc
                ? `<img src="${fotoSrc}" alt="${f.nama}" loading="lazy"/>`
                : `<div class="fas-no-foto">🏫</div>`}
            </div>
            <div class="fas-txt">
              <strong>${f.nama}</strong>
              <span>${f.deskripsi || ''}</span>
            </div>
          </div>`;
      }).join('');
    }
    const chips = document.getElementById('ekskulChips');
    if (data.ekskul && data.ekskul.length) {
      chips.innerHTML = data.ekskul.map(e => {
        const fotoEl = e.foto
          ? `<img src="assets/img/ekskul/${e.foto}" class="e-foto" alt="${e.nama}"/>`
          : `<div class="e-no-foto">📚</div>`;
        return `
          <div class="ekskul-chip">
            ${fotoEl}
            <div class="e-info">
              <span class="e-nama">${e.nama}</span>
              <span class="e-day">${e.hari}</span>
            </div>
          </div>`;
      }).join('');
    }
    initScrollAnimate();
  } catch (e) {
    console.warn('Fasilitas load failed:', e.message);
  }
}

// ===== LOAD PRESTASI =====
async function loadPrestasi() {
  const grid = document.getElementById('prestasiGrid');
  grid.innerHTML = loading();
  try {
    const data = await apiFetch('prestasi.php');
    const medals = ['🥇','🥈','🥉','🏆','🎖️','⭐'];
    grid.innerHTML = data.map((p, i) => {
      const hasGambar = p.gambar && p.gambar.trim() !== '';
      return `
        <div class="prestasi-card">
          ${hasGambar
            ? `<div class="prest-img"><img src="assets/img/prestasi/${p.gambar}" alt="${p.nama_prestasi}" loading="lazy"/></div>`
            : `<div class="prest-medal">${p.medali || medals[i % medals.length]}</div>`}
          <div class="prest-level">Tingkat ${p.tingkat}</div>
          <h4>${p.nama_prestasi}</h4>
          <p>${p.deskripsi}</p>
          <span class="prest-year">${p.tahun}</span>
        </div>`;
    }).join('');
    initScrollAnimate();
  } catch (e) {
    grid.innerHTML = errorState('Gagal memuat data prestasi.');
  }
}

// ===== FORM BUKU TAMU =====
async function submitForm() {
  const nama = document.getElementById('f-nama').value.trim();
  const hp = document.getElementById('f-hp').value.trim();
  const email = document.getElementById('f-email').value.trim();
  const keperluan = document.getElementById('f-keperluan').value;
  const pesan = document.getElementById('f-pesan').value.trim();

  const errEl = document.getElementById('errorMsg');
  const sucEl = document.getElementById('successMsg');
  errEl.style.display = 'none';
  sucEl.style.display = 'none';

  if (!nama || !keperluan || !pesan) {
    errEl.style.display = 'block';
    errEl.textContent = '⚠️ Mohon lengkapi nama, keperluan, dan pesan terlebih dahulu.';
    return;
  }

  const btn = document.querySelector('.form-submit');
  btn.disabled = true;
  btn.textContent = 'Mengirim...';

  try {
    const res = await fetch(API_BASE + 'buku_tamu.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nama, hp, email, keperluan, pesan })
    });
    const result = await res.json();
    if (result.success) {
      sucEl.style.display = 'block';
      document.getElementById('f-nama').value = '';
      document.getElementById('f-hp').value = '';
      document.getElementById('f-email').value = '';
      document.getElementById('f-keperluan').value = '';
      document.getElementById('f-pesan').value = '';
      setTimeout(() => { sucEl.style.display = 'none'; }, 5000);
    } else {
      errEl.style.display = 'block';
      errEl.textContent = '⚠️ ' + (result.message || 'Gagal mengirim pesan.');
    }
  } catch (e) {
    errEl.style.display = 'block';
    errEl.textContent = '⚠️ Koneksi gagal. Periksa server backend.';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Kirim Pesan ✉️';
  }
}

// ===== INIT =====
document.addEventListener('DOMContentLoaded', () => {
  loadHeroStats();
  loadProfil();
  loadBerita();
  loadGuru();
  loadGaleri();
  loadFasilitas();
  loadPrestasi();
  initScrollAnimate();
});
