<?php
$pageTitle  = 'Berita & Pengumuman';
$pageActive = 'berita';
require_once 'auth.php';

$db  = getDB();
$msg = '';
$err = '';

// --- DELETE ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $db->prepare("DELETE FROM berita WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: berita.php?msg=deleted');
    exit;
}

// --- TOGGLE STATUS ---
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $db->prepare("UPDATE berita SET status = IF(status='publish','draft','publish') WHERE id=?")->execute([(int)$_GET['toggle']]);
    header('Location: berita.php');
    exit;
}

// --- SAVE (INSERT/UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)($_POST['id'] ?? 0);
    $judul    = trim($_POST['judul'] ?? '');
    $kategori = $_POST['kategori'] ?? 'berita';
    $ringkasan= trim($_POST['ringkasan'] ?? '');
    $konten   = trim($_POST['konten'] ?? '');
    $tanggal  = $_POST['tanggal'] ?? date('Y-m-d');
    $status   = $_POST['status'] ?? 'publish';
    $icon     = $_POST['icon'] ?? '📰';
    $slug     = strtolower(preg_replace('/[^a-z0-9]+/','-', $judul)) . '-' . time();

    if (!$judul || !$ringkasan) { $err = 'Judul dan ringkasan wajib diisi!'; }
    else {
        if ($id) {
            $db->prepare("UPDATE berita SET judul=?,kategori=?,ringkasan=?,konten=?,tanggal=?,status=?,icon=? WHERE id=?")
               ->execute([$judul,$kategori,$ringkasan,$konten,$tanggal,$status,$icon,$id]);
        } else {
            $db->prepare("INSERT INTO berita (judul,slug,kategori,ringkasan,konten,tanggal,status,icon) VALUES (?,?,?,?,?,?,?,?)")
               ->execute([$judul,$slug,$kategori,$ringkasan,$konten,$tanggal,$status,$icon]);
        }
        header('Location: berita.php?msg=saved');
        exit;
    }
}

// Edit mode
$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit = $db->prepare("SELECT * FROM berita WHERE id=?");
    $edit->execute([(int)$_GET['edit']]);
    $edit = $edit->fetch();
}

$showForm = isset($_GET['action']) || $edit;

// Ambil list
$filter = $_GET['filter'] ?? 'semua';
if ($filter !== 'semua') {
    $stmt = $db->prepare("SELECT * FROM berita WHERE kategori=? ORDER BY tanggal DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $db->query("SELECT * FROM berita ORDER BY tanggal DESC");
}
$list = $stmt->fetchAll();

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'saved') $msg = '✅ Data berhasil disimpan!';
    if ($_GET['msg'] === 'deleted') $msg = '🗑️ Data berhasil dihapus.';
}

require_once 'layout.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error">⚠️ <?= $err ?></div><?php endif; ?>

<!-- Toggle Form/List -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
  <div class="page-tabs">
    <button class="page-tab <?= !$showForm?'active':'' ?>" onclick="location='berita.php'">📋 Daftar Berita</button>
    <button class="page-tab <?= $showForm?'active':'' ?>" onclick="location='berita.php?action=new'"><?= $edit?'✏️ Edit':'➕ Tulis Baru' ?></button>
  </div>
  <?php if (!$showForm): ?>
  <div style="display:flex;gap:8px;align-items:center;">
    <select onchange="location='berita.php?filter='+this.value" style="padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;">
      <option value="semua" <?= $filter==='semua'?'selected':'' ?>>Semua</option>
      <option value="berita" <?= $filter==='berita'?'selected':'' ?>>Berita</option>
      <option value="pengumuman" <?= $filter==='pengumuman'?'selected':'' ?>>Pengumuman</option>
      <option value="kegiatan" <?= $filter==='kegiatan'?'selected':'' ?>>Kegiatan</option>
    </select>
  </div>
  <?php endif; ?>
</div>

