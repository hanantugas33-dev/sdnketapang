<?php
$pageTitle  = 'Berita & Pengumuman';
$pageActive = 'berita';
require_once 'auth.php';

$db  = getDB();
$msg = '';
$err = '';

// ── Helper upload gambar ──────────────────────────────────────
function uploadGambarBerita($fileKey) {
    if (empty($_FILES[$fileKey]['name'])) return null;
    $f   = $_FILES[$fileKey];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $ok  = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $ok))          throw new Exception("Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.");
    if ($f['size'] > 5 * 1024 * 1024) throw new Exception("Ukuran file maksimal 5MB.");
    $dir = dirname(__DIR__) . "/assets/img/berita/";
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = uniqid() . '_' . time() . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], $dir . $name))
        throw new Exception("Gagal menyimpan file.");
    return 'assets/img/berita/' . $name;
}
function hapusGambarBerita($path) {
    if (!$path) return;
    $full = dirname(__DIR__) . '/' . ltrim($path, '/');
    if (file_exists($full)) unlink($full);
}

// --- DELETE ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $row = $db->prepare("SELECT gambar FROM berita WHERE id=?");
    $row->execute([(int)$_GET['delete']]);
    $r = $row->fetch();
    hapusGambarBerita($r['gambar'] ?? null);
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
    try {
        $id        = (int)($_POST['id'] ?? 0);
        $judul     = trim($_POST['judul'] ?? '');
        $kategori  = $_POST['kategori'] ?? 'berita';
        $ringkasan = trim($_POST['ringkasan'] ?? '');
        $konten    = trim($_POST['konten'] ?? '');
        $tanggal   = $_POST['tanggal'] ?? date('Y-m-d');
        $status    = $_POST['status'] ?? 'publish';
        $slug      = strtolower(preg_replace('/[^a-z0-9]+/','-', $judul)) . '-' . time();

        if (!$judul || !$ringkasan) throw new Exception('Judul dan ringkasan wajib diisi!');

        // Gambar lama
        $gambarLama = '';
        if ($id) {
            $s = $db->prepare("SELECT gambar FROM berita WHERE id=?");
            $s->execute([$id]);
            $gambarLama = $s->fetchColumn() ?: '';
        }

        // Upload gambar baru jika ada
        $gambarBaru = uploadGambarBerita('gambar');
        $gambar     = $gambarBaru ?? $gambarLama;

        // Hapus gambar lama jika diganti
        if ($gambarBaru && $gambarLama) hapusGambarBerita($gambarLama);

        // Hapus gambar jika dicentang
        if (isset($_POST['hapus_gambar']) && $_POST['hapus_gambar'] === '1') {
            hapusGambarBerita($gambar);
            $gambar = '';
        }

        if ($id) {
            $db->prepare("UPDATE berita SET judul=?,kategori=?,ringkasan=?,konten=?,tanggal=?,status=?,gambar=? WHERE id=?")
               ->execute([$judul,$kategori,$ringkasan,$konten,$tanggal,$status,$gambar,$id]);
        } else {
            $db->prepare("INSERT INTO berita (judul,slug,kategori,ringkasan,konten,tanggal,status,gambar) VALUES (?,?,?,?,?,?,?,?)")
               ->execute([$judul,$slug,$kategori,$ringkasan,$konten,$tanggal,$status,$gambar]);
        }
        header('Location: berita.php?msg=saved');
        exit;
    } catch (Exception $e) {
        $err = $e->getMessage();
    }
}

// Edit mode
$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM berita WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
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
    if ($_GET['msg'] === 'saved')   $msg = '✅ Data berhasil disimpan!';
    if ($_GET['msg'] === 'deleted') $msg = '🗑️ Data berhasil dihapus.';
}

