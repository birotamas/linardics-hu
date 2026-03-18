<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

$pdo = cms_db();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$machine = null;
$specs   = [];

if ($id) {
    $machine = $pdo->prepare("SELECT * FROM machines WHERE id = ?")->execute([$id]) ? null : null;
    $stmt = $pdo->prepare("SELECT * FROM machines WHERE id = ?");
    $stmt->execute([$id]);
    $machine = $stmt->fetch();
    if (!$machine) { header('Location: machines.php'); exit; }

    $stmt2 = $pdo->prepare("SELECT * FROM machine_specs WHERE machine_id = ? ORDER BY sort_order");
    $stmt2->execute([$id]);
    $specs = $stmt2->fetchAll();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $manufacturer = trim($_POST['manufacturer'] ?? '');
    $category     = trim($_POST['category'] ?? '');
    $cat_label    = trim($_POST['category_label'] ?? '');
    $badge        = trim($_POST['badge'] ?? '');
    $section      = trim($_POST['section'] ?? 'egyeb');
    $short_desc   = trim($_POST['short_description'] ?? '');
    $is_active    = isset($_POST['is_active']) ? 1 : 0;
    $is_featured  = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($name)) $errors[] = 'A gép neve kötelező.';

    // Handle image upload
    $image_path = $machine['image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
        $file     = $_FILES['image'];
        $allowed  = ['image/jpeg','image/png','image/webp','image/gif'];
        $max_size = 3 * 1024 * 1024; // 3 MB

        if (!in_array($file['type'], $allowed)) {
            $errors[] = 'Csak JPG, PNG, WebP és GIF fájlok tölthetők fel.';
        } elseif ($file['size'] > $max_size) {
            $errors[] = 'A fájl mérete maximum 3 MB lehet.';
        } else {
            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'machine_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
            $dest     = dirname(__DIR__) . '/assets/images/machines/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                // Delete old image
                if ($image_path) {
                    $old = dirname(__DIR__) . '/assets/images/machines/' . basename($image_path);
                    if (file_exists($old)) unlink($old);
                }
                $image_path = 'assets/images/machines/' . $filename;
            } else {
                $errors[] = 'A fájl feltöltése sikertelen. Ellenőrizd a mappajogosultságokat.';
            }
        }
    }

    // Delete image
    if (isset($_POST['delete_image']) && $image_path) {
        $old = dirname(__DIR__) . '/' . $image_path;
        if (file_exists($old)) unlink($old);
        $image_path = null;
    }

    if (empty($errors)) {
        if ($id) {
            $pdo->prepare("
                UPDATE machines SET name=?, manufacturer=?, category=?, category_label=?, badge=?,
                section=?, short_description=?, image=?, is_active=?, is_featured=? WHERE id=?
            ")->execute([$name,$manufacturer,$category,$cat_label,$badge,$section,$short_desc,$image_path,$is_active,$is_featured,$id]);
        } else {
            $max_order = $pdo->query("SELECT COALESCE(MAX(sort_order),0)+1 FROM machines")->fetchColumn();
            $pdo->prepare("
                INSERT INTO machines (name, manufacturer, category, category_label, badge, section,
                short_description, image, is_active, is_featured, sort_order)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ")->execute([$name,$manufacturer,$category,$cat_label,$badge,$section,$short_desc,$image_path,$is_active,$is_featured,$max_order]);
            $id = (int)$pdo->lastInsertId();
        }

        // Save specs
        $pdo->prepare("DELETE FROM machine_specs WHERE machine_id = ?")->execute([$id]);
        $spec_keys = $_POST['spec_key'] ?? [];
        $spec_vals = $_POST['spec_val'] ?? [];
        $stmt_s = $pdo->prepare("INSERT INTO machine_specs (machine_id, spec_key, spec_value, sort_order) VALUES (?,?,?,?)");
        foreach ($spec_keys as $i => $key) {
            $key = trim($key);
            $val = trim($spec_vals[$i] ?? '');
            if ($key !== '' || $val !== '') {
                $stmt_s->execute([$id, $key, $val, $i + 1]);
            }
        }

        header('Location: machines.php?saved=1');
        exit;
    }

    // Rebuild machine array for re-rendering form on error
    $machine = array_merge($machine ?? [], compact('name','manufacturer','category','cat_label','badge','section','short_desc','is_active','is_featured'));
    $machine['category_label'] = $cat_label;
    $machine['short_description'] = $short_desc;
    $specs = [];
    foreach (($_POST['spec_key'] ?? []) as $i => $k) {
        $specs[] = ['spec_key' => $k, 'spec_value' => $_POST['spec_val'][$i] ?? ''];
    }
}

$page_title = $id ? 'Gép szerkesztése' : 'Új gép';
include __DIR__ . '/includes/header.php';

$sections = [
    'trulaser'     => 'TRUMPF Lézervágók',
    'trubend'      => 'TRUMPF Hajlítók',
    'amada'        => 'AMADA Hajlítók',
    'egyeb'        => 'Csőhajlítás + Egyéb',
    'kotestechnika'=> 'Kötéstechnika',
];
$categories = [
    'lezervago'    => 'Lézervágó',
    'hajlito'      => 'Hajlító',
    'csohajlito'   => 'Csőhajlító',
    'porfesto'     => 'Porfestő',
    'kotestechnika'=> 'Kötéstechnika',
];
$manufacturers = ['TRUMPF','AMADA','SOCO','Gema','Wagner','STEELINE','VS','SOYER','PEMSERTER','Egyéb'];
?>

<?php if (!empty($errors)): ?>
<div class="bg-red-900/20 border border-red-500/30 px-4 py-3 mb-6 space-y-1">
  <?php foreach ($errors as $e): ?>
  <p class="text-red-300 text-sm"><?= htmlspecialchars($e) ?></p>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="space-y-6 max-w-3xl">

  <!-- Alapadatok -->
  <div class="bg-[#0d1b2a] border border-white/8 p-6">
    <h2 class="font-heading font-bold text-lg uppercase tracking-widest text-white/60 mb-5">Alapadatok</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

      <div class="md:col-span-2">
        <label class="form-label">Gép neve *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($machine['name'] ?? '') ?>"
          required class="form-input" placeholder="pl. TruLaser 3030 Fiber">
      </div>

      <div>
        <label class="form-label">Gyártó</label>
        <select name="manufacturer" class="form-input">
          <?php foreach ($manufacturers as $m): ?>
          <option value="<?= $m ?>" <?= ($machine['manufacturer'] ?? '') === $m ? 'selected' : '' ?>><?= $m ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="form-label">Szekció</label>
        <select name="section" class="form-input">
          <?php foreach ($sections as $val => $label): ?>
          <option value="<?= $val ?>" <?= ($machine['section'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="form-label">Kategória</label>
        <select name="category" class="form-input">
          <?php foreach ($categories as $val => $label): ?>
          <option value="<?= $val ?>" <?= ($machine['category'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="form-label">Kategória felirat <span class="normal-case text-white/20">(pl. "Fő gép · Fiber lézer")</span></label>
        <input type="text" name="category_label" value="<?= htmlspecialchars($machine['category_label'] ?? '') ?>"
          class="form-input" placeholder="pl. CO₂ lézer">
      </div>

      <div>
        <label class="form-label">Badge szöveg <span class="normal-case text-white/20">(pl. "5 kW", "×2")</span></label>
        <input type="text" name="badge" value="<?= htmlspecialchars($machine['badge'] ?? '') ?>"
          class="form-input" placeholder="pl. 5 kW">
      </div>

      <div class="flex items-center gap-6 mt-2">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="is_active" value="1" <?= ($machine['is_active'] ?? 1) ? 'checked' : '' ?> class="w-4 h-4 accent-[#cc2222]">
          <span class="text-sm text-white/60">Aktív (látható a weboldalon)</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="is_featured" value="1" <?= !empty($machine['is_featured']) ? 'checked' : '' ?> class="w-4 h-4 accent-[#cc2222]">
          <span class="text-sm text-white/60">Kiemelt gép</span>
        </label>
      </div>

    </div>
  </div>

  <!-- Leírás -->
  <div class="bg-[#0d1b2a] border border-white/8 p-6">
    <h2 class="font-heading font-bold text-lg uppercase tracking-widest text-white/60 mb-5">Leírás</h2>
    <label class="form-label">Rövid leírás</label>
    <textarea name="short_description" rows="3" class="form-input resize-none"
      placeholder="2-4 mondat a gépről..."><?= htmlspecialchars($machine['short_description'] ?? '') ?></textarea>
  </div>

  <!-- Műszaki adatok -->
  <div class="bg-[#0d1b2a] border border-white/8 p-6">
    <div class="flex items-center justify-between mb-5">
      <h2 class="font-heading font-bold text-lg uppercase tracking-widest text-white/60">Műszaki adatok</h2>
      <button type="button" onclick="addSpec()" class="btn-secondary text-xs py-1.5 px-3">+ Sor hozzáadása</button>
    </div>
    <div id="specs-container" class="space-y-2">
      <?php if (empty($specs)): ?>
      <div class="spec-row flex gap-2 items-center">
        <input type="text" name="spec_key[]" class="form-input flex-1" placeholder="Tulajdonság neve (pl. Munkaterület)">
        <input type="text" name="spec_val[]" class="form-input flex-1" placeholder="Érték (pl. 3000 × 1500 mm)">
        <button type="button" onclick="this.closest('.spec-row').remove()" class="text-white/25 hover:text-[#cc2222] transition-colors p-1 shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <?php else: ?>
      <?php foreach ($specs as $spec): ?>
      <div class="spec-row flex gap-2 items-center">
        <input type="text" name="spec_key[]" value="<?= htmlspecialchars($spec['spec_key']) ?>" class="form-input flex-1" placeholder="Tulajdonság neve">
        <input type="text" name="spec_val[]" value="<?= htmlspecialchars($spec['spec_value']) ?>" class="form-input flex-1" placeholder="Érték">
        <button type="button" onclick="this.closest('.spec-row').remove()" class="text-white/25 hover:text-[#cc2222] transition-colors p-1 shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Kép -->
  <div class="bg-[#0d1b2a] border border-white/8 p-6">
    <h2 class="font-heading font-bold text-lg uppercase tracking-widest text-white/60 mb-5">Kép</h2>
    <?php if (!empty($machine['image'])): ?>
    <div class="mb-4 flex items-start gap-4">
      <img src="../<?= htmlspecialchars($machine['image']) ?>" alt="" class="w-32 h-24 object-cover border border-white/10">
      <label class="flex items-center gap-2 cursor-pointer mt-2">
        <input type="checkbox" name="delete_image" value="1" class="w-4 h-4 accent-[#cc2222]">
        <span class="text-sm text-[#cc2222]/70">Kép törlése</span>
      </label>
    </div>
    <?php endif; ?>
    <label class="form-label">Kép feltöltése <span class="normal-case text-white/20">(JPG, PNG, WebP – max. 3 MB)</span></label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
      class="w-full text-sm text-white/50 file:mr-3 file:py-2 file:px-4 file:border-0 file:bg-[#cc2222] file:text-white file:text-xs file:font-semibold file:uppercase file:tracking-wide file:cursor-pointer hover:file:bg-[#a01818]">
  </div>

  <!-- Submit -->
  <div class="flex items-center gap-3">
    <button type="submit" class="btn-primary px-8 py-3 text-base">Mentés</button>
    <a href="machines.php" class="btn-secondary px-6 py-3">Mégsem</a>
  </div>

</form>

<script>
function addSpec() {
  const container = document.getElementById('specs-container');
  const row = document.createElement('div');
  row.className = 'spec-row flex gap-2 items-center';
  row.innerHTML = `
    <input type="text" name="spec_key[]" class="form-input flex-1" placeholder="Tulajdonság neve">
    <input type="text" name="spec_val[]" class="form-input flex-1" placeholder="Érték">
    <button type="button" onclick="this.closest('.spec-row').remove()" class="text-white/25 hover:text-[#cc2222] transition-colors p-1 shrink-0">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>`;
  container.appendChild(row);
  row.querySelector('input').focus();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
