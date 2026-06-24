<?php
$pageTitle  = 'Data Siswa';
$pageActive = 'siswa';
require_once 'auth.php';

$db  = getDB();
$msg = '';

// ── DELETE ────────────────────────────────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $db->prepare("DELETE FROM data_siswa WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: siswa.php?msg=deleted'); exit;
}

// ── SAVE / UPDATE ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)($_POST['id'] ?? 0);
    $tahun     = trim($_POST['tahun_ajaran'] ?? '');
    $tingkat   = (int)($_POST['tingkat'] ?? 1);
    $rombel    = strtoupper(trim($_POST['rombel'] ?? 'A'));
    $kelas     = $tingkat . $rombel;
    $laki      = (int)($_POST['laki_laki'] ?? 0);
    $perempuan = (int)($_POST['perempuan'] ?? 0);
    $wali      = trim($_POST['wali_kelas'] ?? '');
    $catatan   = trim($_POST['catatan'] ?? '');

    if ($id) {
        $db->prepare("UPDATE data_siswa SET tahun_ajaran=?,kelas=?,tingkat=?,rombel=?,laki_laki=?,perempuan=?,wali_kelas=?,catatan=? WHERE id=?")
           ->execute([$tahun, $kelas, $tingkat, $rombel, $laki, $perempuan, $wali, $catatan, $id]);
    } else {
        $db->prepare("INSERT INTO data_siswa (tahun_ajaran,kelas,tingkat,rombel,laki_laki,perempuan,wali_kelas,catatan) VALUES (?,?,?,?,?,?,?,?)")
           ->execute([$tahun, $kelas, $tingkat, $rombel, $laki, $perempuan, $wali, $catatan]);
    }
    header('Location: siswa.php?msg=saved'); exit;
}

// ── EDIT MODE ─────────────────────────────────────────────────────────────────
$edit     = null;
$showForm = isset($_GET['action']) || isset($_GET['edit']);
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM data_siswa WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit     = $s->fetch();
    $showForm = true;
}

// ── DATA LIST ─────────────────────────────────────────────────────────────────
$tahunList  = $db->query("SELECT DISTINCT tahun_ajaran FROM data_siswa ORDER BY tahun_ajaran DESC")->fetchAll(PDO::FETCH_COLUMN);
$tahunAktif = $_GET['tahun'] ?? ($tahunList[0] ?? date('Y') . '/' . (date('Y') + 1));

$stmt = $db->prepare("SELECT * FROM data_siswa WHERE tahun_ajaran=? ORDER BY tingkat, rombel");
$stmt->execute([$tahunAktif]);
$list = $stmt->fetchAll();

$totalL = array_sum(array_column($list, 'laki_laki'));
$totalP = array_sum(array_column($list, 'perempuan'));
$totalS = $totalL + $totalP;

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'] === 'saved' ? '✅ Data siswa berhasil disimpan!' : '🗑️ Data kelas berhasil dihapus.';
}

require_once 'layout.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <div class="page-tabs" style="margin-bottom:0">
    <button class="page-tab <?= !$showForm?'active':'' ?>" onclick="location='siswa.php'">📋 Data Kelas</button>
    <button class="page-tab <?= $showForm?'active':'' ?>" onclick="location='siswa.php?action=new'">
      <?= $edit ? '✏️ Edit Kelas' : '➕ Tambah Kelas' ?>
    </button>
  </div>
  <?php if (!$showForm): ?>
  <div style="display:flex;gap:10px;align-items:center;">
    <select onchange="location='siswa.php?tahun='+this.value"
            style="padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;background:white;">
      <?php foreach ($tahunList as $t): ?>
        <option value="<?= htmlspecialchars($t) ?>" <?= $t===$tahunAktif?'selected':'' ?>>TA <?= htmlspecialchars($t) ?></option>
      <?php endforeach; ?>
    </select>
    <a href="siswa.php?action=new" class="btn btn-primary">➕ Tambah Kelas</a>
  </div>
  <?php endif; ?>
