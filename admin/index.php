<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

$pdo = cms_db();

$total       = $pdo->query("SELECT COUNT(*) FROM machines")->fetchColumn();
$active      = $pdo->query("SELECT COUNT(*) FROM machines WHERE is_active=1")->fetchColumn();
$trulaser    = $pdo->query("SELECT COUNT(*) FROM machines WHERE section='trulaser'")->fetchColumn();
$hajlito     = $pdo->query("SELECT COUNT(*) FROM machines WHERE section IN ('trubend','amada')")->fetchColumn();

$page_title = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
  <?php
  $stats = [
    ['Összes gép',    $total,    '#cc2222'],
    ['Aktív gép',     $active,   '#22c55e'],
    ['Lézervágók',    $trulaser, '#3b82f6'],
    ['Hajlítók',      $hajlito,  '#f59e0b'],
  ];
  foreach ($stats as [$label, $val, $color]): ?>
  <div class="bg-[#0d1b2a] border border-white/8 p-5">
    <div class="text-3xl font-heading font-bold" style="color:<?= $color ?>"><?= $val ?></div>
    <div class="text-xs text-white/40 uppercase tracking-widest mt-1"><?= $label ?></div>
  </div>
  <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <a href="machines.php" class="bg-[#0d1b2a] border border-white/8 hover:border-[#cc2222]/40 p-5 flex items-center gap-4 transition-colors group">
    <div class="w-10 h-10 bg-[#cc2222]/10 border border-[#cc2222]/20 flex items-center justify-center shrink-0">
      <svg class="w-5 h-5 text-[#cc2222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
    </div>
    <div>
      <div class="font-semibold text-sm text-white group-hover:text-[#cc2222] transition-colors">Gépek kezelése</div>
      <div class="text-xs text-white/35 mt-0.5">CRUD + sorrendezés</div>
    </div>
  </a>
  <a href="settings.php" class="bg-[#0d1b2a] border border-white/8 hover:border-[#cc2222]/40 p-5 flex items-center gap-4 transition-colors group">
    <div class="w-10 h-10 bg-[#cc2222]/10 border border-[#cc2222]/20 flex items-center justify-center shrink-0">
      <svg class="w-5 h-5 text-[#cc2222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
    </div>
    <div>
      <div class="font-semibold text-sm text-white group-hover:text-[#cc2222] transition-colors">Oldal beállítások</div>
      <div class="text-xs text-white/35 mt-0.5">Cím, bevezető, badge-ek</div>
    </div>
  </a>
  <a href="../geppark.php" target="_blank" class="bg-[#0d1b2a] border border-white/8 hover:border-[#cc2222]/40 p-5 flex items-center gap-4 transition-colors group">
    <div class="w-10 h-10 bg-[#cc2222]/10 border border-[#cc2222]/20 flex items-center justify-center shrink-0">
      <svg class="w-5 h-5 text-[#cc2222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
    </div>
    <div>
      <div class="font-semibold text-sm text-white group-hover:text-[#cc2222] transition-colors">Előnézet megtekintése</div>
      <div class="text-xs text-white/35 mt-0.5">geppark.php új tabban</div>
    </div>
  </a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
