<?php
$pageTitle  = 'Profil Sekolah';
$pageActive = 'profil';
require_once 'auth.php';

$db  = getDB();
$msg = '';

// SAVE PROFIL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profil'])) {
    $fields = ['nama_sekolah','npsn','nss','akreditasi','tahun_berdiri','alamat','kelurahan','kecamatan','kabupaten_kota','provinsi','kode_pos','telepon','email','website','total_siswa','total_guru','total_kelas','jam_operasional','maps_embed','visi','misi','tujuan','sejarah'];
    $vals = [];
    foreach ($fields as $f) $vals[$f] = trim($_POST[$f] ?? '');
    // Cek ada atau belum
    $count = $db->query("SELECT COUNT(*) FROM profil_sekolah")->fetchColumn();
    if ($count) {
        $set = implode(',', array_map(fn($f) => "$f=?", $fields));
        $db->prepare("UPDATE profil_sekolah SET $set LIMIT 1")->execute(array_values($vals));
    } else {
        $cols = implode(',', $fields);
        $phs  = implode(',', array_fill(0, count($fields), '?'));
        $db->prepare("INSERT INTO profil_sekolah ($cols) VALUES ($phs)")->execute(array_values($vals));
    }
    header('Location: profil.php?msg=saved'); exit;
}

