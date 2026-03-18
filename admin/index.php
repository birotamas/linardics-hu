<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

$pdo = cms_db();

$total    = $pdo->query("SELECT COUNT(*) FROM machines")->fetchColumn();
$active   = $pdo->query("SELECT COUNT(*) FROM machines WHERE is_active=1")->fetchColumn();
$trulaser = $pdo->query("SELECT COUNT(*) FROM machines WHERE section='trulaser'")->fetchColumn();
$hajlito  = $pdo->query("SELECT COUNT(*) FROM machines WHERE section IN ('trubend','amada')")->fetchColumn();

$page_title = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;">
  <?php
  $stats = [
    ['Összes gép',  $total,    '#f87171', 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18'],
    ['Aktív',       $active,   '#86efac', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['Lézervágók',  $trulaser, '#93c5fd', 'M13 10V3L4 14h7v7l9-11h-7z'],
    ['Hajlítók',    $hajlito,  '#fcd34d', 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
  ];
  foreach ($stats as [$label, $val, $color, $icon]): ?>
  <div class="stat-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
      <span class="stat-label"><?= $label ?></span>
      <svg style="width:16px;height:16px;color:<?= $color ?>;opacity:0.7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?= $icon ?>"/></svg>
    </div>
    <div class="stat-value" style="color:<?= $color ?>"><?= $val ?></div>
  </div>
  <?php endforeach; ?>
</div>

<div class="section-label">Gyors műveletek</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">

  <a href="machines.php" class="quick-link">
    <div class="quick-link-icon">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
    </div>
    <div>
      <div class="quick-link-title">Gépek kezelése</div>
      <div class="quick-link-sub">Lista, szerkesztés, sorrendezés</div>
    </div>
  </a>

  <a href="machine-edit.php" class="quick-link">
    <div class="quick-link-icon">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>
    </div>
    <div>
      <div class="quick-link-title">Új gép hozzáadása</div>
      <div class="quick-link-sub">Adatok, specs, kép feltöltés</div>
    </div>
  </a>

  <a href="settings.php" class="quick-link">
    <div class="quick-link-icon">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
    </div>
    <div>
      <div class="quick-link-title">Oldal beállítások</div>
      <div class="quick-link-sub">Cím, szövegek, badge-ek</div>
    </div>
  </a>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
