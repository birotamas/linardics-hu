<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$config = dirname(__DIR__) . '/cms/config.php';
if (!file_exists($config)) { header('Location: ../setup.php'); exit; }
require_once $config;

if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email && $pass) {
        $stmt = cms_db()->prepare("SELECT id, name, email, password FROM admin_users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']    = $user['id'];
            $_SESSION['admin_name']  = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Hibás e-mail cím vagy jelszó.';
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bejelentkezés – Linardics CMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300..700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg: #080d16; --surface: #0e1623; --elevated: #151f30;
      --border: rgba(255,255,255,0.08); --border-md: rgba(255,255,255,0.13);
      --red: #dc2626; --red-dim: rgba(220,38,38,0.1); --red-border: rgba(220,38,38,0.22);
      --text: #f1f5f9; --muted: rgba(241,245,249,0.5); --subtle: rgba(241,245,249,0.28);
    }
    html { font-family: 'Inter', system-ui, sans-serif; font-size: 14px; }
    body {
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    /* Subtle grid background */
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0;
      background-image:
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
      background-size: 48px 48px;
      pointer-events: none;
    }

    .login-wrap { position: relative; z-index: 1; width: 100%; max-width: 380px; }

    .login-header { text-align: center; margin-bottom: 32px; }
    .login-logo {
      display: inline-flex; align-items: center; justify-content: center;
      width: 52px; height: 52px;
      background: var(--red-dim);
      border: 1px solid var(--red-border);
      border-radius: 14px;
      margin-bottom: 18px;
    }
    .login-logo img { width: 30px; height: 30px; object-fit: contain; filter: brightness(0) invert(1); }
    .login-title { font-size: 22px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .login-sub   { font-size: 13px; color: var(--muted); }

    .login-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 28px;
    }

    .form-group { margin-bottom: 16px; }
    label {
      display: block;
      font-size: 12px; font-weight: 500;
      color: var(--muted);
      margin-bottom: 6px;
    }
    input {
      width: 100%;
      background: var(--elevated);
      border: 1px solid var(--border-md);
      border-radius: 8px;
      color: var(--text);
      padding: 10px 13px;
      font-size: 14px;
      font-family: inherit;
      outline: none;
      transition: border-color 0.15s, box-shadow 0.15s;
    }
    input:focus {
      border-color: var(--red);
      box-shadow: 0 0 0 3px rgba(220,38,38,0.12);
    }
    input::placeholder { color: var(--subtle); }

    .error-box {
      display: flex; align-items: center; gap: 8px;
      background: rgba(220,38,38,0.08);
      border: 1px solid rgba(220,38,38,0.2);
      border-radius: 8px;
      padding: 10px 13px;
      margin-bottom: 18px;
      font-size: 13px;
      color: #f87171;
    }
    .error-box svg { width: 15px; height: 15px; flex-shrink: 0; }

    .submit-btn {
      width: 100%; margin-top: 8px;
      background: var(--red);
      color: #fff;
      border: none; border-radius: 8px;
      padding: 11px;
      font-size: 14px; font-weight: 600;
      font-family: inherit;
      cursor: pointer;
      transition: background 0.15s, box-shadow 0.15s;
    }
    .submit-btn:hover { background: #b91c1c; box-shadow: 0 4px 16px rgba(220,38,38,0.3); }

    .login-footer { text-align: center; margin-top: 20px; font-size: 11px; color: var(--subtle); }
  </style>
</head>
<body>

<div class="login-wrap">
  <div class="login-header">
    <div class="login-logo">
      <img src="../assets/images/linardics-logo.png" alt="Linardics">
    </div>
    <div class="login-title">Linardics CMS</div>
    <div class="login-sub">Adminisztrációs felület</div>
  </div>

  <div class="login-card">
    <?php if ($error): ?>
    <div class="error-box">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="email">E-mail cím</label>
        <input type="email" id="email" name="email"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          placeholder="admin@linardics.hu"
          required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Jelszó</label>
        <input type="password" id="password" name="password"
          placeholder="••••••••"
          required>
      </div>
      <button type="submit" class="submit-btn">Bejelentkezés</button>
    </form>
  </div>

  <div class="login-footer">Linardics Kft. · Géppark CMS v1.0</div>
</div>

</body>
</html>
