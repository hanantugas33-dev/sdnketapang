<?php
$pageTitle  = 'Guru & Staf';
$pageActive = 'guru';
require_once 'auth.php';

$db  = getDB();
$msg = '';

// Upload foto helper
function uploadFoto($fileKey, $oldFoto = '') {
    if (empty($_FILES[$fileKey]['name'])) return $oldFoto;
    $uploadDir = __DIR__ . '/../assets/img/guru/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if (!in_array($ext, $allowed)) return $oldFoto;
    $filename = 'guru_' . time() . '_' . rand(100,999) . '.' . $ext;
    move_uploaded_file($_FILES[$fileKey]['tmp_name'], $uploadDir . $filename);
    return 'assets/img/guru/' . $filename;
}

// DELETE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $db->prepare("UPDATE guru SET is_active=0 WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: guru.php?msg=deleted'); exit;
}

// SAVE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)($_POST['id'] ?? 0);
    $nama      = trim($_POST['nama'] ?? '');
    $nip       = trim($_POST['nip'] ?? '');
    $jabatan   = trim($_POST['jabatan'] ?? '');
    $jabSingkat= $_POST['jabatan_singkat'] ?? 'guru';
    $mapel     = trim($_POST['mapel'] ?? '');
    $bidang    = trim($_POST['bidang'] ?? '');
    $kelas     = trim($_POST['kelas'] ?? '');
    $pendidikan= trim($_POST['pendidikan'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $urutan    = (int)($_POST['urutan'] ?? 0);
    $oldFoto   = $_POST['old_foto'] ?? '';
    $foto      = uploadFoto('foto', $oldFoto);

    if ($id) {
        $db->prepare("UPDATE guru SET nama=?,nip=?,jabatan=?,jabatan_singkat=?,mapel=?,bidang=?,kelas=?,pendidikan=?,email=?,urutan=?,foto=? WHERE id=?")
           ->execute([$nama,$nip,$jabatan,$jabSingkat,$mapel,$bidang,$kelas,$pendidikan,$email,$urutan,$foto,$id]);
    } else {
        $db->prepare("INSERT INTO guru (nama,nip,jabatan,jabatan_singkat,mapel,bidang,kelas,pendidikan,email,urutan,foto) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
           ->execute([$nama,$nip,$jabatan,$jabSingkat,$mapel,$bidang,$kelas,$pendidikan,$email,$urutan,$foto]);
    }
    header('Location: guru.php?msg=saved'); exit;
}

$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM guru WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}
$showForm = isset($_GET['action']) || $edit;

$filter = $_GET['filter'] ?? 'semua';
if ($filter !== 'semua') {
    $stmt = $db->prepare("SELECT * FROM guru WHERE is_active=1 AND jabatan_singkat=? ORDER BY urutan");
    $stmt->execute([$filter]);
} else {
    $stmt = $db->query("SELECT * FROM guru WHERE is_active=1 ORDER BY urutan");
}
$list = $stmt->fetchAll();

if (isset($_GET['msg'])) {
    $msg = $_GET['msg']==='saved' ? '✅ Data guru berhasil disimpan!' : '🗑️ Data guru berhasil dihapus.';
}

require_once 'layout.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
  <div class="page-tabs">
    <button class="page-tab <?= !$showForm?'active':'' ?>" onclick="location='guru.php'">📋 Daftar</button>
    <button class="page-tab <?= $showForm?'active':'' ?>" onclick="location='guru.php?action=new'"><?= $edit?'✏️ Edit':'➕ Tambah' ?></button>
  </div>
  <?php if (!$showForm): ?>
  <select onchange="location='guru.php?filter='+this.value" style="padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;">
    <option value="semua" <?= $filter==='semua'?'selected':'' ?>>Semua</option>
    <option value="kepala" <?= $filter==='kepala'?'selected':'' ?>>Kepala Sekolah</option>
    <option value="guru" <?= $filter==='guru'?'selected':'' ?>>Guru</option>
    <option value="tata_usaha" <?= $filter==='tata_usaha'?'selected':'' ?>>Tata Usaha</option>
    <option value="staff" <?= $filter==='staff'?'selected':'' ?>>Staff</option>
  </select>
  <?php endif; ?>
</div>

<?php if ($showForm): ?>
<div class="card">
  <div class="card-head">
    <h2><?= $edit ? '✏️ Edit Data Guru' : '➕ Tambah Guru / Staf' ?></h2>
    <a href="guru.php" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>"/>
      <input type="hidden" name="old_foto" value="<?= $edit['foto'] ?? '' ?>"/>
      <div class="form-row">
        <!-- Foto Upload -->
        <div class="form-group full" style="text-align:center">
          <label>Foto (Opsional)</label>
          <?php if (!empty($edit['foto'])): ?>
            <img src="../<?= htmlspecialchars($edit['foto']) ?>" class="photo-preview" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>👤</text></svg>'"/>
          <?php else: ?>
            <div style="width:100px;height:100px;background:#f5f5f5;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:40px;margin:0 auto 8px;">👤</div>
          <?php endif; ?>
          <div class="photo-upload" onclick="document.getElementById('fotoInput').click()">
            <input type="file" id="fotoInput" name="foto" accept="image/*" onchange="previewFoto(this)"/>
            <div>📷 Klik untuk upload foto</div>
            <div style="font-size:11px;color:#999;margin-top:4px">JPG, PNG, WebP — maks 2MB</div>
          </div>
        </div>
        <div class="form-group">
          <label>Nama Lengkap + Gelar *</label>
          <input type="text" name="nama" value="<?= htmlspecialchars($edit['nama']??'') ?>" placeholder="Contoh: Budi Santoso, S.Pd." required/>
        </div>
        <div class="form-group">
          <label>NIP</label>
          <input type="text" name="nip" value="<?= htmlspecialchars($edit['nip']??'') ?>" placeholder="Kosongkan jika honorer"/>
        </div>
        <div class="form-group">
          <label>Jabatan Lengkap *</label>
          <input type="text" name="jabatan" value="<?= htmlspecialchars($edit['jabatan']??'') ?>" placeholder="Contoh: Guru Kelas IV" required/>
        </div>
        <div class="form-group">
          <label>Kategori</label>
          <select name="jabatan_singkat">
            <option value="kepala" <?= ($edit['jabatan_singkat']??'')==='kepala'?'selected':'' ?>>Kepala Sekolah</option>
            <option value="guru" <?= ($edit['jabatan_singkat']??'guru')==='guru'?'selected':'' ?>>Guru</option>
            <option value="tata_usaha" <?= ($edit['jabatan_singkat']??'')==='tata_usaha'?'selected':'' ?>>Tata Usaha</option>
            <option value="staff" <?= ($edit['jabatan_singkat']??'')==='staff'?'selected':'' ?>>Staff / Penjaga</option>
            <option value="honor" <?= ($edit['jabatan_singkat']??'')==='honor'?'selected':'' ?>>Honorer</option>
          </select>
        </div>
        <div class="form-group">
          <label>Mata Pelajaran / Bidang</label>
          <input type="text" name="mapel" value="<?= htmlspecialchars($edit['mapel']??'') ?>" placeholder="Contoh: Matematika, Tematik"/>
        </div>
        <div class="form-group">
          <label>Kelas Diajar</label>
          <input type="text" name="kelas" value="<?= htmlspecialchars($edit['kelas']??'') ?>" placeholder="Contoh: IV A & IV B"/>
        </div>
        <div class="form-group">
          <label>Pendidikan Terakhir</label>
          <input type="text" name="pendidikan" value="<?= htmlspecialchars($edit['pendidikan']??'') ?>" placeholder="Contoh: S1 PGSD"/>
        </div>
        <div class="form-group">
          <label>Bidang</label>
          <input type="text" name="bidang" value="<?= htmlspecialchars($edit['bidang']??'') ?>" placeholder="Contoh: Kelas IV"/>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($edit['email']??'') ?>" placeholder="guru@email.com"/>
        </div>
        <div class="form-group">
          <label>Urutan Tampil</label>
          <input type="number" name="urutan" value="<?= $edit['urutan']??0 ?>" min="0"/>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
        <a href="guru.php" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">💾 Simpan</button>
      </div>
    </form>
  </div>
</div>
<script>
function previewFoto(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      let prev = document.querySelector('.photo-preview');
      if (!prev) {
        prev = document.createElement('img');
        prev.className = 'photo-preview';
        input.parentElement.before(prev);
      }
      prev.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php else: ?>
<div class="card">
  <div class="card-head">
    <h2>Daftar Guru & Staf <span style="color:#999;font-size:13px;font-weight:400">(<?= count($list) ?> orang)</span></h2>
  </div>
  <?php if ($list): ?>
  <table class="data-table">
    <thead><tr><th>Foto</th><th>Nama</th><th>Jabatan</th><th>Mapel/Bidang</th><th>NIP</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($list as $g): ?>
      <tr>
        <td>
          <div class="avatar">
            <?php if ($g['foto']): ?>
              <img src="../<?= htmlspecialchars($g['foto']) ?>" onerror="this.style.display='none'"/>
            <?php else: ?>
              👤
            <?php endif; ?>
          </div>
        </td>
        <td>
          <div style="font-weight:500"><?= htmlspecialchars($g['nama']) ?></div>
          <div style="font-size:11px;color:#999"><?= htmlspecialchars($g['pendidikan']??'') ?></div>
        </td>
        <td>
          <span class="badge badge-aktif"><?= htmlspecialchars($g['jabatan']) ?></span>
        </td>
        <td style="font-size:13px;color:#555"><?= htmlspecialchars($g['mapel']?:$g['bidang']) ?></td>
        <td style="font-size:12px;color:#999;font-family:monospace"><?= $g['nip'] ?: '<span style="color:#ccc">—</span>' ?></td>
        <td>
          <div class="td-actions">
            <a href="guru.php?edit=<?= $g['id'] ?>" class="btn btn-outline btn-sm">✏️</a>
            <a href="guru.php?delete=<?= $g['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data guru ini?')">🗑️</a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <div class="empty-state"><div>👨‍🏫</div><p>Belum ada data guru. <a href="guru.php?action=new" style="color:var(--red)">Tambah sekarang →</a></p></div>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once 'layout_end.php'; ?>