</div>

<?php if ($showForm): ?>
<!-- ═══ FORM ═══ -->
<div class="card">
  <div class="card-head">
    <h2><?= $edit ? '✏️ Edit Data Kelas' : '➕ Tambah Data Kelas' ?></h2>
    <a href="siswa.php" class="btn btn-outline btn-sm">← Kembali</a>
  </div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>"/>
      <div class="form-row">

        <div class="form-group">
          <label>Tahun Ajaran *</label>
          <input type="text" name="tahun_ajaran"
                 value="<?= htmlspecialchars($edit['tahun_ajaran'] ?? $tahunAktif) ?>"
                 placeholder="2025/2026" required/>
          <div class="form-hint">Format: YYYY/YYYY</div>
        </div>

        <div class="form-group">
          <label>Preview Nama Kelas</label>
          <div id="kelasPreview"
               style="padding:10px 14px;background:#fff5f5;border:2px solid var(--red);border-radius:8px;font-weight:700;font-size:20px;color:var(--red);text-align:center;">
            <?= htmlspecialchars($edit['kelas'] ?? '1A') ?>
          </div>
        </div>

        <div class="form-group">
          <label>Tingkat / Kelas *</label>
          <select name="tingkat" id="selTingkat" onchange="updatePreview()">
            <?php for ($i=1;$i<=6;$i++): ?>
              <option value="<?= $i ?>" <?= ($edit['tingkat']??1)==$i?'selected':'' ?>>Kelas <?= $i ?></option>
            <?php endfor; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Rombel *</label>
          <input type="text" name="rombel" id="inpRombel"
                 value="<?= htmlspecialchars($edit['rombel'] ?? 'A') ?>"
                 placeholder="A" maxlength="3" required
                 oninput="updatePreview()" style="text-transform:uppercase"/>
          <div class="form-hint">Contoh: A, B, C</div>
        </div>

        <div class="form-group full">
          <label>Wali Kelas (Opsional)</label>
          <input type="text" name="wali_kelas"
                 value="<?= htmlspecialchars($edit['wali_kelas'] ?? '') ?>"
                 placeholder="Nama wali kelas"/>
        </div>

        <!-- Laki-laki -->
        <div class="form-group">
          <label>👦 Jumlah Siswa Laki-laki</label>
          <div style="display:flex;align-items:center;gap:8px;">
            <button type="button" onclick="step('laki_laki',-1)"
                    style="width:38px;height:38px;border-radius:8px;border:1px solid #ddd;background:white;font-size:20px;cursor:pointer;line-height:1;">−</button>
            <input type="number" name="laki_laki" id="laki_laki"
                   value="<?= $edit['laki_laki'] ?? 0 ?>" min="0" max="999"
                   style="text-align:center;font-size:22px;font-weight:700;width:80px;border:1px solid #ddd;border-radius:8px;padding:6px;"
                   oninput="updateTotal()"/>
            <button type="button" onclick="step('laki_laki',1)"
                    style="width:38px;height:38px;border-radius:8px;border:1px solid #ddd;background:white;font-size:20px;cursor:pointer;line-height:1;">+</button>
          </div>
        </div>

        <!-- Perempuan -->
        <div class="form-group">
          <label>👧 Jumlah Siswa Perempuan</label>
          <div style="display:flex;align-items:center;gap:8px;">
            <button type="button" onclick="step('perempuan',-1)"
                    style="width:38px;height:38px;border-radius:8px;border:1px solid #ddd;background:white;font-size:20px;cursor:pointer;line-height:1;">−</button>
            <input type="number" name="perempuan" id="perempuan"
                   value="<?= $edit['perempuan'] ?? 0 ?>" min="0" max="999"
                   style="text-align:center;font-size:22px;font-weight:700;width:80px;border:1px solid #ddd;border-radius:8px;padding:6px;"
                   oninput="updateTotal()"/>
            <button type="button" onclick="step('perempuan',1)"
                    style="width:38px;height:38px;border-radius:8px;border:1px solid #ddd;background:white;font-size:20px;cursor:pointer;line-height:1;">+</button>
          </div>
        </div>

        <div class="form-group full">
          <label>Total Kelas Ini</label>
          <div id="totalBox"
               style="display:inline-block;background:var(--dark2);color:white;border-radius:10px;padding:10px 20px;font-family:'Lora',serif;font-size:28px;font-weight:700;">
            <span id="totalNum"><?= ($edit['laki_laki']??0)+($edit['perempuan']??0) ?></span>
            <span style="font-size:14px;font-weight:400;opacity:.6;margin-left:6px;">siswa</span>
          </div>
        </div>

        <div class="form-group full">
          <label>Catatan (Opsional)</label>
          <textarea name="catatan" rows="2"
                    placeholder="Contoh: termasuk siswa inklusi"><?= htmlspecialchars($edit['catatan'] ?? '') ?></textarea>
        </div>

      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
        <a href="siswa.php" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">💾 Simpan</button>
      </div>
    </form>
  </div>