require_once 'layout.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<!-- Toggle Form/List -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
  <div class="page-tabs">
    <button class="page-tab <?= !$showForm?'active':'' ?>" onclick="location='berita.php'">📋 Daftar Berita</button>
    <button class="page-tab <?= $showForm?'active':'' ?>" onclick="location='berita.php?action=new'"><?= $edit?'✏️ Edit':'➕ Tulis Baru' ?></button>
  </div>
  <?php if (!$showForm): ?>
  <div style="display:flex;gap:8px;align-items:center;">
    <select onchange="location='berita.php?filter='+this.value" style="padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;">
      <option value="semua"       <?= $filter==='semua'?'selected':'' ?>>Semua</option>
      <option value="berita"      <?= $filter==='berita'?'selected':'' ?>>Berita</option>
      <option value="pengumuman"  <?= $filter==='pengumuman'?'selected':'' ?>>Pengumuman</option>
      <option value="kegiatan"    <?= $filter==='kegiatan'?'selected':'' ?>>Kegiatan</option>
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
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>"/>
      <div class="form-row">
        <div class="form-group full">
          <label>Judul *</label>
          <input type="text" name="judul" value="<?= htmlspecialchars($edit['judul']??'') ?>" placeholder="Masukkan judul berita/pengumuman..." required/>
        </div>
        <div class="form-group">
          <label>Kategori</label>
          <select name="kategori">
            <option value="berita"      <?= ($edit['kategori']??'')==='berita'?'selected':'' ?>>📰 Berita</option>
            <option value="pengumuman"  <?= ($edit['kategori']??'')==='pengumuman'?'selected':'' ?>>📢 Pengumuman</option>
            <option value="kegiatan"    <?= ($edit['kategori']??'')==='kegiatan'?'selected':'' ?>>🎯 Kegiatan</option>
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
            <option value="draft"   <?= ($edit['status']??'')==='draft'?'selected':'' ?>>📝 Draft</option>
          </select>
        </div>

        <!-- UPLOAD GAMBAR -->
        <div class="form-group full">
          <label>🖼️ Gambar Berita</label>
          <?php if (!empty($edit['gambar'])): ?>
          <div style="margin-bottom:12px;">
            <img src="../<?= htmlspecialchars($edit['gambar']) ?>"
                 style="width:100%;max-height:200px;object-fit:cover;border-radius:10px;border:1px solid #eee;display:block;"/>
            <div style="margin-top:8px;display:flex;align-items:center;gap:8px;">
              <input type="checkbox" name="hapus_gambar" value="1" id="hapusGambar"/>
              <label for="hapusGambar" style="font-size:12px;color:#c62828;cursor:pointer;font-weight:500;">🗑️ Hapus gambar ini</label>
            </div>
            <div style="font-size:12px;color:#999;margin-top:4px;">Upload baru untuk mengganti gambar.</div>
          </div>
          <?php endif; ?>
          <div class="upload-zone" id="uploadZoneBerita"
               onclick="document.getElementById('gambarBeritaInput').click()"
               ondragover="event.preventDefault();this.classList.add('drag')"
               ondragleave="this.classList.remove('drag')"
               ondrop="handleDropBerita(event)">
            <div id="prevBerita" style="text-align:center;">
              <div style="font-size:36px;margin-bottom:8px;">🖼️</div>
              <div style="font-size:13px;font-weight:600;color:#555;">Klik atau drag gambar ke sini</div>
              <div style="font-size:11px;color:#999;margin-top:4px;">JPG, PNG, WEBP · Maks 5MB · Disarankan 800×450px</div>
            </div>
          </div>
          <input type="file" id="gambarBeritaInput" name="gambar"
                 accept="image/jpeg,image/png,image/webp,image/gif"
                 style="display:none"
                 onchange="previewBerita(this)"/>
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
        <th style="width:80px">Gambar</th>
        <th style="width:38%">Judul</th>
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
          <?php if (!empty($b['gambar'])): ?>
            <img src="../<?= htmlspecialchars($b['gambar']) ?>"
                 style="width:64px;height:48px;object-fit:cover;border-radius:8px;display:block;"/>
          <?php else: ?>
            <div style="width:64px;height:48px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#ccc;">🖼️</div>
          <?php endif; ?>
        </td>
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
            <a href="berita.php?delete=<?= $b['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus berita ini? Gambar juga akan dihapus.')">🗑️</a>
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

<style>
.upload-zone{border:2px dashed #ddd;border-radius:12px;padding:28px 16px;cursor:pointer;transition:all .2s;background:#fafafa;}
.upload-zone:hover,.upload-zone.drag{border-color:var(--red);background:#fff5f5;}
</style>
<script>
function previewBerita(input) {
  const target = document.getElementById('prevBerita');
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    target.innerHTML = `
      <img src="${e.target.result}" style="width:100%;max-height:200px;object-fit:cover;border-radius:8px;display:block;"/>
      <div style="font-size:11px;color:#888;margin-top:6px;text-align:center;">${input.files[0].name} (${(input.files[0].size/1024).toFixed(0)} KB)</div>`;
  };
  reader.readAsDataURL(input.files[0]);
}
function handleDropBerita(event) {
  event.preventDefault();
  event.currentTarget.classList.remove('drag');
  const file = event.dataTransfer.files[0];
  if (!file) return;
  const input = document.getElementById('gambarBeritaInput');
  const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files;
  previewBerita(input);
}
</script>

<?php require_once 'layout_end.php'; ?>
