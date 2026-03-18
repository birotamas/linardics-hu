<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

$pdo = cms_db();

$total    = $pdo->query("SELECT COUNT(*) FROM machines")->fetchColumn();
$active   = $pdo->query("SELECT COUNT(*) FROM machines WHERE is_active=1")->fetchColumn();
$trulaser = $pdo->query("SELECT COUNT(*) FROM machines WHERE section='trulaser'")->fetchColumn();
$hajlito  = $pdo->query("SELECT COUNT(*) FROM machines WHERE section IN ('trubend','amada')")->fetchColumn();

$page_title = 'Vezérlőpult';
include __DIR__ . '/includes/header.php';
?>

<style>
  @keyframes slideUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .anim { opacity: 0; animation: slideUp 0.38s cubic-bezier(0.22,1,0.36,1) forwards; }

  .stat-card { position: relative; overflow: hidden; }
  .stat-card::after {
    content: '';
    position: absolute; top: 0; left: -100%;
    width: 60%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.03), transparent);
    animation: shimmer 3.5s ease-in-out infinite;
  }
  @keyframes shimmer {
    0%   { left: -60%; }
    50%  { left: 130%; }
    100% { left: 130%; }
  }

  .quick-link { transition: border-color 0.2s, background 0.2s, transform 0.2s; }
  .quick-link:hover { transform: translateY(-2px); }
  .quick-link-icon svg { transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1); }
  .quick-link:hover .quick-link-icon svg { transform: scale(1.2); }
</style>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;">
  <?php
  $stats = [
    ['Összes gép',  $total,    '#f87171', 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18'],
    ['Aktív',       $active,   '#86efac', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['Lézervágók',  $trulaser, '#93c5fd', 'M13 10V3L4 14h7v7l9-11h-7z'],
    ['Hajlítók',    $hajlito,  '#fcd34d', 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
  ];
  foreach ($stats as $i => [$label, $val, $color, $icon]): ?>
  <div class="stat-card anim" style="animation-delay:<?= $i * 80 ?>ms;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
      <span class="stat-label"><?= $label ?></span>
      <svg style="width:16px;height:16px;color:<?= $color ?>;opacity:0.7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?= $icon ?>"/></svg>
    </div>
    <div class="stat-value" style="color:<?= $color ?>" data-target="<?= $val ?>">0</div>
  </div>
  <?php endforeach; ?>
</div>

<div class="section-label anim" style="animation-delay:360ms;">Gyors műveletek</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">

  <a href="machines.php" class="quick-link anim" style="animation-delay:420ms;">
    <div class="quick-link-icon">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
    </div>
    <div>
      <div class="quick-link-title">Gépek kezelése</div>
      <div class="quick-link-sub">Lista, szerkesztés, sorrendezés</div>
    </div>
  </a>

  <a href="machine-edit.php" class="quick-link anim" style="animation-delay:500ms;">
    <div class="quick-link-icon">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>
    </div>
    <div>
      <div class="quick-link-title">Új gép hozzáadása</div>
      <div class="quick-link-sub">Adatok, specs, kép feltöltés</div>
    </div>
  </a>

  <a href="settings.php" class="quick-link anim" style="animation-delay:580ms;">
    <div class="quick-link-icon">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
    </div>
    <div>
      <div class="quick-link-title">Oldal beállítások</div>
      <div class="quick-link-sub">Cím, szövegek, badge-ek</div>
    </div>
  </a>

</div>

<script>
// Stat counter
document.querySelectorAll('.stat-value[data-target]').forEach((el, i) => {
  const target = parseInt(el.dataset.target, 10);
  if (!target) { el.textContent = '0'; return; }
  const delay = i * 80 + 180;
  setTimeout(() => {
    const duration = 600;
    const start = performance.now();
    const tick = (now) => {
      const p = Math.min((now - start) / duration, 1);
      const ease = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(ease * target);
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }, delay);
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