</div>
<script>
function step(id, d) {
    const el = document.getElementById(id);
    el.value = Math.max(0, parseInt(el.value||0) + d);
    updateTotal();
}
function updateTotal() {
    const l = parseInt(document.getElementById('laki_laki').value||0);
    const p = parseInt(document.getElementById('perempuan').value||0);
    document.getElementById('totalNum').textContent = l + p;
}
function updatePreview() {
    const t = document.getElementById('selTingkat').value;
    const r = document.getElementById('inpRombel').value.toUpperCase();
    document.getElementById('kelasPreview').textContent = t + r;
}
</script>

<?php else: ?>
<!-- ═══ TABEL ═══ -->

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
  <div class="stat-box">
    <div class="stat-num"><?= $totalS ?></div>
    <div class="stat-lbl">🎒 Total Semua Siswa</div>
  </div>
  <div class="stat-box" style="border-color:#1565C0">
    <div class="stat-num" style="color:#1565C0"><?= $totalL ?></div>
    <div class="stat-lbl">👦 Laki-laki</div>
  </div>
  <div class="stat-box" style="border-color:#880E4F">
    <div class="stat-num" style="color:#880E4F"><?= $totalP ?></div>
    <div class="stat-lbl">👧 Perempuan</div>
  </div>
  <div class="stat-box gold">
    <div class="stat-num"><?= count($list) ?></div>
    <div class="stat-lbl">🏫 Jumlah Rombel</div>
  </div>
</div>

<?php if ($list):
    $grouped = [];
    foreach ($list as $r) $grouped[$r['tingkat']][] = $r;
?>

<div style="display:flex;flex-direction:column;gap:16px;">
<?php foreach ($grouped as $tingkat => $rows):
    $subL = array_sum(array_column($rows,'laki_laki'));
    $subP = array_sum(array_column($rows,'perempuan'));
