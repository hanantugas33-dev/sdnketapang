<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ganti dengan credentials sesuai kebutuhan
    $validUser = 'admin';
    $validPass = 'sdnketapang2025'; // WAJIB diganti!

    if ($_POST['username'] === $validUser && $_POST['password'] === $validPass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $validUser;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login Admin — SDN Ketapang</title>
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--red:#B71C1C;--red2:#D32F2F;--gold:#C8981F;--dark:#111418;--dark2:#1C2128;}
body{font-family:'DM Sans',sans-serif;background:var(--dark);min-height:100vh;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
.bg-pattern{position:absolute;inset:0;background:radial-gradient(ellipse at 20% 50%,rgba(183,28,28,.15) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(200,152,31,.08) 0%,transparent 50%);}
.login-card{position:relative;z-index:2;background:var(--dark2);border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:48px 40px;width:100%;max-width:400px;box-shadow:0 24px 64px rgba(0,0,0,.5);}
.login-logo{width:60px;height:60px;background:var(--red);border-radius:14px;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;text-align:center;line-height:1.3;margin:0 auto 20px;}
.login-title{font-family:'Lora',serif;font-size:22px;color:white;text-align:center;margin-bottom:4px;}
.login-sub{font-size:13px;color:rgba(255,255,255,.4);text-align:center;margin-bottom:32px;}
.form-group{margin-bottom:18px;}
.form-group label{display:block;font-size:11px;font-weight:600;color:rgba(255,255,255,.5);letter-spacing:.5px;text-transform:uppercase;margin-bottom:8px;}
.form-group input{width:100%;padding:12px 16px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;color:white;font-size:14px;font-family:'DM Sans',sans-serif;outline:none;transition:.2s;}
.form-group input:focus{border-color:var(--gold);}
.form-group input::placeholder{color:rgba(255,255,255,.25);}
.btn-login{width:100%;padding:13px;background:var(--red2);color:white;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:.2s;font-family:'DM Sans',sans-serif;margin-top:8px;}
.btn-login:hover{background:#c62828;}
.error{background:rgba(183,28,28,.2);border:1px solid rgba(211,47,47,.3);color:#EF9A9A;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;text-align:center;}
.back-link{display:block;text-align:center;margin-top:20px;font-size:13px;color:rgba(255,255,255,.35);transition:.2s;}
.back-link:hover{color:rgba(255,255,255,.6);}
</style>
</head>
<body>
<div class="bg-pattern"></div>
<div class="login-card">
  <div class="login-logo">SDN<br>KTP</div>
  <div class="login-title">Admin Panel</div>
  <div class="login-sub">SDN Ketapang — Masuk untuk mengelola website</div>
  <?php if ($error): ?>
  <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" placeholder="Masukkan username" required autofocus/>
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Masukkan password" required/>
    </div>
    <button type="submit" class="btn-login">Masuk →</button>
  </form>
  <a href="../index.html" class="back-link">← Kembali ke website</a>
</div>
</body>
</html>
