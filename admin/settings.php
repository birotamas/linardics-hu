<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

$pdo = cms_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE page_settings SET setting_value = ? WHERE setting_key = ?");
    foreach ($_POST as $key => $value) {
        if (strpos($key, '__') !== 0) { // skip internal keys
            $stmt->execute([trim($value), $key]);
        }
    }
    header('Location: settings.php?saved=1');
    exit;
}

$settings_raw = $pdo->query("SELECT * FROM page_settings WHERE setting_group = 'geppark' ORDER BY id")->fetchAll();
$s = [];
foreach ($settings_raw as $row) {
    $s[$row['setting_key']] = $row;
}

$toast_msg = isset($_GET['saved']) ? 'Beállítások mentve!' : '';
$page_title = 'Oldal beállítások';
include __DIR__ . '/includes/header.php';

$groups = [
    'Géppark fejléc' => [
        'geppark_eyebrow' => 'Felső sor',
        'geppark_title'   => 'Főcím (H1)',
        'geppark_intro'   => 'Bevezető szöveg',
    ],
    'Összefoglaló jelölők' => [
        'geppark_badge_1' => 'Jelölő 1',
        'geppark_badge_2' => 'Jelölő 2',
        'geppark_badge_3' => 'Jelölő 3',
        'geppark_badge_4' => 'Jelölő 4',
    ],
    'TRUMPF Lézervágók szekció' => [
        'trulaser_eyebrow' => 'Felső sor',
        'trulaser_title'   => 'Szekció cím',
    ],
    'TRUMPF Hajlítók szekció' => [
        'trubend_eyebrow' => 'Felső sor',
        'trubend_title'   => 'Szekció cím',
    ],
    'AMADA Hajlítók szekció' => [
        'amada_eyebrow' => 'Felső sor',
        'amada_title'   => 'Szekció cím',
    ],
    'Csőhajlítás & Egyéb szekció' => [
        'egyeb_eyebrow' => 'Felső sor',
        'egyeb_title'   => 'Szekció cím',
    ],
];
?>

<form method="POST" style="max-width:640px;">
  <?php foreach ($groups as $group_name => $fields): ?>
  <div class="card" style="margin-bottom:16px;">
    <div class="card-header">
      <span class="card-title"><?= htmlspecialchars($group_name) ?></span>
    </div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
      <?php foreach ($fields as $key => $label): ?>
      <?php $val = $s[$key]['setting_value'] ?? ''; $type = $s[$key]['setting_type'] ?? 'text'; ?>
      <div>
        <label class="form-label"><?= htmlspecialchars($label) ?></label>
        <?php if ($type === 'textarea'): ?>
        <textarea name="<?= $key ?>" rows="3" class="form-input"><?= htmlspecialchars($val) ?></textarea>
        <?php else: ?>
        <input type="text" name="<?= $key ?>" value="<?= htmlspecialchars($val) ?>" class="form-input">
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <div style="display:flex;align-items:center;gap:10px;">
    <button type="submit" class="btn-primary">Beállítások mentése</button>
    <a href="../geppark.php" target="_blank" class="btn-secondary">Előnézet →</a>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
