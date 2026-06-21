<?php
$pageTitle  = 'Fasilitas & Ekskul';
$pageActive = 'fasilitas';
require_once 'auth.php';

$db  = getDB();
$msg = '';

// ══════════════════════════════════════════════════
//  HELPER: upload foto
// ══════════════════════════════════════════════════
function uploadFoto($fileKey, $subDir) {
    if (empty($_FILES[$fileKey]['name'])) return null;
    $f    = $_FILES[$fileKey];
    $ext  = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $ok   = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $ok))          throw new Exception("Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.");
    if ($f['size'] > 5 * 1024 * 1024) throw new Exception("Ukuran file maksimal 5MB.");
    $dir  = dirname(__DIR__) . "/assets/img/{$subDir}/";
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = uniqid() . '_' . time() . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], $dir . $name))
        throw new Exception("Gagal menyimpan file.");
    return $name;
}

function hapusFoto($nama, $subDir) {
    if (!$nama) return;
    $path = dirname(__DIR__) . "/assets/img/{$subDir}/{$nama}";
    if (file_exists($path)) unlink($path);
}

// ══════════════════════════════════════════════════
//  FASILITAS — CRUD
// ══════════════════════════════════════════════════
if (isset($_GET['del_fas']) && is_numeric($_GET['del_fas'])) {
    $row = $db->prepare("SELECT foto FROM fasilitas WHERE id=?");
    $row->execute([(int)$_GET['del_fas']]);
    $r = $row->fetch();
    hapusFoto($r['foto'] ?? null, 'fasilitas');
    $db->prepare("UPDATE fasilitas SET is_active=0 WHERE id=?")->execute([(int)$_GET['del_fas']]);
    header('Location: fasilitas.php?tab=fasilitas&msg=deleted'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_fas'])) {
    try {
        $id   = (int)($_POST['id_fas'] ?? 0);
        $nama = trim($_POST['nama_fas'] ?? '');
        $desk = trim($_POST['desk_fas'] ?? '');
        $icon = trim($_POST['icon_fas'] ?? '🏫');
        $jml  = (int)($_POST['jumlah_fas'] ?? 1);
        $kond = $_POST['kondisi_fas'] ?? 'baik';
        $urut = (int)($_POST['urutan_fas'] ?? 0);

        // Foto lama
        $fotoLama = '';
        if ($id) {
            $s = $db->prepare("SELECT foto FROM fasilitas WHERE id=?");
            $s->execute([$id]);
            $fotoLama = $s->fetchColumn() ?: '';
        }

        // Upload foto baru (jika ada)
        $fotoBaru = uploadFoto('foto_fas', 'fasilitas');
        $foto     = $fotoBaru ?? $fotoLama; // pakai yang baru, fallback ke lama

        // Hapus foto lama jika diganti
        if ($fotoBaru && $fotoLama) hapusFoto($fotoLama, 'fasilitas');

        // Hapus foto jika di-centang "hapus foto"
        if (isset($_POST['hapus_foto_fas']) && $_POST['hapus_foto_fas'] === '1') {
            hapusFoto($foto, 'fasilitas');
            $foto = '';
        }

        if ($id) {
            $db->prepare("UPDATE fasilitas SET nama=?,deskripsi=?,icon=?,foto=?,jumlah=?,kondisi=?,urutan=? WHERE id=?")
               ->execute([$nama,$desk,$icon,$foto,$jml,$kond,$urut,$id]);
        } else {
            $db->prepare("INSERT INTO fasilitas (nama,deskripsi,icon,foto,jumlah,kondisi,urutan) VALUES (?,?,?,?,?,?,?)")
               ->execute([$nama,$desk,$icon,$foto,$jml,$kond,$urut]);
        }
        header('Location: fasilitas.php?tab=fasilitas&msg=saved'); exit;
    } catch (Exception $e) {
        $msg = '❌ ' . $e->getMessage();
    }
}

// ══════════════════════════════════════════════════
//  EKSKUL — CRUD
// ══════════════════════════════════════════════════
if (isset($_GET['del_eks']) && is_numeric($_GET['del_eks'])) {
    $row = $db->prepare("SELECT foto FROM ekskul WHERE id=?");
    $row->execute([(int)$_GET['del_eks']]);
    $r = $row->fetch();
    hapusFoto($r['foto'] ?? null, 'ekskul');
    $db->prepare("UPDATE ekskul SET is_active=0 WHERE id=?")->execute([(int)$_GET['del_eks']]);
    header('Location: fasilitas.php?tab=ekskul&msg=deleted'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_eks'])) {
    try {
        $id      = (int)($_POST['id_eks'] ?? 0);
        $nama    = trim($_POST['nama_eks'] ?? '');
        $icon    = trim($_POST['icon_eks'] ?? '🎯');
        $hari    = trim($_POST['hari_eks'] ?? 'Senin');
        $jam_m   = $_POST['jam_mulai_eks']   ?? '14:00';
        $jam_s   = $_POST['jam_selesai_eks'] ?? '16:00';
        $pembina = trim($_POST['pembina_eks'] ?? '');
        $desk    = trim($_POST['desk_eks'] ?? '');
        $urut    = (int)($_POST['urutan_eks'] ?? 0);

        $fotoLama = '';
        if ($id) {
            $s = $db->prepare("SELECT foto FROM ekskul WHERE id=?");
            $s->execute([$id]);
            $fotoLama = $s->fetchColumn() ?: '';
        }

        $fotoBaru = uploadFoto('foto_eks', 'ekskul');
        $foto     = $fotoBaru ?? $fotoLama;
        if ($fotoBaru && $fotoLama) hapusFoto($fotoLama, 'ekskul');

        if (isset($_POST['hapus_foto_eks']) && $_POST['hapus_foto_eks'] === '1') {
            hapusFoto($foto, 'ekskul');
            $foto = '';
        }

        if ($id) {
            $db->prepare("UPDATE ekskul SET nama=?,icon=?,foto=?,hari=?,jam_mulai=?,jam_selesai=?,pembina=?,deskripsi=?,urutan=? WHERE id=?")
               ->execute([$nama,$icon,$foto,$hari,$jam_m,$jam_s,$pembina,$desk,$urut,$id]);
        } else {
            $db->prepare("INSERT INTO ekskul (nama,icon,foto,hari,jam_mulai,jam_selesai,pembina,deskripsi,urutan) VALUES (?,?,?,?,?,?,?,?,?)")
               ->execute([$nama,$icon,$foto,$hari,$jam_m,$jam_s,$pembina,$desk,$urut]);
        }
        header('Location: fasilitas.php?tab=ekskul&msg=saved'); exit;
    } catch (Exception $e) {
        $msg = '❌ ' . $e->getMessage();
    }
}

// ── Edit data ─────────────────────────────────────
$editFas = null;
if (isset($_GET['edit_fas']) && is_numeric($_GET['edit_fas'])) {
    $s = $db->prepare("SELECT * FROM fasilitas WHERE id=?"); $s->execute([(int)$_GET['edit_fas']]);
    $editFas = $s->fetch();
}
$editEks = null;
if (isset($_GET['edit_eks']) && is_numeric($_GET['edit_eks'])) {
    $s = $db->prepare("SELECT * FROM ekskul WHERE id=?"); $s->execute([(int)$_GET['edit_eks']]);
    $editEks = $s->fetch();
}

$listFas   = $db->query("SELECT * FROM fasilitas WHERE is_active=1 ORDER BY urutan,id")->fetchAll();
$listEks   = $db->query("SELECT * FROM ekskul WHERE is_active=1 ORDER BY urutan,id")->fetchAll();
$activeTab = $_GET['tab'] ?? 'fasilitas';

if (isset($_GET['msg']) && !$msg)
    $msg = $_GET['msg']==='saved' ? '✅ Data berhasil disimpan!' : '🗑️ Data berhasil dihapus.';

require_once 'layout.php';
?>

<?php if ($msg): ?>
<div class="alert <?= str_starts_with($msg,'❌')?'alert-danger':'alert-success' ?>"><?= $msg ?></div>
<?php endif; ?>

<!-- TABS -->
<div class="page-tabs">
  <button class="page-tab <?= $activeTab==='fasilitas'?'active':'' ?>"
          onclick="location='fasilitas.php?tab=fasilitas'">🏛️ Fasilitas Sekolah</button>
  <button class="page-tab <?= $activeTab==='ekskul'?'active':'' ?>"
          onclick="location='fasilitas.php?tab=ekskul'">⚽ Ekstrakurikuler</button>
</div>

<?php if ($activeTab === 'fasilitas'): ?>
<!-- ══════ TAB FASILITAS ══════ -->
<div style="display:grid;grid-template-columns:360px 1fr;gap:20px;align-items:start;">

  <!-- FORM FASILITAS -->
  <div class="card" style="position:sticky;top:20px;">
    <div class="card-head">
      <h2><?= $editFas ? '✏️ Edit Fasilitas' : '➕ Tambah Fasilitas' ?></h2>
      <?php if ($editFas): ?>
        <a href="fasilitas.php?tab=fasilitas" class="btn btn-outline btn-sm">✕ Batal</a>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="save_fas" value="1"/>
        <input type="hidden" name="id_fas"   value="<?= $editFas['id']??0 ?>"/>

        <!-- Icon + Nama -->
        <div style="display:flex;gap:10px;align-items:flex-end;">
          <div class="form-group" style="margin-bottom:0;flex:0 0 64px;">
            <label>Icon</label>
            <input type="text" name="icon_fas" id="iconFas"
                   value="<?= htmlspecialchars($editFas['icon']??'🏫') ?>"
                   maxlength="5"
                   style="text-align:center;font-size:22px;padding:6px 4px;width:64px;"/>
          </div>
          <div class="form-group" style="margin-bottom:0;flex:1;">
            <label>Nama Fasilitas *</label>
            <input type="text" name="nama_fas" id="namaFas"
                   value="<?= htmlspecialchars($editFas['nama']??'') ?>"
                   required placeholder="Contoh: Ruang Kelas"/>
          </div>
        </div>

        <div class="form-group" style="margin-top:14px;">
          <label>Deskripsi Singkat</label>
          <input type="text" name="desk_fas" id="deskFas"
                 value="<?= htmlspecialchars($editFas['deskripsi']??'') ?>"
                 placeholder="Contoh: 12 ruang kelas berventilasi baik" maxlength="200"/>
        </div>

        <!-- UPLOAD FOTO -->
        <div class="form-group">
          <label>📷 Foto Fasilitas</label>

          <?php if (!empty($editFas['foto'])): ?>
          <!-- Foto existing -->
          <div style="margin-bottom:10px;position:relative;display:inline-block;">
            <img src="../assets/img/fasilitas/<?= htmlspecialchars($editFas['foto']) ?>"
                 style="width:100%;max-height:140px;object-fit:cover;border-radius:10px;display:block;border:1px solid #eee;"/>
            <div style="margin-top:6px;display:flex;align-items:center;gap:8px;">
              <input type="checkbox" name="hapus_foto_fas" value="1" id="hapusFas"/>
              <label for="hapusFas" style="font-size:12px;color:#c62828;cursor:pointer;font-weight:500;">
                🗑️ Hapus foto ini
              </label>
            </div>
          </div>
          <div style="font-size:12px;color:#999;margin-bottom:6px;">
            Biarkan kosong untuk mempertahankan foto lama. Upload baru untuk mengganti.
          </div>
          <?php endif; ?>

          <div class="upload-zone" id="uploadZoneFas"
               onclick="document.getElementById('fotoFasInput').click()"
               ondragover="event.preventDefault();this.classList.add('drag')"
               ondragleave="this.classList.remove('drag')"
               ondrop="handleDrop(event,'fotoFasInput','prevFas')">
            <div id="prevFas" style="text-align:center;">
              <div style="font-size:28px;margin-bottom:6px;">📷</div>
              <div style="font-size:13px;font-weight:600;color:#555;">Klik atau drag foto ke sini</div>
              <div style="font-size:11px;color:#999;margin-top:4px;">JPG, PNG, WEBP · Maks 5MB</div>
            </div>
          </div>
          <input type="file" id="fotoFasInput" name="foto_fas"
                 accept="image/jpeg,image/png,image/webp,image/gif"
                 style="display:none"
                 onchange="previewImg(this,'prevFas')"/>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
          <div class="form-group">
            <label>Jumlah</label>
            <input type="number" name="jumlah_fas" value="<?= $editFas['jumlah']??1 ?>" min="0"/>
          </div>
          <div class="form-group">
            <label>Kondisi</label>
            <select name="kondisi_fas">
              <option value="baik"  <?= ($editFas['kondisi']??'baik')==='baik' ?'selected':'' ?>>✅ Baik</option>
              <option value="cukup" <?= ($editFas['kondisi']??'')==='cukup'?'selected':'' ?>>⚠️ Cukup</option>
              <option value="rusak" <?= ($editFas['kondisi']??'')==='rusak'?'selected':'' ?>>❌ Rusak</option>
            </select>
          </div>
          <div class="form-group">
            <label>Urutan</label>
            <input type="number" name="urutan_fas" value="<?= $editFas['urutan']??count($listFas)+1 ?>" min="0"/>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;margin-top:4px;">
          💾 <?= $editFas ? 'Simpan Perubahan' : 'Tambah Fasilitas' ?>
        </button>
      </form>

      <!-- Quick icon picker -->
      <div style="margin-top:14px;border-top:1px solid #eee;padding-top:12px;">
        <div style="font-size:11px;color:#999;margin-bottom:8px;font-weight:600;letter-spacing:.5px;">ICON CEPAT (jika tanpa foto)</div>
        <div style="display:flex;flex-wrap:wrap;gap:5px;">
          <?php foreach (['📚','📖','🖥️','⚽','🕌','🍽️','🏥','🚻','🪑','📁','🎨','🏫','💧','🔬','🎭','🎤','🏋️','🎸'] as $em): ?>
            <button type="button"
                    onclick="document.getElementById('iconFas').value='<?= $em ?>'"
                    style="width:32px;height:32px;border:1px solid #eee;border-radius:8px;
                           background:white;font-size:17px;cursor:pointer;">
              <?= $em ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- LIST FASILITAS -->
  <div class="card">
    <div class="card-head">
      <h2>Daftar Fasilitas
        <span style="color:#999;font-size:13px;font-weight:400;">(<?= count($listFas) ?> item)</span>
      </h2>
    </div>
    <?php if ($listFas): ?>
    <table class="data-table">
      <thead>
        <tr>
          <th style="width:64px">Foto</th>
          <th>Nama</th>
          <th>Deskripsi</th>
          <th style="text-align:center;width:50px">Jml</th>
          <th style="width:100px">Kondisi</th>
          <th style="width:90px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($listFas as $f): ?>
        <tr>
          <td>
            <?php if ($f['foto']): ?>
              <img src="../assets/img/fasilitas/<?= htmlspecialchars($f['foto']) ?>"
                   style="width:52px;height:44px;object-fit:cover;border-radius:8px;display:block;"/>
            <?php else: ?>
              <div style="width:44px;height:44px;background:#f5f5f5;border-radius:8px;
                          display:flex;align-items:center;justify-content:center;font-size:22px;">
                <?= $f['icon'] ?>
              </div>
            <?php endif; ?>
          </td>
          <td style="font-weight:600;"><?= htmlspecialchars($f['nama']) ?></td>
          <td style="font-size:12px;color:#666;max-width:180px;">
            <?= $f['deskripsi'] ? htmlspecialchars($f['deskripsi']) : '<span style="color:#ccc">—</span>' ?>
          </td>
          <td style="text-align:center;font-weight:600;"><?= $f['jumlah'] ?></td>
          <td>
            <?php $kMap=['baik'=>['badge-aktif','✅ Baik'],'cukup'=>['badge-berita','⚠️ Cukup'],'rusak'=>['badge-draft','❌ Rusak']];
                  [$cls,$lbl] = $kMap[$f['kondisi']] ?? ['badge-draft','—']; ?>
            <span class="badge <?= $cls ?>"><?= $lbl ?></span>
          </td>
          <td>
            <div class="td-actions">
              <a href="fasilitas.php?tab=fasilitas&edit_fas=<?= $f['id'] ?>"
                 class="btn btn-outline btn-sm">✏️</a>
              <a href="fasilitas.php?del_fas=<?= $f['id'] ?>"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Hapus fasilitas ini? Foto juga akan dihapus.')">🗑️</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state"><div>🏛️</div><p>Belum ada fasilitas.</p></div>
    <?php endif; ?>
  </div>
</div>

<?php else: /* ══════ TAB EKSKUL ══════ */ ?>
<div style="display:grid;grid-template-columns:380px 1fr;gap:20px;align-items:start;">

  <!-- FORM EKSKUL -->
  <div class="card" style="position:sticky;top:20px;">
    <div class="card-head">
      <h2><?= $editEks ? '✏️ Edit Ekskul' : '➕ Tambah Ekskul' ?></h2>
      <?php if ($editEks): ?>
        <a href="fasilitas.php?tab=ekskul" class="btn btn-outline btn-sm">✕ Batal</a>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="save_eks" value="1"/>
        <input type="hidden" name="id_eks"   value="<?= $editEks['id']??0 ?>"/>

        <div style="display:flex;gap:10px;align-items:flex-end;">
          <div class="form-group" style="margin-bottom:0;flex:0 0 64px;">
            <label>Icon</label>
            <input type="text" name="icon_eks"
                   value="<?= htmlspecialchars($editEks['icon']??'🎯') ?>"
                   maxlength="5"
                   style="text-align:center;font-size:22px;padding:6px 4px;width:64px;"/>
          </div>
          <div class="form-group" style="margin-bottom:0;flex:1;">
            <label>Nama Ekskul *</label>
            <input type="text" name="nama_eks"
                   value="<?= htmlspecialchars($editEks['nama']??'') ?>"
                   required placeholder="Contoh: Pramuka"/>
          </div>
        </div>

        <!-- UPLOAD FOTO EKSKUL -->
        <div class="form-group" style="margin-top:14px;">
          <label>📷 Foto Ekskul</label>

          <?php if (!empty($editEks['foto'])): ?>
          <div style="margin-bottom:10px;">
            <img src="../assets/img/ekskul/<?= htmlspecialchars($editEks['foto']) ?>"
                 style="width:100%;max-height:120px;object-fit:cover;border-radius:10px;display:block;border:1px solid #eee;"/>
            <div style="margin-top:6px;display:flex;align-items:center;gap:8px;">
              <input type="checkbox" name="hapus_foto_eks" value="1" id="hapusEks"/>
              <label for="hapusEks" style="font-size:12px;color:#c62828;cursor:pointer;font-weight:500;">
                🗑️ Hapus foto ini
              </label>
            </div>
          </div>
          <div style="font-size:12px;color:#999;margin-bottom:6px;">Upload baru untuk mengganti.</div>
          <?php endif; ?>

          <div class="upload-zone"
               onclick="document.getElementById('fotoEksInput').click()"
               ondragover="event.preventDefault();this.classList.add('drag')"
               ondragleave="this.classList.remove('drag')"
               ondrop="handleDrop(event,'fotoEksInput','prevEks')">
            <div id="prevEks" style="text-align:center;">
              <div style="font-size:28px;margin-bottom:6px;">📷</div>
              <div style="font-size:13px;font-weight:600;color:#555;">Klik atau drag foto ke sini</div>
              <div style="font-size:11px;color:#999;margin-top:4px;">JPG, PNG, WEBP · Maks 5MB</div>
            </div>
          </div>
          <input type="file" id="fotoEksInput" name="foto_eks"
                 accept="image/jpeg,image/png,image/webp,image/gif"
                 style="display:none"
                 onchange="previewImg(this,'prevEks')"/>
        </div>

        <div class="form-group">
          <label>Hari Latihan</label>
          <select name="hari_eks">
            <?php foreach (["Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu"] as $h): ?>
              <option value="<?= $h ?>" <?= ($editEks['hari']??'')===$h?'selected':'' ?>><?= $h ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group">
            <label>⏰ Jam Mulai</label>
            <input type="time" name="jam_mulai_eks" value="<?= substr($editEks['jam_mulai']??'14:00',0,5) ?>"/>
          </div>
          <div class="form-group">
            <label>⏰ Jam Selesai</label>
            <input type="time" name="jam_selesai_eks" value="<?= substr($editEks['jam_selesai']??'16:00',0,5) ?>"/>
          </div>
        </div>
        <div class="form-group">
          <label>Nama Pembina</label>
          <input type="text" name="pembina_eks"
                 value="<?= htmlspecialchars($editEks['pembina']??'') ?>"
                 placeholder="Contoh: Pak Ahmad Fauzi"/>
        </div>
        <div class="form-group">
          <label>Deskripsi (Opsional)</label>
          <textarea name="desk_eks" rows="2"
                    placeholder="Deskripsi singkat..."><?= htmlspecialchars($editEks['deskripsi']??'') ?></textarea>
        </div>
        <div class="form-group">
          <label>Urutan</label>
          <input type="number" name="urutan_eks" value="<?= $editEks['urutan']??count($listEks)+1 ?>" min="0"/>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;margin-top:4px;">
          💾 <?= $editEks ? 'Simpan Perubahan' : 'Tambah Ekskul' ?>
        </button>
      </form>

      <!-- Quick icon picker ekskul -->
      <div style="margin-top:14px;border-top:1px solid #eee;padding-top:12px;">
        <div style="font-size:11px;color:#999;margin-bottom:8px;font-weight:600;letter-spacing:.5px;">ICON CEPAT (jika tanpa foto)</div>
        <div style="display:flex;flex-wrap:wrap;gap:5px;">
          <?php foreach (['⚜️','🥁','⚽','🏸','🎨','💃','📖','🖥️','🎤','🏋️','🎸','🏊','🤸','🎭','🥊','🎯','🏐','🎺'] as $em): ?>
            <button type="button"
                    onclick="document.querySelector('[name=icon_eks]').value='<?= $em ?>'"
                    style="width:32px;height:32px;border:1px solid #eee;border-radius:8px;
                           background:white;font-size:17px;cursor:pointer;">
              <?= $em ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- LIST EKSKUL -->
  <div class="card">
    <div class="card-head">
      <h2>Daftar Ekskul
        <span style="color:#999;font-size:13px;font-weight:400;">(<?= count($listEks) ?> ekskul)</span>
      </h2>
    </div>
    <?php if ($listEks): ?>
    <table class="data-table">
      <thead>
        <tr>
          <th style="width:64px">Foto</th>
          <th>Ekskul</th>
          <th style="width:90px">Hari</th>
          <th style="width:130px">Jam</th>
          <th>Pembina</th>
          <th style="width:90px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($listEks as $e): ?>
        <tr>
          <td>
            <?php if ($e['foto']): ?>
              <img src="../assets/img/ekskul/<?= htmlspecialchars($e['foto']) ?>"
                   style="width:52px;height:44px;object-fit:cover;border-radius:8px;display:block;"/>
            <?php else: ?>
              <div style="width:44px;height:44px;background:#f5f5f5;border-radius:8px;
                          display:flex;align-items:center;justify-content:center;font-size:22px;">
                <?= $e['icon'] ?>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-weight:600;font-size:14px;"><?= htmlspecialchars($e['nama']) ?></div>
            <?php if ($e['deskripsi']): ?>
              <div style="font-size:11px;color:#999;"><?= htmlspecialchars($e['deskripsi']) ?></div>
            <?php endif; ?>
          </td>
          <td><span class="badge badge-kegiatan"><?= htmlspecialchars($e['hari']) ?></span></td>
          <td style="font-size:12px;color:#555;">⏰ <?= substr($e['jam_mulai']??'',0,5) ?> – <?= substr($e['jam_selesai']??'',0,5) ?></td>
          <td style="font-size:13px;"><?= $e['pembina'] ? htmlspecialchars($e['pembina']) : '<span style="color:#ccc">—</span>' ?></td>
          <td>
            <div class="td-actions">
              <a href="fasilitas.php?tab=ekskul&edit_eks=<?= $e['id'] ?>"
                 class="btn btn-outline btn-sm">✏️</a>
              <a href="fasilitas.php?del_eks=<?= $e['id'] ?>"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Hapus ekskul ini? Foto juga akan dihapus.')">🗑️</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state"><div>⚽</div><p>Belum ada ekskul.</p></div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<style>
.upload-zone{
  border:2px dashed #ddd;border-radius:12px;padding:24px 16px;
  cursor:pointer;transition:all .2s;background:#fafafa;
  margin-bottom:0;
}
.upload-zone:hover,.upload-zone.drag{
  border-color:var(--red);background:#fff5f5;
}
.upload-zone img{
  width:100%;max-height:150px;object-fit:cover;
  border-radius:8px;display:block;
}
</style>

<script>
function previewImg(input, targetId) {
  const target = document.getElementById(targetId);
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = function(e) {
    target.innerHTML = `
      <img src="${e.target.result}"
           style="width:100%;max-height:150px;object-fit:cover;border-radius:8px;display:block;"/>
      <div style="font-size:11px;color:#888;margin-top:6px;text-align:center;">
        ${input.files[0].name} (${(input.files[0].size/1024).toFixed(0)} KB)
      </div>`;
  };
  reader.readAsDataURL(input.files[0]);
}

function handleDrop(event, inputId, previewId) {
  event.preventDefault();
  event.currentTarget.classList.remove('drag');
  const file = event.dataTransfer.files[0];
  if (!file) return;
  const input = document.getElementById(inputId);
  const dt = new DataTransfer();
  dt.items.add(file);
  input.files = dt.files;
  previewImg(input, previewId);
}
</script>

<?php require_once 'layout_end.php'; ?>
