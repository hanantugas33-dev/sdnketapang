<?php
$pageTitle  = 'Prestasi Sekolah';
$pageActive = 'prestasi';
require_once 'auth.php';

$db  = getDB();
$msg = '';
$err = '';

// ── Helper upload gambar ──────────────────────────────────────
function uploadGambarPrestasi($fileKey) {
    if (empty($_FILES[$fileKey]['name'])) return null;
    $f   = $_FILES[$fileKey];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $ok  = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $ok))          throw new Exception("Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.");
    if ($f['size'] > 5 * 1024 * 1024) throw new Exception("Ukuran file maksimal 5MB.");
    $dir = dirname(__DIR__) . "/assets/img/prestasi/";
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = uniqid() . '_' . time() . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], $dir . $name))
        throw new Exception("Gagal menyimpan file.");
    return $name;
}
function hapusGambarPrestasi($nama) {
    if (!$nama) return;
    $path = dirname(__DIR__) . "/assets/img/prestasi/{$nama}";
    if (file_exists($path)) unlink($path);
}

// Pastikan kolom gambar ada (migration otomatis)
try {
    $db->query("ALTER TABLE prestasi ADD COLUMN IF NOT EXISTS gambar VARCHAR(255) DEFAULT NULL AFTER medali");
} catch(Exception $e) {}

// --- DELETE ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $row = $db->prepare("SELECT gambar FROM prestasi WHERE id=?");
    $row->execute([(int)$_GET['delete']]);
    $r = $row->fetch();
    hapusGambarPrestasi($r['gambar'] ?? null);
    $db->prepare("DELETE FROM prestasi WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: prestasi.php?msg=deleted'); exit;
}

// --- TOGGLE FEATURED ---
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $db->prepare("UPDATE prestasi SET is_featured = IF(is_featured=1,0,1) WHERE id=?")->execute([(int)$_GET['toggle']]);
    header('Location: prestasi.php'); exit;
}

// --- SAVE (INSERT/UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id     = (int)($_POST['id'] ?? 0);
        $nama   = trim($_POST['nama_prestasi'] ?? '');
        $desk   = trim($_POST['deskripsi'] ?? '');
        $tingkat= $_POST['tingkat'] ?? 'Kecamatan';
        $juara  = trim($_POST['juara'] ?? '');
        $medali = trim($_POST['medali'] ?? '🏆');
        $raih   = trim($_POST['raih_oleh'] ?? '');
        $tahun  = (int)($_POST['tahun'] ?? date('Y'));
        $kat    = $_POST['kategori'] ?? 'akademik';
        $feat   = isset($_POST['is_featured']) ? 1 : 0;
        $urut   = (int)($_POST['urutan'] ?? 0);

        if (!$nama) throw new Exception('Nama prestasi wajib diisi!');

        // Gambar lama
        $gambarLama = '';
        if ($id) {
            $s = $db->prepare("SELECT gambar FROM prestasi WHERE id=?");
            $s->execute([$id]);
            $gambarLama = $s->fetchColumn() ?: '';
        }

        $gambarBaru = uploadGambarPrestasi('gambar');
        $gambar     = $gambarBaru ?? $gambarLama;
        if ($gambarBaru && $gambarLama) hapusGambarPrestasi($gambarLama);

        if (isset($_POST['hapus_gambar']) && $_POST['hapus_gambar'] === '1') {
            hapusGambarPrestasi($gambar);
            $gambar = '';
        }

        if ($id) {
            $db->prepare("UPDATE prestasi SET nama_prestasi=?,deskripsi=?,tingkat=?,juara=?,medali=?,gambar=?,raih_oleh=?,tahun=?,kategori=?,is_featured=?,urutan=? WHERE id=?")
               ->execute([$nama,$desk,$tingkat,$juara,$medali,$gambar,$raih,$tahun,$kat,$feat,$urut,$id]);
        } else {
            $db->prepare("INSERT INTO prestasi (nama_prestasi,deskripsi,tingkat,juara,medali,gambar,raih_oleh,tahun,kategori,is_featured,urutan) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
               ->execute([$nama,$desk,$tingkat,$juara,$medali,$gambar,$raih,$tahun,$kat,$feat,$urut]);
        }
        header('Location: prestasi.php?msg=saved'); exit;
    } catch (Exception $e) {
        $err = $e->getMessage();
    }
}

// Edit mode
$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM prestasi WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}

$showForm = isset($_GET['action']) || $edit;
$list     = $db->query("SELECT * FROM prestasi ORDER BY is_featured DESC, tahun DESC, urutan ASC")->fetchAll();

