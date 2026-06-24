<?php
$pageTitle  = 'Buku Tamu';
$pageActive = 'buku_tamu';
require_once 'auth.php';

$db  = getDB();
$msg = '';

// DELETE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $db->prepare("DELETE FROM buku_tamu WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: buku_tamu.php?msg=deleted'); exit;
}

// MARK READ
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $db->prepare("UPDATE buku_tamu SET status='dibaca' WHERE id=?")->execute([(int)$_GET['read']]);
    header('Location: buku_tamu.php?view='.(int)$_GET['read']); exit;
}

// MARK ALL READ
if (isset($_GET['readall'])) {
    $db->query("UPDATE buku_tamu SET status='dibaca' WHERE status='belum_dibaca'");
    header('Location: buku_tamu.php?msg=readall'); exit;
}

// SAVE BALASAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_balasan'])) {
    $id      = (int)$_POST['id'];
    $balasan = trim($_POST['balasan']);
    $db->prepare("UPDATE buku_tamu SET balasan=?,status='dibalas' WHERE id=?")->execute([$balasan,$id]);
    header('Location: buku_tamu.php?view='.$id.'&msg=replied'); exit;
}

// View detail
$viewId = isset($_GET['view']) && is_numeric($_GET['view']) ? (int)$_GET['view'] : null;
$detail = null;
if ($viewId) {
    $s = $db->prepare("SELECT * FROM buku_tamu WHERE id=?");
    $s->execute([$viewId]);
    $detail = $s->fetch();
    // Auto mark read
    if ($detail && $detail['status'] === 'belum_dibaca') {
        $db->prepare("UPDATE buku_tamu SET status='dibaca' WHERE id=?")->execute([$viewId]);
        $detail['status'] = 'dibaca';
    }
}