?>
<div class="card">
  <div class="card-head">
    <div>
      <h2>Kelas <?= $tingkat ?></h2>
      <div style="font-size:12px;color:var(--muted);margin-top:2px;">
        <?= count($rows) ?> rombel &nbsp;·&nbsp; Total: <strong><?= $subL+$subP ?></strong> siswa
        &nbsp;(L: <?= $subL ?> / P: <?= $subP ?>)
      </div>
    </div>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Kelas</th><th>Wali Kelas</th>
        <th style="text-align:center">👦 L</th>
        <th style="text-align:center">👧 P</th>
        <th style="text-align:center">Total</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $k): ?>
      <tr>
        <td>
          <span style="display:inline-flex;align-items:center;justify-content:center;
                       width:36px;height:36px;border-radius:8px;
                       background:var(--red);color:white;font-weight:700;font-size:14px;">
            <?= htmlspecialchars($k['kelas']) ?>
          </span>
        </td>
        <td style="font-size:13px;color:#444;">
          <?= $k['wali_kelas'] ? htmlspecialchars($k['wali_kelas']) : '<span style="color:#ccc">—</span>' ?>
        </td>
        <td style="text-align:center;font-weight:600;color:#1565C0;"><?= $k['laki_laki'] ?></td>
        <td style="text-align:center;font-weight:600;color:#880E4F;"><?= $k['perempuan'] ?></td>
        <td style="text-align:center;font-weight:700;font-size:16px;"><?= $k['laki_laki']+$k['perempuan'] ?></td>
        <td>
          <div class="td-actions">
            <a href="siswa.php?edit=<?= $k['id'] ?>" class="btn btn-outline btn-sm">✏️</a>
            <a href="siswa.php?delete=<?= $k['id'] ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Hapus kelas <?= htmlspecialchars(addslashes($k['kelas'])) ?>?')">🗑️</a>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr style="background:#fafafa;">
        <td colspan="2" style="padding:10px 16px;font-size:11px;font-weight:700;color:var(--muted);letter-spacing:.5px;text-transform:uppercase;">
          SUBTOTAL
        </td>
        <td style="text-align:center;font-weight:700;color:#1565C0;padding:10px 16px;"><?= $subL ?></td>
        <td style="text-align:center;font-weight:700;color:#880E4F;padding:10px 16px;"><?= $subP ?></td>
        <td style="text-align:center;font-weight:700;font-size:16px;padding:10px 16px;"><?= $subL+$subP ?></td>
        <td></td>
      </tr>
    </tfoot>
  </table>
</div>
<?php endforeach; ?>
</div>

<!-- Grand Total -->
<div class="card" style="margin-top:16px;background:linear-gradient(135deg,var(--dark2),var(--dark3));color:white;">
  <div class="card-body" style="display:flex;align-items:center;gap:32px;justify-content:center;padding:28px;flex-wrap:wrap;">
    <div style="text-align:center;">
      <div style="font-size:11px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:1px;">Total Siswa</div>
      <div style="font-family:'Lora',serif;font-size:44px;font-weight:700;color:var(--gold);"><?= $totalS ?></div>
      <div style="font-size:11px;color:rgba(255,255,255,.3);">Angka tampil di halaman utama</div>
    </div>
    <div style="width:1px;height:60px;background:rgba(255,255,255,.1);"></div>
    <div style="text-align:center;">
      <div style="font-size:11px;color:rgba(255,255,255,.4);">Laki-laki</div>
      <div style="font-size:30px;font-weight:700;color:#90CAF9;"><?= $totalL ?></div>
    </div>
    <div style="text-align:center;">
      <div style="font-size:11px;color:rgba(255,255,255,.4);">Perempuan</div>
      <div style="font-size:30px;font-weight:700;color:#F48FB1;"><?= $totalP ?></div>
    </div>
    <div style="width:1px;height:60px;background:rgba(255,255,255,.1);"></div>
    <div style="text-align:center;">
      <div style="font-size:11px;color:rgba(255,255,255,.4);">Rombel</div>
      <div style="font-size:30px;font-weight:700;color:rgba(255,255,255,.8);"><?= count($list) ?></div>
    </div>
    <div style="text-align:center;">
      <div style="font-size:11px;color:rgba(255,255,255,.4);">Tahun Ajaran</div>
      <div style="font-size:18px;font-weight:700;color:rgba(255,255,255,.8);"><?= htmlspecialchars($tahunAktif) ?></div>
    </div>
  </div>
</div>

<?php else: ?>
<div class="empty-state">
  <div>🎒</div>
  <p>Belum ada data siswa untuk TA <strong><?= htmlspecialchars($tahunAktif) ?></strong>.</p>
  <a href="siswa.php?action=new" class="btn btn-primary" style="margin-top:12px;">➕ Tambah Data Kelas</a>
</div>
<?php endif; ?>
<?php endif; ?>

<?php require_once 'layout_end.php'; ?>
