<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

$pdo = cms_db();

// Toggle active
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $pdo->prepare("UPDATE machines SET is_active = 1 - is_active WHERE id = ?")->execute([(int)$_GET['toggle']]);
    header('Location: machines.php?toggled=1');
    exit;
}

// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM machines WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: machines.php?deleted=1');
    exit;
}

$machines = $pdo->query("SELECT * FROM machines ORDER BY sort_order, id")->fetchAll();

$toast_msg   = isset($_GET['saved'])   ? 'Gép sikeresen mentve!'   : (isset($_GET['toggled']) ? 'Láthatóság módosítva.' : '');
$toast_msg   = isset($_GET['deleted']) ? 'Gép törölve.'             : $toast_msg;

$page_title = 'Gépek';
include __DIR__ . '/includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
  <p class="text-white/40 text-sm"><?= count($machines) ?> gép összesen</p>
  <a href="machine-edit.php" class="btn-primary">+ Új gép</a>
</div>

<div class="bg-[#0d1b2a] border border-white/8 overflow-hidden">
  <table class="w-full text-sm" id="machines-table">
    <thead>
      <tr class="border-b border-white/8 text-left">
        <th class="px-4 py-3 text-xs text-white/35 uppercase tracking-widest font-medium w-8">#</th>
        <th class="px-4 py-3 text-xs text-white/35 uppercase tracking-widest font-medium">Gép neve</th>
        <th class="px-4 py-3 text-xs text-white/35 uppercase tracking-widest font-medium hidden md:table-cell">Gyártó</th>
        <th class="px-4 py-3 text-xs text-white/35 uppercase tracking-widest font-medium hidden lg:table-cell">Szekció</th>
        <th class="px-4 py-3 text-xs text-white/35 uppercase tracking-widest font-medium text-center">Aktív</th>
        <th class="px-4 py-3 text-xs text-white/35 uppercase tracking-widest font-medium text-right">Műveletek</th>
      </tr>
    </thead>
    <tbody id="sortable-body">
      <?php foreach ($machines as $m): ?>
      <tr class="border-b border-white/5 hover:bg-white/2 transition-colors" data-id="<?= $m['id'] ?>">
        <td class="px-4 py-3 text-white/25 cursor-grab active:cursor-grabbing" title="Húzd a sorrend módosításához">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 6zm0 6a2 2 0 10.001 4.001A2 2 0 007 12zm6-8a2 2 0 10-.001-4.001A2 2 0 0013 4zm0 6a2 2 0 10-.001-4.001A2 2 0 0013 10zm0 6a2 2 0 10-.001-4.001A2 2 0 0013 16z"/></svg>
        </td>
        <td class="px-4 py-3">
          <div class="font-medium text-white"><?= htmlspecialchars($m['name']) ?></div>
          <?php if ($m['badge']): ?>
          <span class="text-[10px] bg-[#cc2222]/10 border border-[#cc2222]/20 text-[#cc2222] px-1.5 py-0.5 font-bold tracking-wider"><?= htmlspecialchars($m['badge']) ?></span>
          <?php endif; ?>
        </td>
        <td class="px-4 py-3 text-white/50 hidden md:table-cell"><?= htmlspecialchars($m['manufacturer']) ?></td>
        <td class="px-4 py-3 hidden lg:table-cell">
          <span class="text-[10px] bg-[#122135] border border-white/10 text-white/50 px-2 py-0.5 uppercase tracking-widest"><?= htmlspecialchars($m['section']) ?></span>
        </td>
        <td class="px-4 py-3 text-center">
          <a href="machines.php?toggle=<?= $m['id'] ?>" class="inline-block w-10 h-5 rounded-full relative transition-colors <?= $m['is_active'] ? 'bg-[#cc2222]' : 'bg-white/15' ?>" title="<?= $m['is_active'] ? 'Inaktiválás' : 'Aktiválás' ?>">
            <span class="absolute top-0.5 w-4 h-4 rounded-full bg-white transition-all <?= $m['is_active'] ? 'left-5' : 'left-0.5' ?>"></span>
          </a>
        </td>
        <td class="px-4 py-3 text-right">
          <div class="flex items-center gap-2 justify-end">
            <a href="machine-edit.php?id=<?= $m['id'] ?>" class="text-xs text-white/50 hover:text-white border border-white/10 hover:border-white/30 px-3 py-1.5 transition-colors">Szerkesztés</a>
            <a href="machines.php?delete=<?= $m['id'] ?>"
               onclick="return confirm('Biztosan törlöd: <?= htmlspecialchars(addslashes($m['name'])) ?>?')"
               class="text-xs text-[#cc2222]/60 hover:text-[#cc2222] border border-[#cc2222]/15 hover:border-[#cc2222]/40 px-3 py-1.5 transition-colors">Törlés</a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
Sortable.create(document.getElementById('sortable-body'), {
  animation: 150,
  handle: 'td:first-child',
  ghostClass: 'opacity-30',
  onEnd: function() {
    const ids = [...document.querySelectorAll('#sortable-body tr[data-id]')].map(r => r.dataset.id);
    fetch('reorder.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ids})
    });
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