if (isset($_GET['msg']) && !$msg)
    $msg = $_GET['msg']==='saved' ? '✅ Data berhasil disimpan!' : '🗑️ Data berhasil dihapus.';

require_once 'layout.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<!-- Tabs -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
  <div class="page-tabs">
    <button class="page-tab <?= !$showForm?'active':'' ?>" onclick="location='prestasi.php'">🏆 Daftar Prestasi</button>
    <button class="page-tab <?= $showForm?'active':'' ?>" onclick="location='prestasi.php?action=new'"><?= $edit?'✏️ Edit':'➕ Tambah Baru' ?></button>
  </div>
</div>

<?php if ($showForm): ?>
<!-- FORM -->
<div class="card">
  <div class="card-head">
    <h2><?= $edit ? '✏️ Edit Prestasi' : '➕ Tambah Prestasi' ?></h2>
    <a href="prestasi.php" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>"/>
      <div class="form-row">

        <div class="form-group full">
          <label>Nama Prestasi *</label>
          <input type="text" name="nama_prestasi" value="<?= htmlspecialchars($edit['nama_prestasi']??'') ?>"
                 placeholder="Contoh: Juara I Olimpiade Matematika" required/>
        </div>

        <!-- UPLOAD GAMBAR -->
        <div class="form-group full">
          <label>🖼️ Foto / Piagam Prestasi</label>
          <?php if (!empty($edit['gambar'])): ?>
          <div style="margin-bottom:12px;">
            <img src="../assets/img/prestasi/<?= htmlspecialchars($edit['gambar']) ?>"
                 style="width:100%;max-height:200px;object-fit:cover;border-radius:10px;border:1px solid #eee;display:block;"/>
            <div style="margin-top:8px;display:flex;align-items:center;gap:8px;">
              <input type="checkbox" name="hapus_gambar" value="1" id="hapusGambar"/>
              <label for="hapusGambar" style="font-size:12px;color:#c62828;cursor:pointer;font-weight:500;">🗑️ Hapus gambar ini</label>
            </div>
            <div style="font-size:12px;color:#999;margin-top:4px;">Upload baru untuk mengganti.</div>
          </div>
          <?php endif; ?>
          <div class="upload-zone" onclick="document.getElementById('gambarPrestasiInput').click()"
               ondragover="event.preventDefault();this.classList.add('drag')"
               ondragleave="this.classList.remove('drag')"
               ondrop="handleDropPrestasi(event)">
            <div id="prevPrestasi" style="text-align:center;">
              <div style="font-size:36px;margin-bottom:8px;">🏆</div>
              <div style="font-size:13px;font-weight:600;color:#555;">Klik atau drag foto piagam/trofi ke sini</div>
              <div style="font-size:11px;color:#999;margin-top:4px;">JPG, PNG, WEBP · Maks 5MB</div>
            </div>
          </div>
          <input type="file" id="gambarPrestasiInput" name="gambar"
                 accept="image/jpeg,image/png,image/webp,image/gif"
                 style="display:none" onchange="previewPrestasi(this)"/>
        </div>

        <div class="form-group">
          <label>Tingkat</label>
          <select name="tingkat">
            <?php foreach (['Kecamatan','Kabupaten/Kota','Provinsi','Nasional','Internasional'] as $t): ?>
            <option value="<?= $t ?>" <?= ($edit['tingkat']??'')===$t?'selected':'' ?>><?= $t ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Juara / Peringkat</label>
          <input type="text" name="juara" value="<?= htmlspecialchars($edit['juara']??'') ?>"
                 placeholder="Contoh: Juara I"/>
        </div>
        <div class="form-group">
          <label>Tahun</label>
          <input type="number" name="tahun" value="<?= $edit['tahun']??date('Y') ?>" min="2000" max="<?= date('Y')+1 ?>"/>
        </div>
        <div class="form-group">
          <label>Kategori</label>
          <select name="kategori">
            <?php foreach (['akademik'=>'📚 Akademik','olahraga'=>'⚽ Olahraga','seni'=>'🎨 Seni','agama'=>'🕌 Agama','pramuka'=>'⚜️ Pramuka','lainnya'=>'🌟 Lainnya'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($edit['kategori']??'')===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Medali/Ikon</label>
          <input type="text" name="medali" value="<?= htmlspecialchars($edit['medali']??'🏆') ?>" maxlength="5"
                 style="text-align:center;font-size:22px;width:70px;"/>
          <div class="form-hint">Gunakan emoji: 🥇 🥈 🥉 🏆 ⭐ 🎖️</div>
        </div>
        <div class="form-group">
          <label>Diraih Oleh</label>
          <input type="text" name="raih_oleh" value="<?= htmlspecialchars($edit['raih_oleh']??'') ?>"
                 placeholder="Nama siswa / tim"/>
        </div>
        <div class="form-group full">
          <label>Deskripsi</label>
          <textarea name="deskripsi" rows="3" placeholder="Keterangan singkat pencapaian..."><?= htmlspecialchars($edit['deskripsi']??'') ?></textarea>
        </div>
        <div class="form-group">
          <label>Urutan Tampil</label>
          <input type="number" name="urutan" value="<?= $edit['urutan']??count($list)+1 ?>" min="0"/>
        </div>
        <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:22px;">
          <input type="checkbox" name="is_featured" value="1" id="isFeatured" <?= ($edit['is_featured']??0)?'checked':'' ?> style="width:18px;height:18px;accent-color:var(--red);"/>
          <label for="isFeatured" style="font-size:13px;font-weight:600;cursor:pointer;">⭐ Tampilkan sebagai Unggulan</label>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
        <a href="prestasi.php" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">💾 Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php else: ?>
<!-- DAFTAR PRESTASI -->
<div class="card">
  <div class="card-head">
    <h2>Daftar Prestasi <span style="color:#999;font-size:13px;font-weight:400">(<?= count($list) ?> prestasi)</span></h2>
  </div>
  <?php if ($list): ?>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width:80px">Foto</th>
        <th style="width:36%">Prestasi</th>
        <th>Tingkat</th>
        <th>Tahun</th>
        <th>Diraih Oleh</th>
        <th style="width:80px">Unggulan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($list as $p): ?>
      <tr>
        <td>
          <?php if (!empty($p['gambar'])): ?>
            <img src="../assets/img/prestasi/<?= htmlspecialchars($p['gambar']) ?>"
                 style="width:64px;height:48px;object-fit:cover;border-radius:8px;display:block;"/>
          <?php else: ?>
            <div style="width:64px;height:48px;background:#fffde7;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:26px;">
              <?= $p['medali'] ?: '🏆' ?>
            </div>
          <?php endif; ?>
        </td>
        <td>
          <div style="font-weight:600"><?= htmlspecialchars($p['nama_prestasi']) ?></div>
          <div style="font-size:12px;color:#999;margin-top:2px"><?= htmlspecialchars($p['juara'] ?? '') ?></div>
        </td>
        <td><span class="badge badge-kegiatan">Tk. <?= htmlspecialchars($p['tingkat']) ?></span></td>
        <td style="font-weight:600"><?= $p['tahun'] ?></td>
        <td style="font-size:12px;color:#555"><?= $p['raih_oleh'] ? htmlspecialchars($p['raih_oleh']) : '<span style="color:#ccc">—</span>' ?></td>
        <td style="text-align:center">
          <a href="prestasi.php?toggle=<?= $p['id'] ?>" title="Toggle unggulan">
            <?= $p['is_featured'] ? '<span style="font-size:18px">⭐</span>' : '<span style="font-size:18px;opacity:.25">☆</span>' ?>
          </a>
        </td>
        <td>
          <div class="td-actions">
            <a href="prestasi.php?edit=<?= $p['id'] ?>" class="btn btn-outline btn-sm">✏️</a>
            <a href="prestasi.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Hapus prestasi ini? Gambar juga akan dihapus.')">🗑️</a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <div class="empty-state"><div>🏆</div><p>Belum ada prestasi. <a href="prestasi.php?action=new" style="color:var(--red)">Tambah prestasi pertama →</a></p></div>
  <?php endif; ?>
</div>
<?php endif; ?>

<style>
.upload-zone{border:2px dashed #ddd;border-radius:12px;padding:28px 16px;cursor:pointer;transition:all .2s;background:#fafafa;}
.upload-zone:hover,.upload-zone.drag{border-color:var(--red);background:#fff5f5;}
</style>
<script>
function previewPrestasi(input) {
  const target = document.getElementById('prevPrestasi');
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    target.innerHTML = `
      <img src="${e.target.result}" style="width:100%;max-height:200px;object-fit:cover;border-radius:8px;display:block;"/>
      <div style="font-size:11px;color:#888;margin-top:6px;text-align:center;">${input.files[0].name} (${(input.files[0].size/1024).toFixed(0)} KB)</div>`;
  };
  reader.readAsDataURL(input.files[0]);
}
function handleDropPrestasi(event) {
  event.preventDefault();
  event.currentTarget.classList.remove('drag');
  const file = event.dataTransfer.files[0];
  if (!file) return;
  const input = document.getElementById('gambarPrestasiInput');
  const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files;
  previewPrestasi(input);
}
</script>

<?php require_once 'layout_end.php'; ?>
