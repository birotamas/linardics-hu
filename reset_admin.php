<?php
/**
 * Admin jelszó visszaállító – TÖRÖLD A FUTTATÁS UTÁN!
 */

$config = __DIR__ . '/cms/config.php';
if (!file_exists($config)) {
    die('<p style="color:red;font-family:monospace">Hiba: cms/config.php nem létezik. Futtasd először a setup.php-t.</p>');
}
require_once $config;

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $name  = trim($_POST['name'] ?? 'Admin');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Érvényes e-mail cím szükséges.';
    } elseif (strlen($pass) < 6) {
        $message = 'A jelszó legalább 6 karakter legyen.';
    } else {
        try {
            $pdo  = cms_db();
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            // Létező user frissítése, vagy új létrehozása
            $existing = $pdo->prepare("SELECT id FROM admin_users WHERE email = ?");
            $existing->execute([$email]);

            if ($existing->fetch()) {
                $pdo->prepare("UPDATE admin_users SET password = ?, name = ? WHERE email = ?")
                    ->execute([$hash, $name, $email]);
                $message = "Jelszó frissítve: {$email}";
            } else {
                $pdo->prepare("INSERT INTO admin_users (name, email, password) VALUES (?, ?, ?)")
                    ->execute([$name, $email, $hash]);
                $message = "Admin létrehozva: {$email}";
            }
            $success = true;

            // DB tartalom ellenőrzés
            $users = $pdo->query("SELECT id, name, email, LEFT(password,20) as pass_preview FROM admin_users")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $message = 'DB hiba: ' . $e->getMessage();
        }
    }
}

// DB teszt
$db_ok = false;
$db_error = '';
try {
    $pdo  = cms_db();
    $db_ok = true;
    $users = $pdo->query("SELECT id, name, email FROM admin_users")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $db_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>Admin visszaállítás</title>
  <style>
    body { font-family: monospace; background: #060f1a; color: #fff; padding: 2rem; max-width: 500px; margin: 0 auto; }
    h1 { color: #cc2222; font-size: 1.2rem; margin-bottom: 1.5rem; }
    label { display: block; font-size: .75rem; color: rgba(255,255,255,.4); text-transform: uppercase; letter-spacing: .1em; margin-bottom: .25rem; margin-top: 1rem; }
    input { width: 100%; background: #0d1b2a; border: 1px solid rgba(255,255,255,.15); color: #fff; padding: .6rem .8rem; font-size: .9rem; box-sizing: border-box; }
    button { margin-top: 1.5rem; width: 100%; background: #cc2222; color: #fff; border: none; padding: .75rem; font-size: 1rem; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: .1em; }
    .ok  { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.3); color: #86efac; padding: .75rem 1rem; margin-bottom: 1rem; }
    .err { background: rgba(204,34,34,.1); border: 1px solid rgba(204,34,34,.3); color: #fca5a5; padding: .75rem 1rem; margin-bottom: 1rem; }
    .info { background: #0d1b2a; border: 1px solid rgba(255,255,255,.1); padding: .75rem 1rem; margin-top: 1.5rem; font-size: .8rem; color: rgba(255,255,255,.6); }
    a { color: #cc2222; }
    .warn { color: #fbbf24; font-size: .75rem; margin-top: 1.5rem; }
  </style>
</head>
<body>

<h1>⚠ Admin visszaállító</h1>

<!-- DB státusz -->
<?php if (!$db_ok): ?>
<div class="err">DB kapcsolat sikertelen: <?= htmlspecialchars($db_error) ?></div>
<?php else: ?>
<div class="ok">DB kapcsolat: OK</div>
<?php endif; ?>

<!-- Meglévő userek -->
<?php if ($db_ok && !empty($users)): ?>
<div class="info">
  Meglévő admin userek:<br><br>
  <?php foreach ($users as $u): ?>
  &nbsp;· <?= htmlspecialchars($u['email']) ?> (<?= htmlspecialchars($u['name']) ?>)<br>
  <?php endforeach; ?>
</div>
<?php elseif ($db_ok): ?>
<div class="info">Nincs még admin user az adatbázisban.</div>
<?php endif; ?>

<!-- Eredmény -->
<?php if ($message): ?>
<div class="<?= $success ? 'ok' : 'err' ?>" style="margin-top:1rem"><?= htmlspecialchars($message) ?></div>
<?php if ($success): ?>
<p style="margin-top:1rem">→ <a href="admin/login.php">Bejelentkezés az admin panelre</a></p>
<?php endif; ?>
<?php endif; ?>

<!-- Form -->
<?php if (!$success): ?>
<form method="POST">
  <label>Név</label>
  <input type="text" name="name" value="Admin">
  <label>E-mail *</label>
  <input type="email" name="email" value="admin@linardics.hu" required>
  <label>Új jelszó * (min. 6 karakter)</label>
  <input type="password" name="password" required placeholder="••••••••">
  <button type="submit">Jelszó beállítása</button>
</form>
<?php endif; ?>

<p class="warn">⚠ Töröld ezt a fájlt (reset_admin.php) a használat után!</p>

</body>
</html>