$filter = $_GET['filter'] ?? 'semua';
if ($filter !== 'semua') {
    $stmt = $db->prepare("SELECT * FROM buku_tamu WHERE status=? ORDER BY created_at DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $db->query("SELECT * FROM buku_tamu ORDER BY created_at DESC");
}
$list = $stmt->fetchAll();

$totalUnread = $db->query("SELECT COUNT(*) FROM buku_tamu WHERE status='belum_dibaca'")->fetchColumn();

if (isset($_GET['msg'])) {
    $msgs = ['deleted'=>'🗑️ Pesan dihapus.','replied'=>'✅ Balasan disimpan!','readall'=>'✅ Semua pesan ditandai sudah dibaca.'];
    $msg = $msgs[$_GET['msg']] ?? '';
}

require_once 'layout.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

<?php if ($detail): ?>
<!-- DETAIL VIEW -->
<div class="card" style="max-width:700px;">
  <div class="card-head">
    <h2>Detail Pesan</h2>
    <a href="buku_tamu.php" class="btn btn-outline btn-sm">← Kembali ke Daftar</a>
  </div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;padding:20px;background:#f9f9f9;border-radius:10px;">
      <div><div style="font-size:11px;color:#999;text-transform:uppercase;letter-spacing:.5px">Nama</div><div style="font-weight:600;margin-top:3px"><?= htmlspecialchars($detail['nama']) ?></div></div>
      <div><div style="font-size:11px;color:#999;text-transform:uppercase;letter-spacing:.5px">Keperluan</div><div style="font-weight:600;margin-top:3px"><?= htmlspecialchars($detail['keperluan']) ?></div></div>
      <div><div style="font-size:11px;color:#999;text-transform:uppercase;letter-spacing:.5px">Email</div><div style="margin-top:3px"><?= $detail['email'] ? "<a href='mailto:{$detail['email']}' style='color:var(--red)'>{$detail['email']}</a>" : '—' ?></div></div>
      <div><div style="font-size:11px;color:#999;text-transform:uppercase;letter-spacing:.5px">WhatsApp / HP</div><div style="margin-top:3px"><?= $detail['hp'] ? "<a href='https://wa.me/62".ltrim($detail['hp'],'0')."' target='_blank' style='color:var(--red)'>{$detail['hp']}</a>" : '—' ?></div></div>
      <div><div style="font-size:11px;color:#999;text-transform:uppercase;letter-spacing:.5px">Status</div>
        <div style="margin-top:4px"><span class="badge <?= $detail['status']==='dibalas'?'badge-aktif':($detail['status']==='dibaca'?'badge-pengumuman':'badge-draft') ?>"><?= ucfirst(str_replace('_',' ',$detail['status'])) ?></span></div>
      </div>
      <div><div style="font-size:11px;color:#999;text-transform:uppercase;letter-spacing:.5px">Waktu Kirim</div><div style="margin-top:3px;font-size:13px"><?= date('d M Y, H:i', strtotime($detail['created_at'])) ?> WIB</div></div>
    </div>

    <div style="margin-bottom:24px;">
      <div style="font-size:12px;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Pesan:</div>
      <div style="background:#fff;border:1px solid #eee;border-radius:10px;padding:16px;font-size:14px;line-height:1.8;color:#333;white-space:pre-wrap"><?= htmlspecialchars($detail['pesan']) ?></div>
    </div>

    <?php if ($detail['balasan']): ?>
    <div style="margin-bottom:24px;">
      <div style="font-size:12px;font-weight:600;color:#1B5E20;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">✅ Balasan Anda:</div>
      <div style="background:#E8F5E9;border:1px solid #C8E6C9;border-radius:10px;padding:16px;font-size:14px;line-height:1.8;color:#333;white-space:pre-wrap"><?= htmlspecialchars($detail['balasan']) ?></div>
    </div>
    <?php endif; ?>

    <!-- Form Balasan -->
    <form method="POST">
      <input type="hidden" name="save_balasan" value="1"/>
      <input type="hidden" name="id" value="<?= $detail['id'] ?>"/>
      <div class="form-group">
        <label>Tulis Balasan (catatan internal — untuk referensi admin)</label>
        <textarea name="balasan" rows="4" placeholder="Tulis catatan balasan atau tindak lanjut di sini..."><?= htmlspecialchars($detail['balasan']??'') ?></textarea>
      </div>
      <div style="display:flex;gap:10px;justify-content:space-between;">
        <?php if ($detail['email']): ?>
        <a href="mailto:<?= htmlspecialchars($detail['email']) ?>?subject=Re: <?= urlencode($detail['keperluan']) ?>" class="btn btn-outline">📧 Balas via Email</a>
        <?php else: ?><div></div><?php endif; ?>
        <div style="display:flex;gap:8px;">
          <a href="buku_tamu.php?delete=<?= $detail['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus pesan ini?')">🗑️ Hapus</a>
          <button type="submit" class="btn btn-success">💾 Simpan Balasan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php else: ?>
<!-- DAFTAR PESAN -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
  <div style="display:flex;gap:8px;align-items:center;">
    <select onchange="location='buku_tamu.php?filter='+this.value" style="padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;">
      <option value="semua" <?= $filter==='semua'?'selected':'' ?>>Semua Pesan</option>
      <option value="belum_dibaca" <?= $filter==='belum_dibaca'?'selected':'' ?>>Belum Dibaca</option>
      <option value="dibaca" <?= $filter==='dibaca'?'selected':'' ?>>Sudah Dibaca</option>
      <option value="dibalas" <?= $filter==='dibalas'?'selected':'' ?>>Sudah Dibalas</option>
    </select>
    <?php if ($totalUnread > 0): ?>
    <span style="background:var(--red);color:white;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600;"><?= $totalUnread ?> belum dibaca</span>
    <a href="buku_tamu.php?readall=1" class="btn btn-outline btn-sm">✅ Tandai Semua Dibaca</a>
    <?php endif; ?>
  </div>
  <div style="font-size:13px;color:#999"><?= count($list) ?> pesan</div>
</div>

<div class="card">
  <?php if ($list): ?>
  <table class="data-table">
    <thead>
      <tr>
        <th>Status</th>
        <th>Nama Pengirim</th>
        <th>Keperluan</th>
        <th>Email / HP</th>
        <th>Waktu</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($list as $p): ?>
      <tr style="<?= $p['status']==='belum_dibaca'?'background:#fffbf0;font-weight:500':'' ?>">
        <td>
          <span class="badge <?= $p['status']==='dibalas'?'badge-aktif':($p['status']==='dibaca'?'badge-pengumuman':'badge-berita') ?>">
            <?= $p['status']==='belum_dibaca'?'🔴 Baru':($p['status']==='dibaca'?'👁️ Dibaca':'✅ Dibalas') ?>
          </span>
        </td>
        <td><?= htmlspecialchars($p['nama']) ?></td>
        <td style="font-size:12px;color:#555;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($p['keperluan']) ?></td>
        <td style="font-size:12px;color:#777"><?= $p['email'] ?: ($p['hp'] ?: '—') ?></td>
        <td style="font-size:12px;color:#999;white-space:nowrap"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
        <td>
          <div class="td-actions">
            <a href="buku_tamu.php?view=<?= $p['id'] ?>" class="btn btn-outline btn-sm">👁️ Baca</a>
            <a href="buku_tamu.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus pesan ini?')">🗑️</a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <div class="empty-state"><div>✉️</div><p>Belum ada pesan masuk.</p></div>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once 'layout_end.php'; ?>
