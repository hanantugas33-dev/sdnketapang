<?php
$pageTitle  = 'Galeri Foto';
$pageActive = 'galeri';
require_once 'auth.php';

$db  = getDB();
$msg = '';

function uploadGambar($fileKey, $oldGambar = '') {
    if (empty($_FILES[$fileKey]['name'])) return $oldGambar;
    $uploadDir = __DIR__ . '/../assets/img/galeri/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed)) return $oldGambar;
    $filename = 'galeri_' . time() . '_' . rand(100,999) . '.' . $ext;
    move_uploaded_file($_FILES[$fileKey]['tmp_name'], $uploadDir . $filename);
    return 'assets/img/galeri/' . $filename;
}

// DELETE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $db->prepare("UPDATE galeri SET status='nonaktif' WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: galeri.php?msg=deleted'); exit;
}

// TOGGLE LARGE
if (isset($_GET['toggle_large']) && is_numeric($_GET['toggle_large'])) {
    $db->prepare("UPDATE galeri SET is_large = IF(is_large=1,0,1) WHERE id=?")->execute([(int)$_GET['toggle_large']]);
    header('Location: galeri.php'); exit;
}

// SAVE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)($_POST['id'] ?? 0);
    $judul    = trim($_POST['judul'] ?? '');
    $deskripsi= trim($_POST['deskripsi'] ?? '');
    $kategori = $_POST['kategori'] ?? 'kegiatan';
    $icon     = $_POST['icon'] ?? '🏫';
    $is_large = isset($_POST['is_large']) ? 1 : 0;
    $urutan   = (int)($_POST['urutan'] ?? 0);
    $oldGambar= $_POST['old_gambar'] ?? '';
    $gambar   = uploadGambar('gambar', $oldGambar);

    if ($id) {
        $db->prepare("UPDATE galeri SET judul=?,deskripsi=?,kategori=?,icon=?,is_large=?,urutan=?,gambar=? WHERE id=?")
           ->execute([$judul,$deskripsi,$kategori,$icon,$is_large,$urutan,$gambar,$id]);
    } else {
        $db->prepare("INSERT INTO galeri (judul,deskripsi,kategori,icon,is_large,urutan,gambar,status) VALUES (?,?,?,?,?,?,?,'aktif')")
           ->execute([$judul,$deskripsi,$kategori,$icon,$is_large,$urutan,$gambar]);
    }
    header('Location: galeri.php?msg=saved'); exit;
}

$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM galeri WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}
$showForm = isset($_GET['action']) || $edit;

$filter = $_GET['filter'] ?? 'semua';
if ($filter !== 'semua') {
    $stmt = $db->prepare("SELECT * FROM galeri WHERE status='aktif' AND kategori=? ORDER BY urutan ASC");
    $stmt->execute([$filter]);
} else {
    $stmt = $db->query("SELECT * FROM galeri WHERE status='aktif' ORDER BY urutan ASC");
}
$list = $stmt->fetchAll();

if (isset($_GET['msg'])) $msg = $_GET['msg']==='saved' ? '✅ Foto berhasil disimpan!' : '🗑️ Foto berhasil dihapus.';

require_once 'layout.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
  <div class="page-tabs">
    <button class="page-tab <?= !$showForm?'active':'' ?>" onclick="location='galeri.php'">🖼️ Daftar Foto</button>
    <button class="page-tab <?= $showForm?'active':'' ?>" onclick="location='galeri.php?action=new'"><?= $edit?'✏️ Edit':'➕ Tambah Foto' ?></button>
  </div>
  <?php if (!$showForm): ?>
  <select onchange="location='galeri.php?filter='+this.value" style="padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;">
    <option value="semua" <?= $filter==='semua'?'selected':'' ?>>Semua Kategori</option>
    <option value="kegiatan" <?= $filter==='kegiatan'?'selected':'' ?>>Kegiatan</option>
    <option value="prestasi" <?= $filter==='prestasi'?'selected':'' ?>>Prestasi</option>
    <option value="fasilitas" <?= $filter==='fasilitas'?'selected':'' ?>>Fasilitas</option>
    <option value="ekskul" <?= $filter==='ekskul'?'selected':'' ?>>Ekskul</option>
    <option value="umum" <?= $filter==='umum'?'selected':'' ?>>Umum</option>
  </select>
  <?php endif; ?>
</div>