<?php if ($showForm): ?>
<!-- FORM TAMBAH/EDIT -->
<div class="card">
  <div class="card-head">
    <h2><?= $edit ? '✏️ Edit Berita' : '➕ Tambah Berita / Pengumuman' ?></h2>
    <a href="berita.php" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>"/>
      <div class="form-row">
        <div class="form-group full">
          <label>Judul *</label>
          <input type="text" name="judul" value="<?= htmlspecialchars($edit['judul']??'') ?>" placeholder="Masukkan judul berita/pengumuman..." required/>
        </div>
        <div class="form-group">
          <label>Kategori</label>
          <select name="kategori">
            <option value="berita" <?= ($edit['kategori']??'')==='berita'?'selected':'' ?>>📰 Berita</option>
            <option value="pengumuman" <?= ($edit['kategori']??'')==='pengumuman'?'selected':'' ?>>📢 Pengumuman</option>
            <option value="kegiatan" <?= ($edit['kategori']??'')==='kegiatan'?'selected':'' ?>>🎯 Kegiatan</option>
          </select>
        </div>
        <div class="form-group">
          <label>Tanggal</label>
          <input type="date" name="tanggal" value="<?= $edit['tanggal'] ?? date('Y-m-d') ?>"/>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="publish" <?= ($edit['status']??'')==='publish'?'selected':'' ?>>✅ Publish</option>
            <option value="draft" <?= ($edit['status']??'')==='draft'?'selected':'' ?>>📝 Draft</option>
          </select>
        </div>
        <div class="form-group">
          <label>Icon Emoji</label>
          <input type="text" name="icon" value="<?= htmlspecialchars($edit['icon']??'📰') ?>" placeholder="📰" maxlength="5"/>
          <div class="form-hint">Emoji untuk thumbnail (contoh: 🏆, 📢, 🎨)</div>
        </div>
        <div class="form-group full">
          <label>Ringkasan * <span style="color:#999;font-weight:400">(ditampilkan di halaman utama)</span></label>
          <textarea name="ringkasan" rows="3" placeholder="Tulis ringkasan singkat berita (2-3 kalimat)..." required><?= htmlspecialchars($edit['ringkasan']??'') ?></textarea>
        </div>
        <div class="form-group full">
          <label>Konten Lengkap <span style="color:#999;font-weight:400">(untuk halaman detail berita)</span></label>
          <textarea name="konten" rows="8" placeholder="Tulis konten lengkap berita di sini..."><?= htmlspecialchars($edit['konten']??'') ?></textarea>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
        <a href="berita.php" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">💾 Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php else: ?>
<!-- DAFTAR BERITA -->
<div class="card">
  <div class="card-head">
    <h2>Daftar Berita <span style="color:#999;font-size:13px;font-weight:400">(<?= count($list) ?> item)</span></h2>
  </div>
  <?php if ($list): ?>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width:40%">Judul</th>
        <th>Kategori</th>
        <th>Tanggal</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($list as $b): ?>
      <tr>
        <td>
          <div style="font-weight:500"><?= htmlspecialchars($b['judul']) ?></div>
          <div style="font-size:12px;color:#999;margin-top:2px"><?= mb_strimwidth(htmlspecialchars($b['ringkasan']),0,80,'...') ?></div>
        </td>
        <td><span class="badge badge-<?= $b['kategori'] ?>"><?= ucfirst($b['kategori']) ?></span></td>
        <td style="font-size:12px;color:#666"><?= date('d M Y', strtotime($b['tanggal'])) ?></td>
        <td>
          <a href="berita.php?toggle=<?= $b['id'] ?>" title="Klik untuk toggle">
            <span class="badge badge-<?= $b['status'] ?>"><?= $b['status']==='publish'?'✅ Publish':'📝 Draft' ?></span>
          </a>
        </td>
        <td>
          <div class="td-actions">
            <a href="berita.php?edit=<?= $b['id'] ?>" class="btn btn-outline btn-sm">✏️</a>
            <a href="berita.php?delete=<?= $b['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus berita ini?')">🗑️</a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <div class="empty-state"><div>📰</div><p>Belum ada berita. <a href="berita.php?action=new" style="color:var(--red)">Tulis berita pertama →</a></p></div>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once 'layout_end.php'; ?>