// SAVE STRUKTUR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_struktur'])) {
    $id      = (int)($_POST['id'] ?? 0);
    $nama    = trim($_POST['nama'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $nip     = trim($_POST['nip'] ?? '');
    $level   = $_POST['level'] ?? 'staff';
    $urutan  = (int)($_POST['urutan'] ?? 0);
    $allowed = ['kepala','komite','wakil','staff','koordinator','guru_kelas','guru_mapel','penunjang','siswa'];
    if (!in_array($level, $allowed)) $level = 'staff';
    if ($id) {
        $db->prepare("UPDATE struktur_organisasi SET nama=?,jabatan=?,nip=?,level=?,urutan=? WHERE id=?")->execute([$nama,$jabatan,$nip,$level,$urutan,$id]);
    } else {
        $db->prepare("INSERT INTO struktur_organisasi (nama,jabatan,nip,level,urutan,is_active) VALUES (?,?,?,?,?,1)")->execute([$nama,$jabatan,$nip,$level,$urutan]);
    }
    header('Location: profil.php?tab=struktur&msg=saved'); exit;
}

// DELETE STRUKTUR
if (isset($_GET['del_str']) && is_numeric($_GET['del_str'])) {
    $db->prepare("DELETE FROM struktur_organisasi WHERE id=?")->execute([(int)$_GET['del_str']]);
    header('Location: profil.php?tab=struktur&msg=deleted'); exit;
}

$profil   = $db->query("SELECT * FROM profil_sekolah LIMIT 1")->fetch() ?: [];
$struktur = $db->query("SELECT * FROM struktur_organisasi WHERE is_active=1 ORDER BY urutan")->fetchAll();

$editStr = null;
if (isset($_GET['edit_str']) && is_numeric($_GET['edit_str'])) {
    $s = $db->prepare("SELECT * FROM struktur_organisasi WHERE id=?");
    $s->execute([(int)$_GET['edit_str']]);
    $editStr = $s->fetch();
}

$activeTab = $_GET['tab'] ?? 'info';
if (isset($_GET['msg'])) $msg = $_GET['msg']==='saved' ? '✅ Berhasil disimpan!' : '🗑️ Berhasil dihapus.';

require_once 'layout.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

<div class="page-tabs">
  <button class="page-tab <?= $activeTab==='info'?'active':'' ?>" onclick="location='profil.php?tab=info'">🏫 Info Sekolah</button>
  <button class="page-tab <?= $activeTab==='visi'?'active':'' ?>" onclick="location='profil.php?tab=visi'">🔭 Visi Misi & Tujuan</button>
  <button class="page-tab <?= $activeTab==='sejarah'?'active':'' ?>" onclick="location='profil.php?tab=sejarah'">📜 Sejarah</button>
  <button class="page-tab <?= $activeTab==='struktur'?'active':'' ?>" onclick="location='profil.php?tab=struktur'">🏛️ Struktur Organisasi</button>
  <button class="page-tab <?= $activeTab==='kontak'?'active':'' ?>" onclick="location='profil.php?tab=kontak'">📍 Kontak & Maps</button>
</div>

<?php if ($activeTab === 'struktur'): ?>
<!-- TAB STRUKTUR -->
<div style="display:grid;grid-template-columns:1fr 1.2fr;gap:20px;">

  <div class="card">
    <div class="card-head"><h2><?= $editStr ? '✏️ Edit Jabatan' : '➕ Tambah Jabatan' ?></h2></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="save_struktur" value="1"/>
        <input type="hidden" name="id" value="<?= $editStr['id'] ?? 0 ?>"/>
        <div class="form-group">
          <label>Nama + Gelar *</label>
          <input type="text" name="nama" value="<?= htmlspecialchars($editStr['nama']??'') ?>" required placeholder="Budi Santoso, S.Pd."/>
        </div>
        <div class="form-group">
          <label>Jabatan *</label>
          <input type="text" name="jabatan" value="<?= htmlspecialchars($editStr['jabatan']??'') ?>" required placeholder="Kepala Sekolah"/>
        </div>
        <div class="form-group">
          <label>NIP (opsional)</label>
          <input type="text" name="nip" value="<?= htmlspecialchars($editStr['nip']??'') ?>" placeholder="Kosongkan jika tidak ada"/>
        </div>
        <div class="form-group">
          <label>Level / Hierarki</label>
          <select name="level">
            <option value="kepala"      <?= ($editStr['level']??'')==='kepala'      ?'selected':'' ?>>👑 Kepala Sekolah</option>
            <option value="komite"      <?= ($editStr['level']??'')==='komite'      ?'selected':'' ?>>🤝 Komite Sekolah</option>
            <option value="wakil"       <?= ($editStr['level']??'')==='wakil'       ?'selected':'' ?>>🔑 Wakil Kepala Sekolah</option>
            <option value="staff"       <?= ($editStr['level']??'staff')==='staff'  ?'selected':'' ?>>💼 Staff Admin (TU/Bendahara/Operator)</option>
            <option value="koordinator" <?= ($editStr['level']??'')==='koordinator' ?'selected':'' ?>>📋 Koordinator</option>
            <option value="guru_kelas"  <?= ($editStr['level']??'')==='guru_kelas'  ?'selected':'' ?>>📚 Guru Kelas</option>
            <option value="guru_mapel"  <?= ($editStr['level']??'')==='guru_mapel'  ?'selected':'' ?>>🎓 Guru Mata Pelajaran</option>
            <option value="penunjang"   <?= ($editStr['level']??'')==='penunjang'   ?'selected':'' ?>>🛠️ Tenaga Penunjang (Perpus/UKS/Kebersihan)</option>
            <option value="siswa"       <?= ($editStr['level']??'')==='siswa'       ?'selected':'' ?>>🎒 Siswa</option>
          </select>
        </div>
        <div class="form-group">
          <label>Urutan Tampil</label>
          <input type="number" name="urutan" value="<?= $editStr['urutan']??0 ?>" min="0"/>
        </div>
        <div style="display:flex;gap:8px;">
          <?php if ($editStr): ?><a href="profil.php?tab=struktur" class="btn btn-outline">Batal</a><?php endif; ?>
          <button type="submit" class="btn btn-primary">💾 Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-head"><h2>Daftar Struktur Organisasi</h2></div>
    <?php
    $levelLabel = [
      'kepala'      => ['👑 Kepala Sekolah',     '#fdecea','#B71C1C'],
      'komite'      => ['🤝 Komite',              '#fffbeb','#92400e'],
      'wakil'       => ['🔑 Wakil Kepala',        '#fef2f2','#991b1b'],
      'staff'       => ['💼 Staff Admin',          '#eff6ff','#1d4ed8'],
      'koordinator' => ['📋 Koordinator',          '#ecfdf5','#065f46'],
      'guru_kelas'  => ['📚 Guru Kelas',           '#f5f3ff','#4c1d95'],
      'guru_mapel'  => ['🎓 Guru Mapel',           '#fff7ed','#9a3412'],
      'penunjang'   => ['🛠️ Penunjang',            '#f9fafb','#374151'],
      'siswa'       => ['🎒 Siswa',                '#f1f5f9','#1e293b'],
    ];
    ?>
    <?php if ($struktur): ?>
    <table class="data-table">
      <thead><tr><th>#</th><th>Nama</th><th>Jabatan</th><th>Level</th><th style="width:80px">Urutan</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($struktur as $i => $s):
          $lv = $levelLabel[$s['level']] ?? ['🔹 '.$s['level'],'#f3f4f6','#374151'];
        ?>
        <tr>
          <td style="color:#9ca3af;font-size:12px"><?= $i+1 ?></td>
          <td style="font-weight:600"><?= htmlspecialchars($s['nama']) ?></td>
          <td style="color:#6b7280"><?= htmlspecialchars($s['jabatan']) ?></td>
          <td>
            <span style="background:<?= $lv[1] ?>;color:<?= $lv[2] ?>;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap">
              <?= $lv[0] ?>
            </span>
          </td>
          <td style="text-align:center;color:#9ca3af"><?= $s['urutan'] ?></td>
          <td>
            <div class="td-actions">
              <a href="profil.php?tab=struktur&edit_str=<?= $s['id'] ?>" class="btn btn-outline btn-sm">✏️ Edit</a>
              <a href="profil.php?del_str=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus <?= htmlspecialchars(addslashes($s['nama'])) ?>?')">🗑️</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state"><div>🏛️</div><p>Belum ada data struktur organisasi.</p></div>
    <?php endif; ?>
  </div>
</div>

<?php else: ?>
<div class="card">
  <div class="card-head"><h2>Edit <?= ['info'=>'Informasi Sekolah','visi'=>'Visi, Misi & Tujuan','sejarah'=>'Sejarah Sekolah','kontak'=>'Kontak & Lokasi'][$activeTab] ?? '' ?></h2></div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="save_profil" value="1"/>
      <!-- Hidden fields untuk field lain yang tidak ditampilkan di tab ini -->
      <?php foreach (['nama_sekolah','npsn','nss','akreditasi','tahun_berdiri','alamat','kelurahan','kecamatan','kabupaten_kota','provinsi','kode_pos','telepon','email','website','total_siswa','total_guru','total_kelas','jam_operasional','maps_embed','visi','misi','tujuan','sejarah'] as $f): ?>
        <?php if (!in_array($f, ['nama_sekolah','npsn','nss','akreditasi','tahun_berdiri','alamat','kelurahan','kecamatan','kabupaten_kota','provinsi','kode_pos','telepon','email','website','total_siswa','total_guru','total_kelas'])
               || $activeTab !== 'info'): ?>
          <?php if (!in_array($f, ['visi','misi','tujuan']) || $activeTab !== 'visi'): ?>
            <?php if ($f !== 'sejarah' || $activeTab !== 'sejarah'): ?>
              <?php if (!in_array($f, ['alamat','kode_pos','telepon','email','website','maps_embed','jam_operasional']) || $activeTab !== 'kontak'): ?>
                <input type="hidden" name="<?= $f ?>" value="<?= htmlspecialchars($profil[$f]??'') ?>"/>
              <?php endif; ?>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
      <?php endforeach; ?>

      <?php if ($activeTab === 'info'): ?>
      <div class="form-row">
        <div class="form-group full">
          <label>Nama Sekolah *</label>
          <input type="text" name="nama_sekolah" value="<?= htmlspecialchars($profil['nama_sekolah']??'SDN Ketapang') ?>" required/>
        </div>
        <div class="form-group"><label>NPSN</label><input type="text" name="npsn" value="<?= htmlspecialchars($profil['npsn']??'') ?>" placeholder="8 digit"/></div>
        <div class="form-group"><label>NSS</label><input type="text" name="nss" value="<?= htmlspecialchars($profil['nss']??'') ?>"/></div>
        <div class="form-group">
          <label>Akreditasi</label>
          <select name="akreditasi">
            <?php foreach (['A','B','C','Belum'] as $a): ?>
            <option value="<?= $a ?>" <?= ($profil['akreditasi']??'A')===$a?'selected':'' ?>><?= $a ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Tahun Berdiri</label><input type="number" name="tahun_berdiri" value="<?= $profil['tahun_berdiri']??'' ?>" placeholder="1975" min="1900" max="2099"/></div>
        <div class="form-group"><label>Total Siswa</label><input type="number" name="total_siswa" value="<?= $profil['total_siswa']??0 ?>"/></div>
        <div class="form-group"><label>Total Guru & Staf</label><input type="number" name="total_guru" value="<?= $profil['total_guru']??0 ?>"/></div>
        <div class="form-group"><label>Total Kelas</label><input type="number" name="total_kelas" value="<?= $profil['total_kelas']??0 ?>"/></div>
        <div class="form-group full"><label>Jam Operasional</label><input type="text" name="jam_operasional" value="<?= htmlspecialchars($profil['jam_operasional']??'') ?>" placeholder="Senin–Jumat: 07.00–13.30 WIB"/></div>
      </div>

      <?php elseif ($activeTab === 'visi'): ?>
      <div class="form-group">
        <label>Visi Sekolah</label>
        <textarea name="visi" rows="4" placeholder="Tulis visi sekolah..."><?= htmlspecialchars($profil['visi']??'') ?></textarea>
      </div>
      <div class="form-group">
        <label>Misi Sekolah <span style="color:#999;font-weight:400">(gunakan baris baru untuk tiap poin)</span></label>
        <textarea name="misi" rows="7" placeholder="Tulis misi 1&#10;Tulis misi 2&#10;Tulis misi 3"><?= htmlspecialchars($profil['misi']??'') ?></textarea>
      </div>
      <div class="form-group">
        <label>Tujuan Sekolah <span style="color:#999;font-weight:400">(gunakan baris baru untuk tiap poin)</span></label>
        <textarea name="tujuan" rows="5" placeholder="Tulis tujuan 1&#10;Tulis tujuan 2"><?= htmlspecialchars($profil['tujuan']??'') ?></textarea>
      </div>

      <?php elseif ($activeTab === 'sejarah'): ?>
      <div class="form-group">
        <label>Sejarah Sekolah</label>
        <textarea name="sejarah" rows="12" placeholder="Tulis sejarah lengkap sekolah di sini..."><?= htmlspecialchars($profil['sejarah']??'') ?></textarea>
        <div class="form-hint">Gunakan paragraf biasa. Teks ini akan ditampilkan di halaman profil sekolah.</div>
      </div>

      <?php elseif ($activeTab === 'kontak'): ?>
      <div class="form-row">
        <div class="form-group full"><label>Alamat Lengkap</label><input type="text" name="alamat" value="<?= htmlspecialchars($profil['alamat']??'') ?>"/></div>
        <div class="form-group"><label>Kelurahan/Desa</label><input type="text" name="kelurahan" value="<?= htmlspecialchars($profil['kelurahan']??'') ?>"/></div>
        <div class="form-group"><label>Kecamatan</label><input type="text" name="kecamatan" value="<?= htmlspecialchars($profil['kecamatan']??'') ?>"/></div>
        <div class="form-group"><label>Kabupaten/Kota</label><input type="text" name="kabupaten_kota" value="<?= htmlspecialchars($profil['kabupaten_kota']??'') ?>"/></div>
        <div class="form-group"><label>Provinsi</label><input type="text" name="provinsi" value="<?= htmlspecialchars($profil['provinsi']??'') ?>"/></div>
        <div class="form-group"><label>Kode Pos</label><input type="text" name="kode_pos" value="<?= htmlspecialchars($profil['kode_pos']??'') ?>"/></div>
        <div class="form-group"><label>Nomor Telepon</label><input type="text" name="telepon" value="<?= htmlspecialchars($profil['telepon']??'') ?>"/></div>
        <div class="form-group"><label>Email Resmi</label><input type="email" name="email" value="<?= htmlspecialchars($profil['email']??'') ?>"/></div>
        <div class="form-group full"><label>Website</label><input type="url" name="website" value="<?= htmlspecialchars($profil['website']??'') ?>" placeholder="https://sdnketapang.sch.id"/></div>
        <div class="form-group full">
          <label>Google Maps Embed Code</label>
          <textarea name="maps_embed" rows="4" placeholder='Paste kode iframe dari Google Maps di sini...\n<iframe src="https://www.google.com/maps/embed?pb=..." ...></iframe>'><?= htmlspecialchars($profil['maps_embed']??'') ?></textarea>
          <div class="form-hint">📌 Cara: Google Maps → cari sekolah → Bagikan → Sematkan peta → Salin kode iframe</div>
        </div>
      </div>
      <?php endif; ?>

      <div style="display:flex;justify-content:flex-end;margin-top:8px;">
        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php require_once 'layout_end.php'; ?>