<?php if ($showForm): ?>
<div class="card">
  <div class="card-head">
    <h2><?= $edit ? '✏️ Edit Foto' : '➕ Upload Foto ke Galeri' ?></h2>
    <a href="galeri.php" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>"/>
      <input type="hidden" name="old_gambar" value="<?= $edit['gambar'] ?? '' ?>"/>

      <!-- Preview Area -->
      <div style="text-align:center;margin-bottom:24px;">
        <?php if (!empty($edit['gambar'])): ?>
          <img src="../<?= htmlspecialchars($edit['gambar']) ?>" id="imgPreview" style="max-height:200px;border-radius:10px;border:2px solid #eee;"/>
        <?php else: ?>
          <div id="imgPreviewEmpty" style="height:150px;background:#f5f5f5;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:50px;border:2px dashed #ddd;">🖼️</div>
          <img id="imgPreview" style="display:none;max-height:200px;border-radius:10px;border:2px solid #eee;"/>
        <?php endif; ?>
      </div>

      <div class="form-row">
        <div class="form-group full">
          <label>Upload Gambar</label>
          <div class="photo-upload" onclick="document.getElementById('gambarInput').click()">
            <input type="file" id="gambarInput" name="gambar" accept="image/*" onchange="previewImg(this)"/>
            <div>📷 Klik untuk pilih foto</div>
            <div style="font-size:11px;color:#999;margin-top:4px">JPG, PNG, WebP — Rekomendasi min 800x600px</div>
          </div>
        </div>
        <div class="form-group">
          <label>Judul Foto *</label>
          <input type="text" name="judul" value="<?= htmlspecialchars($edit['judul']??'') ?>" required placeholder="Contoh: Upacara Bendera Senin"/>
        </div>
        <div class="form-group">
          <label>Kategori</label>
          <select name="kategori">
            <?php foreach (['kegiatan'=>'🎯 Kegiatan','prestasi'=>'🏆 Prestasi','fasilitas'=>'🏛️ Fasilitas','ekskul'=>'⚽ Ekskul','umum'=>'📸 Umum'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($edit['kategori']??'kegiatan')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Icon Emoji (backup jika foto tidak ada)</label>
          <input type="text" name="icon" value="<?= htmlspecialchars($edit['icon']??'🏫') ?>" maxlength="5"/>
        </div>
        <div class="form-group">
          <label>Urutan Tampil</label>
          <input type="number" name="urutan" value="<?= $edit['urutan']??0 ?>" min="0"/>
        </div>
        <div class="form-group full">
          <label>Deskripsi</label>
          <textarea name="deskripsi" rows="3" placeholder="Tulis deskripsi singkat foto ini..."><?= htmlspecialchars($edit['deskripsi']??'') ?></textarea>
        </div>
        <div class="form-group full">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
            <input type="checkbox" name="is_large" <?= ($edit['is_large']??0)?'checked':'' ?> style="width:16px;height:16px;accent-color:var(--red)"/>
            <span>Tampilkan sebagai foto besar (ukuran 2x di galeri)</span>
          </label>
          <div class="form-hint">Cocok untuk foto unggulan atau foto utama</div>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
        <a href="galeri.php" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">💾 Simpan</button>
      </div>
    </form>
  </div>
</div>
<script>
function previewImg(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      const prev = document.getElementById('imgPreview');
      const empty = document.getElementById('imgPreviewEmpty');
      if (empty) empty.style.display = 'none';
      prev.src = e.target.result;
      prev.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php else: ?>
<!-- DAFTAR GALERI - GRID VIEW -->
<div class="card">
  <div class="card-head">
    <h2>Galeri Foto <span style="color:#999;font-size:13px;font-weight:400">(<?= count($list) ?> foto)</span></h2>
    <div style="font-size:12px;color:#999;">💡 Foto tampil di website sesuai urutan</div>
  </div>
  <?php if ($list): ?>
  <table class="data-table">
    <thead><tr><th>Preview</th><th>Judul</th><th>Kategori</th><th>Besar?</th><th>Urutan</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($list as $g): ?>
      <tr>
        <td>
          <div style="width:60px;height:48px;border-radius:6px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;overflow:hidden;font-size:24px;">
            <?php if ($g['gambar']): ?>
              <img src="../<?= htmlspecialchars($g['gambar']) ?>" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'"/>
            <?php else: echo $g['icon']; endif; ?>
          </div>
        </td>
        <td>
          <div style="font-weight:500"><?= htmlspecialchars($g['judul']) ?></div>
          <div style="font-size:12px;color:#999"><?= mb_strimwidth(htmlspecialchars($g['deskripsi']??''),0,50,'...') ?></div>
        </td>
        <td><span class="badge badge-aktif" style="text-transform:capitalize"><?= $g['kategori'] ?></span></td>
        <td>
          <a href="galeri.php?toggle_large=<?= $g['id'] ?>" title="Toggle ukuran besar">
            <span style="font-size:18px"><?= $g['is_large'] ? '✅' : '⬜' ?></span>
          </a>
        </td>
        <td style="color:#999;font-size:13px"><?= $g['urutan'] ?></td>
        <td>
          <div class="td-actions">
            <a href="galeri.php?edit=<?= $g['id'] ?>" class="btn btn-outline btn-sm">✏️</a>
            <a href="galeri.php?delete=<?= $g['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus foto ini?')">🗑️</a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <div class="empty-state"><div>🖼️</div><p>Belum ada foto di galeri. <a href="galeri.php?action=new" style="color:var(--red)">Upload foto pertama →</a></p></div>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once 'layout_end.php'; ?>
