<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

$pdo = cms_db();

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $pdo->prepare("UPDATE machines SET is_active = 1 - is_active WHERE id = ?")->execute([(int)$_GET['toggle']]);
    header('Location: machines.php?toggled=1'); exit;
}
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM machines WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: machines.php?deleted=1'); exit;
}

$search    = trim($_GET['q']       ?? '');
$f_section = trim($_GET['section'] ?? '');
$f_active  = $_GET['active'] ?? '';

$where = []; $params = [];
if ($search !== '')  { $where[] = '(name LIKE ? OR manufacturer LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($f_section !== '') { $where[] = 'section = ?'; $params[] = $f_section; }
if ($f_active !== '')  { $where[] = 'is_active = ?'; $params[] = (int)$f_active; }

$sql  = "SELECT * FROM machines" . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY sort_order, id";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$machines  = $stmt->fetchAll();
$total_all = $pdo->query("SELECT COUNT(*) FROM machines")->fetchColumn();

$toast_msg = isset($_GET['saved'])   ? 'Gép sikeresen mentve!'  :
            (isset($_GET['toggled']) ? 'Láthatóság módosítva.'  :
            (isset($_GET['deleted']) ? 'Gép törölve.'           : ''));

try {
    $sections_rows = $pdo->query("SELECT slug, label FROM sections ORDER BY sort_order, id")->fetchAll();
    $sections = ['' => 'Minden szekció'];
    foreach ($sections_rows as $r) $sections[$r['slug']] = $r['label'];
} catch (Exception $e) {
    $sections = ['' => 'Minden szekció'];
}

$no_filter = !$search && !$f_section && $f_active === '';

$page_title = 'Gépek';
include __DIR__ . '/includes/header.php';
?>

<!-- Toolbar -->
<form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;margin-bottom:20px;">
  <div style="flex:1;min-width:200px;">
    <label class="form-label">Keresés</label>
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" class="form-input" placeholder="Gép neve, gyártó…">
  </div>
  <div>
    <label class="form-label">Szekció</label>
    <select name="section" class="form-input" style="min-width:160px;">
      <?php foreach ($sections as $v => $l): ?>
      <option value="<?= $v ?>" <?= $f_section === $v ? 'selected' : '' ?>><?= $l ?></option>
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
  <div style="display:flex;gap:8px;align-items:flex-end;">
    <button type="submit" class="btn-primary">
      <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      Szűrés
    </button>
    <?php if (!$no_filter): ?>
    <a href="machines.php" class="btn-secondary">Törlés</a>
    <?php endif; ?>
  </div>
  <div style="margin-left:auto;align-self:flex-end;">
    <a href="machine-edit.php" class="btn-primary">
      <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
      Új gép
    </a>
  </div>
</form>

<p style="font-size:12px;color:var(--muted);margin-bottom:14px;">
  <?= count($machines) ?> találat<?php if (!$no_filter): ?> <span style="color:var(--subtle)">/ <?= $total_all ?> összesen</span><?php endif; ?>
  <?php if ($no_filter): ?><span style="color:var(--subtle);margin-left:6px;">· húzd a sorokat az átrendezéshez</span><?php endif; ?>
</p>

<div class="card">
  <?php if (empty($machines)): ?>
  <div class="empty-state">Nincs találat a megadott feltételekre.</div>
  <?php else: ?>
  <table class="data-table" id="machines-table">
    <thead>
      <tr>
        <th style="width:36px;"></th>
        <th>Gép neve</th>
        <th>Gyártó</th>
        <th>Szekció</th>
        <th style="text-align:center;">Aktív</th>
        <th style="text-align:right;">Műveletek</th>
      </tr>
    </thead>
    <tbody id="sortable-body">
      <?php foreach ($machines as $m): ?>
      <tr data-id="<?= $m['id'] ?>">
        <td>
          <div class="drag-handle" title="Húzd a sorrend módosításához">
            <svg style="width:14px;height:14px" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 6zm0 6a2 2 0 10.001 4.001A2 2 0 007 12zm6-8a2 2 0 10-.001-4.001A2 2 0 0013 4zm0 6a2 2 0 10-.001-4.001A2 2 0 0013 10zm0 6a2 2 0 10-.001-4.001A2 2 0 0013 16z"/></svg>
          </div>
        </td>
        <td>
          <span style="font-weight:600;color:var(--text)"><?= htmlspecialchars($m['name']) ?></span>
          <?php if ($m['badge']): ?>
          <span class="pill pill-red" style="margin-left:6px"><?= htmlspecialchars($m['badge']) ?></span>
          <?php endif; ?>
          <?php if ($m['is_featured']): ?>
          <span class="pill pill-slate" style="margin-left:4px">Kiemelt</span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($m['manufacturer']) ?></td>
        <td><span class="pill pill-slate"><?= htmlspecialchars($m['section']) ?></span></td>
        <td style="text-align:center;">
          <a href="machines.php?toggle=<?= $m['id'] ?><?= $search ? '&q='.urlencode($search) : '' ?><?= $f_section ? '&section='.urlencode($f_section) : '' ?><?= $f_active !== '' ? '&active='.$f_active : '' ?>"
             title="<?= $m['is_active'] ? 'Kattints az inaktiváláshoz' : 'Kattints az aktiváláshoz' ?>">
            <label class="toggle" onclick="event.preventDefault();this.closest('a').click()">
              <input type="checkbox" <?= $m['is_active'] ? 'checked' : '' ?> readonly>
              <span class="toggle-slider"></span>
            </label>
          </a>
        </td>
        <td style="text-align:right;">
          <div style="display:flex;gap:6px;justify-content:flex-end;">
            <a href="machine-edit.php?id=<?= $m['id'] ?>" class="btn-ghost">Szerkesztés</a>
            <a href="machines.php?delete=<?= $m['id'] ?>"
               onclick="return confirm('Biztosan törlöd: <?= htmlspecialchars(addslashes($m['name'])) ?>?')"
               class="btn-danger-ghost">Törlés</a>
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
<?php if ($no_filter): ?>
Sortable.create(document.getElementById('sortable-body'), {
  animation: 150,
  handle: '.drag-handle',
  ghostClass: 'opacity-30',
  onEnd() {
    const ids = [...document.querySelectorAll('#sortable-body tr[data-id]')].map(r => r.dataset.id);
    fetch('reorder.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ids}) });
  }
});
<?php endif; ?>
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
