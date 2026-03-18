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
        'geppark_eyebrow' => 'Eyebrow szöveg',
        'geppark_title'   => 'Főcím (H1)',
        'geppark_intro'   => 'Bevezető szöveg',
    ],
    'Summary badge-ek' => [
        'geppark_badge_1' => 'Badge 1',
        'geppark_badge_2' => 'Badge 2',
        'geppark_badge_3' => 'Badge 3',
        'geppark_badge_4' => 'Badge 4',
    ],
    'TRUMPF Lézervágók szekció' => [
        'trulaser_eyebrow' => 'Eyebrow',
        'trulaser_title'   => 'Szekció cím',
    ],
    'TRUMPF Hajlítók szekció' => [
        'trubend_eyebrow' => 'Eyebrow',
        'trubend_title'   => 'Szekció cím',
    ],
    'AMADA Hajlítók szekció' => [
        'amada_eyebrow' => 'Eyebrow',
        'amada_title'   => 'Szekció cím',
    ],
    'Csőhajlítás & Egyéb szekció' => [
        'egyeb_eyebrow' => 'Eyebrow',
        'egyeb_title'   => 'Szekció cím',
    ],
];
?>

<form method="POST" class="space-y-6 max-w-2xl">
  <?php foreach ($groups as $group_name => $fields): ?>
  <div class="bg-[#0d1b2a] border border-white/8 p-6">
    <h2 class="font-heading font-bold text-base uppercase tracking-widest text-white/50 mb-5"><?= htmlspecialchars($group_name) ?></h2>
    <div class="space-y-4">
      <?php foreach ($fields as $key => $label): ?>
      <?php $val = $s[$key]['setting_value'] ?? ''; $type = $s[$key]['setting_type'] ?? 'text'; ?>
      <div>
        <label class="form-label"><?= htmlspecialchars($label) ?></label>
        <?php if ($type === 'textarea'): ?>
        <textarea name="<?= $key ?>" rows="3" class="form-input resize-none"><?= htmlspecialchars($val) ?></textarea>
        <?php else: ?>
        <input type="text" name="<?= $key ?>" value="<?= htmlspecialchars($val) ?>" class="form-input">
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <div class="flex items-center gap-3">
    <button type="submit" class="btn-primary px-8 py-3 text-base">Beállítások mentése</button>
    <a href="../geppark.php" target="_blank" class="btn-secondary px-6 py-3">Előnézet →</a>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
