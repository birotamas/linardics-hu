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

// Filters
$search    = trim($_GET['q']       ?? '');
$f_section = trim($_GET['section'] ?? '');
$f_active  = $_GET['active'] ?? '';

$where  = [];
$params = [];

if ($search !== '') {
    $where[]  = '(m.name LIKE ? OR m.manufacturer LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($f_section !== '') {
    $where[]  = 'm.section = ?';
    $params[] = $f_section;
}
if ($f_active !== '') {
    $where[]  = 'm.is_active = ?';
    $params[] = (int)$f_active;
}

$sql = "SELECT * FROM machines m" . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY m.sort_order, m.id";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$machines = $stmt->fetchAll();

$total_all = $pdo->query("SELECT COUNT(*) FROM machines")->fetchColumn();

$toast_msg = isset($_GET['saved'])   ? 'Gép sikeresen mentve!'  :
            (isset($_GET['toggled']) ? 'Láthatóság módosítva.'  :
            (isset($_GET['deleted']) ? 'Gép törölve.'           : ''));

$sections = [
    ''              => 'Minden szekció',
    'trulaser'      => 'TRUMPF Lézervágók',
    'trubend'       => 'TRUMPF Hajlítók',
    'amada'         => 'AMADA Hajlítók',
    'egyeb'         => 'Csőhajlítás + Egyéb',
    'kotestechnika' => 'Kötéstechnika',
];

$page_title = 'Gépek';
include __DIR__ . '/includes/header.php';
?>

<!-- Szűrősáv -->
<form method="GET" class="flex flex-wrap gap-3 mb-6 items-end">
  <div class="flex-1 min-w-48">
    <label class="form-label">Keresés</label>
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
      class="form-input" placeholder="Gép neve, gyártó...">
  </div>
  <div>
    <label class="form-label">Szekció</label>
    <select name="section" class="form-input">
      <?php foreach ($sections as $val => $label): ?>
      <option value="<?= $val ?>" <?= $f_section === $val ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label class="form-label">Állapot</label>
    <select name="active" class="form-input">
      <option value=""  <?= $f_active === ''  ? 'selected' : '' ?>>Mind</option>
      <option value="1" <?= $f_active === '1' ? 'selected' : '' ?>>Aktív</option>
      <option value="0" <?= $f_active === '0' ? 'selected' : '' ?>>Inaktív</option>
    </select>
  </div>
  <div class="flex gap-2">
    <button type="submit" class="btn-primary">Szűrés</button>
    <?php if ($search || $f_section || $f_active !== ''): ?>
    <a href="machines.php" class="btn-secondary">Törlés</a>
    <?php endif; ?>
  </div>
  <div class="ml-auto">
    <a href="machine-edit.php" class="btn-primary">+ Új gép</a>
  </div>
</form>

<!-- Találat számláló -->
<p class="text-white/35 text-xs mb-4">
  <?= count($machines) ?> találat
  <?php if ($search || $f_section || $f_active !== ''): ?>
  <span class="text-white/20">/ <?= $total_all ?> összesen</span>
  <?php endif; ?>
</p>

<div class="bg-[#0d1b2a] border border-white/8 overflow-hidden">
  <?php if (empty($machines)): ?>
  <div class="px-6 py-12 text-center text-white/30 text-sm">
    Nincs találat a megadott feltételekre.
  </div>
  <?php else: ?>
  <table class="w-full text-sm" id="machines-table">
    <thead>
      <tr class="border-b border-white/8 text-left">
        <th class="px-4 py-3 text-xs text-white/35 uppercase tracking-widest font-medium w-8"><?= (!$search && !$f_section && $f_active === '') ? '↕' : '#' ?></th>
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
        <td class="px-4 py-3 text-white/25 <?= (!$search && !$f_section && $f_active === '') ? 'cursor-grab active:cursor-grabbing' : '' ?>" title="<?= (!$search && !$f_section && $f_active === '') ? 'Húzd a sorrend módosításához' : '' ?>">
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
          <a href="machines.php?toggle=<?= $m['id'] ?><?= $search ? '&q='.urlencode($search) : '' ?><?= $f_section ? '&section='.urlencode($f_section) : '' ?><?= $f_active !== '' ? '&active='.$f_active : '' ?>"
             class="inline-block w-10 h-5 rounded-full relative transition-colors <?= $m['is_active'] ? 'bg-[#cc2222]' : 'bg-white/15' ?>"
             title="<?= $m['is_active'] ? 'Inaktiválás' : 'Aktiválás' ?>">
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
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Drag-and-drop csak szűrő nélküli nézetben aktív
<?php if (!$search && !$f_section && $f_active === ''): ?>
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
<?php endif; ?>
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
