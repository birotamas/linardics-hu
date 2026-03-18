<?php
/**
 * Képek importálása – egyszeri futtatásra való segédeszköz.
 * Megmutatja az assets/images/machines/ mappában lévő képeket,
 * és automatikusan javasol gép-hozzárendelést névegyezés alapján.
 */
require_once __DIR__ . '/includes/auth.php';
require_auth();

$pdo       = cms_db();
$img_dir   = dirname(__DIR__) . '/assets/images/machines/';
$img_base  = 'assets/images/machines/';

// Handle POST: save assignments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assigned = 0;
    foreach ($_POST['assign'] ?? [] as $filename => $machine_id) {
        $machine_id = (int)$machine_id;
        if (!$machine_id) continue;
        $path = $img_base . basename($filename);
        $pdo->prepare("UPDATE machines SET image = ? WHERE id = ? AND (image IS NULL OR image = '')")
            ->execute([$path, $machine_id]);
        $assigned++;
    }
    header("Location: import_images.php?done=$assigned"); exit;
}

// Load all machines
$machines = $pdo->query("SELECT id, name, image FROM machines ORDER BY name")->fetchAll();
$assigned_images = array_filter(array_column($machines, 'image'));

// Scan image files
$files = array_values(array_filter(
    scandir($img_dir) ?: [],
    fn($f) => preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $f) && $f !== '.gitkeep'
));

// Already assigned filenames
$already_used = array_map('basename', $assigned_images);

// Fuzzy match: score filename against machine name
function match_score(string $filename, string $machine_name): int {
    $fn   = strtolower(preg_replace('/\.(jpg|jpeg|png|webp|gif)$/i', '', $filename));
    $fn   = preg_replace('/[-_]+/', ' ', $fn);
    $mn   = strtolower($machine_name);
    $score = 0;
    foreach (preg_split('/\s+/', $fn) as $word) {
        if (strlen($word) >= 3 && str_contains($mn, $word)) $score += strlen($word);
    }
    return $score;
}

// For each image, find best machine match
function best_match(string $filename, array $machines): array {
    $best_id = 0; $best_score = 0; $best_name = '';
    foreach ($machines as $m) {
        $score = match_score($filename, $m['name']);
        if ($score > $best_score) {
            $best_score = $score;
            $best_id    = $m['id'];
            $best_name  = $m['name'];
        }
    }
    return ['id' => $best_id, 'name' => $best_name, 'score' => $best_score];
}

$done = isset($_GET['done']) ? (int)$_GET['done'] : -1;
$page_title = 'Képek importálása';
include __DIR__ . '/includes/header.php';
?>

<style>
  .import-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .import-table thead th {
    padding: 10px 16px; text-align: left;
    font-size: 11px; font-weight: 600; letter-spacing: 0.06em;
    text-transform: uppercase; color: var(--subtle);
    border-bottom: 1px solid var(--border);
  }
  .import-table tbody tr { border-bottom: 1px solid var(--border); }
  .import-table tbody tr:last-child { border-bottom: none; }
  .import-table td { padding: 12px 16px; vertical-align: middle; color: var(--muted); }
  .thumb { width: 80px; height: 56px; object-fit: cover; border-radius: 5px; border: 1px solid var(--border); display: block; }
  .already-tag { font-size: 11px; color: #86efac; background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); border-radius: 4px; padding: 2px 7px; }
  .skip-tag    { font-size: 11px; color: var(--subtle); background: var(--elevated); border: 1px solid var(--border); border-radius: 4px; padding: 2px 7px; }
</style>

<?php if ($done >= 0): ?>
<div style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);border-radius:8px;padding:14px 18px;margin-bottom:20px;font-size:13px;color:#86efac;">
  <?= $done ?> gépen frissült a kép. <a href="machines.php" style="color:#86efac;text-decoration:underline;">Vissza a géplistához →</a>
</div>
<?php endif; ?>

<div class="card" style="margin-bottom:16px;">
  <div class="card-header">
    <span class="card-title">Letöltött képek – <?= count($files) ?> db</span>
    <span class="pill pill-slate"><?= count($already_used) ?> már hozzárendelve</span>
  </div>
  <div style="padding:14px 22px;border-bottom:1px solid var(--border);font-size:13px;color:var(--muted);line-height:1.6;">
    A rendszer névegyezés alapján automatikusan javasol géphozzárendelést. Ellenőrizd, módosítsd ha kell, majd kattints a <strong>Mentés</strong> gombra. Már képpel rendelkező gépeket nem ír felül.
  </div>

  <form method="POST">
    <table class="import-table">
      <thead>
        <tr>
          <th style="width:100px;">Kép</th>
          <th>Fájlnév</th>
          <th>Javasolt gép</th>
          <th>Hozzárendelés</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($files as $file):
          $is_used = in_array($file, $already_used);
          $match   = best_match($file, $machines);
        ?>
        <tr>
          <td><img src="../<?= $img_base . htmlspecialchars($file) ?>" alt="" class="thumb"></td>
          <td style="font-family:monospace;font-size:12px;color:var(--muted);"><?= htmlspecialchars($file) ?></td>
          <td>
            <?php if ($is_used): ?>
              <span class="already-tag">Már hozzárendelve</span>
            <?php elseif ($match['id']): ?>
              <span style="color:var(--text);font-weight:500;"><?= htmlspecialchars($match['name']) ?></span>
              <span style="font-size:11px;color:var(--subtle);margin-left:4px;">(<?= $match['score'] ?> pont)</span>
            <?php else: ?>
              <span class="skip-tag">Nincs találat</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($is_used): ?>
              <span class="skip-tag">Kihagyás</span>
            <?php else: ?>
              <select name="assign[<?= htmlspecialchars($file) ?>]" class="form-input" style="min-width:220px;">
                <option value="">– Kihagyás –</option>
                <?php foreach ($machines as $m): ?>
                <option value="<?= $m['id'] ?>" <?= $match['id'] === $m['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['name']) ?>
                  <?php if ($m['image']): ?>(van kép)<?php endif; ?>
                </option>
                <?php endforeach; ?>
              </select>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div style="padding:16px 22px;border-top:1px solid var(--border);display:flex;gap:10px;">
      <button type="submit" class="btn-primary">Mentés</button>
      <a href="machines.php" class="btn-secondary">Mégsem</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
