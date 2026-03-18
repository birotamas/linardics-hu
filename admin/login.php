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
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>* { font-family: 'Inter', sans-serif; } .font-heading { font-family: 'Barlow Condensed', sans-serif; }</style>
</head>
<body class="bg-[#060f1a] text-white min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-sm">
  <div class="text-center mb-8">
    <img src="../assets/images/linardics-logo.png" alt="Linardics" class="h-10 w-auto mx-auto mb-4" style="filter:brightness(0) invert(1);">
    <h1 class="font-heading font-bold text-3xl uppercase tracking-widest text-white">CMS Admin</h1>
    <p class="text-white/35 text-sm mt-1">Linardics Kft.</p>
  </div>

  <?php if ($error): ?>
  <div class="bg-red-900/20 border border-red-500/30 px-4 py-3 mb-5 text-red-300 text-sm flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
    <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <form method="POST" class="bg-[#0d1b2a] border border-white/8 p-6 space-y-4">
    <div>
      <label class="block text-xs text-white/40 uppercase tracking-widest mb-1.5">E-mail cím</label>
      <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
        required autofocus
        class="w-full bg-[#060f1a] border border-white/10 focus:border-[#cc2222] outline-none px-4 py-2.5 text-sm text-white"
        placeholder="admin@linardics.hu">
    </div>
    <div>
      <label class="block text-xs text-white/40 uppercase tracking-widest mb-1.5">Jelszó</label>
      <input type="password" name="password" required
        class="w-full bg-[#060f1a] border border-white/10 focus:border-[#cc2222] outline-none px-4 py-2.5 text-sm text-white"
        placeholder="••••••••">
    </div>
    <button type="submit"
      class="w-full bg-[#cc2222] hover:bg-[#a01818] text-white font-heading font-bold text-base uppercase tracking-widest py-3 transition-colors mt-2">
      Bejelentkezés
    </button>
  </form>
</div>

</body>
</html>
