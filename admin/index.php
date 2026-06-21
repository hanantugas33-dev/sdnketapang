<?php
$pageTitle  = 'Dashboard';
$pageActive = 'dashboard';
require_once 'auth.php';

$db = getDB();
$totalBerita   = $db->query("SELECT COUNT(*) FROM berita WHERE status='publish'")->fetchColumn();
$totalGuru     = $db->query("SELECT COUNT(*) FROM guru WHERE is_active=1")->fetchColumn();
$totalGaleri   = $db->query("SELECT COUNT(*) FROM galeri WHERE status='aktif'")->fetchColumn();
$totalPesan    = $db->query("SELECT COUNT(*) FROM buku_tamu WHERE status='belum_dibaca'")->fetchColumn();
$recentBerita  = $db->query("SELECT judul, kategori, tanggal FROM berita ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentPesan   = $db->query("SELECT nama, keperluan, created_at FROM buku_tamu ORDER BY created_at DESC LIMIT 5")->fetchAll();
// Total siswa dari data_siswa (tahun terbaru), fallback ke profil_sekolah
try {
    $totalSiswa = $db->query("SELECT COALESCE(SUM(laki_laki+perempuan),0) FROM data_siswa WHERE tahun_ajaran=(SELECT MAX(tahun_ajaran) FROM data_siswa)")->fetchColumn();
    if (!$totalSiswa) throw new Exception('kosong');
} catch (Exception $e) {
    $ps = $db->query("SELECT total_siswa FROM profil_sekolah LIMIT 1")->fetch();
    $totalSiswa = $ps['total_siswa'] ?? 0;
}

require_once 'layout.php';
?>

<div class="stats-grid" style="grid-template-columns:repeat(5,1fr)">
  <div class="stat-box">
    <div class="stat-num"><?= $totalBerita ?></div>
    <div class="stat-lbl">📰 Berita Terpublish</div>
  </div>
  <div class="stat-box gold">
    <div class="stat-num"><?= $totalGuru ?></div>
    <div class="stat-lbl">👨‍🏫 Guru & Staf Aktif</div>
  </div>
  <div class="stat-box" style="border-color:#1565C0">
    <div class="stat-num" style="color:#1565C0"><?= $totalSiswa ?></div>
    <div class="stat-lbl">🎒 Total Siswa</div>
  </div>
  <div class="stat-box green">
    <div class="stat-num"><?= $totalGaleri ?></div>
    <div class="stat-lbl">🖼️ Foto Galeri</div>
  </div>
  <div class="stat-box blue">
    <div class="stat-num" style="color:<?= $totalPesan>0?'var(--red)':'var(--dark)' ?>"><?= $totalPesan ?></div>
    <div class="stat-lbl">✉️ Pesan Belum Dibaca</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

  <!-- Recent Berita -->
  <div class="card">
    <div class="card-head">
      <h2>Berita Terbaru</h2>
      <a href="berita.php" class="btn btn-outline btn-sm">Kelola →</a>
    </div>
    <table class="data-table">
      <thead><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th></tr></thead>
      <tbody>
        <?php foreach ($recentBerita as $b): ?>
        <tr>
          <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($b['judul']) ?></td>
          <td><span class="badge badge-<?= $b['kategori'] ?>"><?= ucfirst($b['kategori']) ?></span></td>
          <td><?= date('d/m/Y', strtotime($b['tanggal'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$recentBerita): ?>
        <tr><td colspan="3" class="empty-state" style="padding:20px;text-align:center;color:#999">Belum ada berita</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Recent Pesan -->
  <div class="card">
    <div class="card-head">
      <h2>Pesan Masuk Terbaru</h2>
      <a href="buku_tamu.php" class="btn btn-outline btn-sm">Lihat Semua →</a>
    </div>
    <table class="data-table">
      <thead><tr><th>Nama</th><th>Keperluan</th><th>Waktu</th></tr></thead>
      <tbody>
        <?php foreach ($recentPesan as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['nama']) ?></td>
          <td style="font-size:12px;color:var(--muted);max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($p['keperluan']) ?></td>
          <td style="font-size:12px;color:var(--muted)"><?= date('d/m H:i', strtotime($p['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$recentPesan): ?>
        <tr><td colspan="3" style="padding:20px;text-align:center;color:#999">Belum ada pesan</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- Quick Actions -->
<div class="card" style="margin-top:20px;">
  <div class="card-head"><h2>Aksi Cepat</h2></div>
  <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
    <a href="berita.php?action=new" class="btn btn-primary">📰 Tulis Berita Baru</a>
    <a href="galeri.php?action=new" class="btn btn-gold">🖼️ Tambah Foto Galeri</a>
    <a href="guru.php?action=new" class="btn btn-success">👨‍🏫 Tambah Guru/Staf</a>
    <a href="siswa.php" class="btn btn-outline" style="border-color:#1565C0;color:#1565C0;">🎒 Kelola Data Siswa</a>
    <a href="profil.php" class="btn btn-outline">🏫 Edit Profil Sekolah</a>
    <a href="buku_tamu.php" class="btn btn-outline">✉️ Lihat Pesan<?= $totalPesan>0?" ($totalPesan)":"" ?></a>
  </div>
</div>

<?php require_once 'layout_end.php'; ?>
