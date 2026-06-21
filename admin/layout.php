<?php
// layout.php — dipanggil oleh setiap halaman admin
// $pageTitle dan $pageActive harus di-set sebelum include ini
$pageTitle  = $pageTitle  ?? 'Admin';
$pageActive = $pageActive ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= htmlspecialchars($pageTitle) ?> — Admin SDN Ketapang</title>
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
  --red:#B71C1C;--red2:#D32F2F;--gold:#C8981F;
  --dark:#111418;--dark2:#1C2128;--dark3:#252B34;
  --sidebar-w:240px;
  --white:#F8F6F1;--cream:#F2EDE4;--muted:#6B7280;
  --green:#1B5E20;
  --radius:12px;--shadow:0 4px 20px rgba(0,0,0,.08);
}
html,body{height:100%;font-family:'DM Sans',sans-serif;background:#F4F5F7;color:#1a1a2e;}
a{text-decoration:none;color:inherit;}
img{max-width:100%;}
button{cursor:pointer;}

/* LAYOUT */
.admin-wrap{display:flex;min-height:100vh;}

/* SIDEBAR */
.sidebar{
  width:var(--sidebar-w);flex-shrink:0;
  background:var(--dark2);
  display:flex;flex-direction:column;
  position:fixed;top:0;left:0;bottom:0;
  z-index:100;overflow-y:auto;
}
.sidebar-brand{
  padding:24px 20px 20px;
  border-bottom:1px solid rgba(255,255,255,.06);
}
.brand-logo{
  width:42px;height:42px;background:var(--red);
  border-radius:10px;display:inline-flex;align-items:center;
  justify-content:center;color:white;font-weight:700;font-size:11px;
  text-align:center;line-height:1.3;margin-bottom:10px;
}
.brand-name{font-family:'Lora',serif;font-size:15px;color:white;font-weight:700;}
.brand-sub{font-size:11px;color:rgba(255,255,255,.35);margin-top:2px;}

.sidebar-nav{padding:16px 12px;flex:1;}
.nav-section{font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.25);padding:0 8px;margin:16px 0 8px;}
.nav-item{
  display:flex;align-items:center;gap:10px;
  padding:10px 12px;border-radius:9px;
  font-size:13px;font-weight:500;color:rgba(255,255,255,.55);
  transition:all .2s;margin-bottom:2px;
}
.nav-item:hover{background:rgba(255,255,255,.06);color:white;}
.nav-item.active{background:var(--red);color:white;}
.nav-item .nav-icon{font-size:16px;width:20px;text-align:center;flex-shrink:0;}
.sidebar-footer{
  padding:16px 12px;
  border-top:1px solid rgba(255,255,255,.06);
}
.logout-btn{
  display:flex;align-items:center;gap:10px;
  padding:10px 12px;border-radius:9px;
  font-size:13px;font-weight:500;color:rgba(255,255,255,.4);
  background:none;border:none;width:100%;
  transition:all .2s;
}
.logout-btn:hover{background:rgba(183,28,28,.2);color:#EF9A9A;}

/* MAIN */
.main-content{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}

/* TOPBAR */
.admin-topbar{
  background:white;border-bottom:1px solid #eee;
  padding:0 32px;height:60px;
  display:flex;align-items:center;justify-content:space-between;
  position:sticky;top:0;z-index:50;
}
.topbar-left h1{font-family:'Lora',serif;font-size:18px;color:var(--dark);font-weight:700;}
.topbar-left p{font-size:12px;color:var(--muted);}
.topbar-right{display:flex;align-items:center;gap:12px;}
.view-site-btn{
  display:flex;align-items:center;gap:6px;
  padding:7px 14px;border-radius:8px;
  background:var(--cream);color:var(--dark);
  font-size:12px;font-weight:600;border:none;
  transition:.2s;
}
.view-site-btn:hover{background:#e0d9cf;}
.admin-badge{
  background:var(--red);color:white;
  padding:5px 12px;border-radius:20px;
  font-size:11px;font-weight:600;
}

/* PAGE CONTENT */
.page-body{padding:28px 32px;flex:1;}

/* CARDS */
.card{background:white;border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.card-head{
  padding:18px 24px;border-bottom:1px solid #f0f0f0;
  display:flex;align-items:center;justify-content:space-between;
}
.card-head h2{font-family:'Lora',serif;font-size:16px;font-weight:700;color:var(--dark);}
.card-body{padding:24px;}

/* BUTTONS */
.btn{padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;border:none;transition:.2s;display:inline-flex;align-items:center;gap:6px;font-family:'DM Sans',sans-serif;}
.btn-primary{background:var(--red2);color:white;}
.btn-primary:hover{background:#c62828;}
.btn-success{background:#2E7D32;color:white;}
.btn-success:hover{background:#1B5E20;}
.btn-outline{background:white;color:var(--dark);border:1px solid #ddd;}
.btn-outline:hover{border-color:var(--red);color:var(--red);}
.btn-danger{background:#fff;color:#c62828;border:1px solid #ffcdd2;}
.btn-danger:hover{background:#ffebee;}
.btn-sm{padding:6px 12px;font-size:12px;}
.btn-gold{background:var(--gold);color:white;}
.btn-gold:hover{background:#a37a15;}

/* FORMS */
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.form-group{margin-bottom:18px;}
.form-group label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:6px;}
.form-group input,.form-group select,.form-group textarea{
  width:100%;padding:10px 14px;
  border:1px solid #ddd;border-radius:8px;
  font-size:14px;font-family:'DM Sans',sans-serif;
  color:var(--dark);background:white;
  outline:none;transition:.2s;
}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--red);}
.form-group textarea{resize:vertical;min-height:100px;}
.form-group.full{grid-column:1/-1;}
.form-hint{font-size:11px;color:var(--muted);margin-top:4px;}

/* TABLE */
.data-table{width:100%;border-collapse:collapse;}
.data-table th{font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--muted);padding:10px 16px;text-align:left;border-bottom:2px solid #f0f0f0;}
.data-table td{padding:12px 16px;border-bottom:1px solid #f8f8f8;font-size:13px;vertical-align:middle;}
.data-table tr:last-child td{border-bottom:none;}
.data-table tr:hover td{background:#fafafa;}
.td-actions{display:flex;gap:6px;}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-berita{background:#FFF3E0;color:#E65100;}
.badge-pengumuman{background:#E8F5E9;color:#1B5E20;}
.badge-kegiatan{background:#E3F2FD;color:#0D47A1;}
.badge-publish{background:#E8F5E9;color:#1B5E20;}
.badge-draft{background:#f5f5f5;color:#999;}
.badge-aktif{background:#E8F5E9;color:#1B5E20;}
.badge-nonaktif{background:#f5f5f5;color:#999;}

/* AVATAR */
.avatar{width:36px;height:36px;border-radius:8px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;font-size:18px;overflow:hidden;}
.avatar img{width:100%;height:100%;object-fit:cover;}

/* ALERTS */
.alert{padding:12px 18px;border-radius:8px;font-size:13px;margin-bottom:20px;}
.alert-success{background:#E8F5E9;border:1px solid #C8E6C9;color:#1B5E20;}
.alert-error{background:#FFEBEE;border:1px solid #FFCDD2;color:#c62828;}

/* MODAL */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;padding:20px;}
.modal-overlay.open{display:flex;}
.modal{background:white;border-radius:16px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;}
.modal-head{padding:20px 24px;border-bottom:1px solid #eee;display:flex;align-items:center;justify-content:space-between;}
.modal-head h3{font-family:'Lora',serif;font-size:17px;}
.modal-close{background:none;border:none;font-size:18px;color:#999;cursor:pointer;padding:4px;}
.modal-close:hover{color:var(--red);}
.modal-body{padding:24px;}
.modal-footer{padding:16px 24px;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:10px;}

/* STATS GRID */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;}
.stat-box{background:white;border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);border-left:4px solid var(--red);}
.stat-box.gold{border-color:var(--gold);}
.stat-box.green{border-color:var(--green);}
.stat-box.blue{border-color:#1565C0;}
.stat-num{font-family:'Lora',serif;font-size:28px;font-weight:700;color:var(--dark);}
.stat-lbl{font-size:12px;color:var(--muted);margin-top:2px;}

/* TABS */
.page-tabs{display:flex;gap:4px;margin-bottom:24px;background:white;padding:6px;border-radius:10px;box-shadow:var(--shadow);width:fit-content;}
.page-tab{padding:8px 18px;border-radius:7px;font-size:13px;font-weight:600;border:none;background:none;color:var(--muted);transition:.2s;}
.page-tab.active{background:var(--red);color:white;}
.page-tab:hover:not(.active){background:#f5f5f5;}

/* PHOTO UPLOAD */
.photo-upload{border:2px dashed #ddd;border-radius:10px;padding:24px;text-align:center;cursor:pointer;transition:.2s;}
.photo-upload:hover{border-color:var(--red);background:#fff5f5;}
.photo-upload input{display:none;}
.photo-preview{width:100px;height:100px;border-radius:10px;object-fit:cover;margin:0 auto 8px;display:block;}

/* RESPONSIVE */
@media(max-width:768px){
  .sidebar{transform:translateX(-100%);transition:.3s;}
  .sidebar.open{transform:translateX(0);}
  .main-content{margin-left:0;}
  .stats-grid{grid-template-columns:1fr 1fr;}
  .form-row{grid-template-columns:1fr;}
}

/* LOADING */
.spinner{width:20px;height:20px;border:2px solid #f3f3f3;border-top:2px solid var(--red);border-radius:50%;animation:spin .6s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}
.btn-loading{display:flex;align-items:center;gap:8px;}

/* EMPTY STATE */
.empty-state{text-align:center;padding:48px;color:var(--muted);}
.empty-state div{font-size:40px;margin-bottom:12px;}
.empty-state p{font-size:14px;}
</style>
</head>
<body>
<div class="admin-wrap">

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo">SDN<br>KTP</div>
    <div class="brand-name">SDN Ketapang</div>
    <div class="brand-sub">Panel Admin</div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Dashboard</div>
    <a href="index.php" class="nav-item <?= $pageActive==='dashboard'?'active':'' ?>">
      <span class="nav-icon">🏠</span> Dashboard
    </a>
    <div class="nav-section">Konten</div>
    <a href="berita.php" class="nav-item <?= $pageActive==='berita'?'active':'' ?>">
      <span class="nav-icon">📰</span> Berita & Pengumuman
    </a>
    <a href="galeri.php" class="nav-item <?= $pageActive==='galeri'?'active':'' ?>">
      <span class="nav-icon">🖼️</span> Galeri Foto
    </a>
    <div class="nav-section">Sekolah</div>
    <a href="profil.php" class="nav-item <?= $pageActive==='profil'?'active':'' ?>">
      <span class="nav-icon">🏫</span> Profil Sekolah
    </a>
    <a href="guru.php" class="nav-item <?= $pageActive==='guru'?'active':'' ?>">
      <span class="nav-icon">👨‍🏫</span> Guru & Staf
    </a>
    <a href="fasilitas.php" class="nav-item <?= $pageActive==='fasilitas'?'active':'' ?>">
      <span class="nav-icon">🏛️</span> Fasilitas & Ekskul
    </a>
    <a href="siswa.php" class="nav-item <?= $pageActive==='siswa'?'active':'' ?>">
      <span class="nav-icon">🎒</span> Data Siswa
    </a>
    <div class="nav-section">Pesan</div>
    <a href="buku_tamu.php" class="nav-item <?= $pageActive==='buku_tamu'?'active':'' ?>">
      <span class="nav-icon">✉️</span> Buku Tamu
      <?php
      // Tampilkan badge unread
      try {
        $db = getDB();
        $unread = $db->query("SELECT COUNT(*) FROM buku_tamu WHERE status='belum_dibaca'")->fetchColumn();
        if ($unread > 0) echo "<span style='background:var(--gold);color:white;border-radius:10px;padding:1px 7px;font-size:10px;font-weight:700;margin-left:auto'>$unread</span>";
      } catch(Exception $e) {}
      ?>
    </a>
  </nav>
  <div class="sidebar-footer">
    <form method="POST" action="logout.php">
      <button type="submit" class="logout-btn">
        <span class="nav-icon">🚪</span> Keluar
      </button>
    </form>
  </div>
</aside>

<!-- MAIN -->
<div class="main-content">
  <header class="admin-topbar">
    <div class="topbar-left">
      <h1><?= htmlspecialchars($pageTitle) ?></h1>
    </div>
    <div class="topbar-right">
      <a href="../index.html" target="_blank" class="view-site-btn">🌐 Lihat Website</a>
      <div class="admin-badge">👤 <?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></div>
    </div>
  </header>
  <div class="page-body">
