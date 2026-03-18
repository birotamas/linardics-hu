<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

$pdo = cms_db();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$machine = null;
$specs   = [];

if ($id) {
    $machine = $pdo->prepare("SELECT * FROM machines WHERE id = ?") ? null : null;
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

try {
    $sections_rows = $pdo->query("SELECT slug, label FROM sections ORDER BY sort_order, id")->fetchAll();
    $sections = [];
    foreach ($sections_rows as $r) $sections[$r['slug']] = $r['label'];
} catch (Exception $e) {
    $sections = [];
}

try {
    $categories_rows = $pdo->query("SELECT slug, label FROM categories ORDER BY sort_order, id")->fetchAll();
    $categories = [];
    foreach ($categories_rows as $r) $categories[$r['slug']] = $r['label'];
} catch (Exception $e) {
    $categories = [];
}

try {
    $manufacturers = $pdo->query("SELECT name FROM manufacturers ORDER BY sort_order, name")->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $manufacturers = [];
}
?>

<style>
  .edit-form { max-width: 760px; }
  .edit-form .card { margin-bottom: 16px; }
  .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .form-grid-2 .col-span-2 { grid-column: span 2; }
  .form-checks { display: flex; align-items: center; gap: 20px; margin-top: 4px; }
  .form-check  { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; color: var(--muted); }
  .file-input {
    width: 100%; font-size: 13px; color: var(--muted);
    background: var(--elevated); border: 1px solid var(--border-md);
    border-radius: 7px; padding: 8px 12px; cursor: pointer;
  }
  .file-input::file-selector-button {
    background: var(--red); color: #fff; border: none;
    padding: 5px 12px; border-radius: 5px; cursor: pointer;
    font-size: 12px; font-weight: 600; margin-right: 10px;
    transition: background 0.15s;
  }
  .file-input::file-selector-button:hover { background: #b91c1c; }
  .img-preview { width: 128px; height: 88px; object-fit: cover; border: 1px solid var(--border-md); border-radius: 7px; }
  .error-banner {
    background: rgba(220,38,38,0.08); border: 1px solid rgba(220,38,38,0.22);
    border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;
  }
  .error-banner p { font-size: 13px; color: #f87171; margin-bottom: 4px; }
  .error-banner p:last-child { margin-bottom: 0; }
</style>

<?php if (!empty($errors)): ?>
<div class="error-banner">
  <?php foreach ($errors as $e): ?>
  <p><?= htmlspecialchars($e) ?></p>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="edit-form">

  <!-- Alapadatok -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Alapadatok</span>
    </div>
    <div class="card-body">
      <div class="form-grid-2">

        <div class="col-span-2">
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
          <label class="form-label">Kategória felirat <span style="font-weight:400;color:var(--subtle)">(pl. "Fiber lézer")</span></label>
          <input type="text" name="category_label" value="<?= htmlspecialchars($machine['category_label'] ?? '') ?>"
            class="form-input" placeholder="pl. CO₂ lézer">
        </div>

        <div>
          <label class="form-label">Badge szöveg <span style="font-weight:400;color:var(--subtle)">(pl. "5 kW", "×2")</span></label>
          <input type="text" name="badge" value="<?= htmlspecialchars($machine['badge'] ?? '') ?>"
            class="form-input" placeholder="pl. 5 kW">
        </div>

        <div class="col-span-2">
          <div class="form-checks">
            <label class="form-check">
              <input type="checkbox" name="is_active" value="1" <?= ($machine['is_active'] ?? 1) ? 'checked' : '' ?>>
              Aktív (látható a weboldalon)
            </label>
            <label class="form-check">
              <input type="checkbox" name="is_featured" value="1" <?= !empty($machine['is_featured']) ? 'checked' : '' ?>>
              Kiemelt gép
            </label>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Leírás -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Leírás</span>
    </div>
    <div class="card-body">
      <label class="form-label">Rövid leírás</label>
      <textarea name="short_description" rows="3" class="form-input"
        placeholder="2-4 mondat a gépről..."><?= htmlspecialchars($machine['short_description'] ?? '') ?></textarea>
    </div>
  </div>

  <!-- Műszaki adatok -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Műszaki adatok</span>
      <button type="button" onclick="addSpec()" class="btn-secondary" style="padding:6px 12px;font-size:12px;">+ Sor hozzáadása</button>
    </div>
    <div class="card-body">
      <div id="specs-container">
        <?php if (empty($specs)): ?>
        <div class="spec-row">
          <input type="text" name="spec_key[]" class="form-input" placeholder="Tulajdonság (pl. Munkaterület)">
          <input type="text" name="spec_val[]" class="form-input" placeholder="Érték (pl. 3000 × 1500 mm)">
          <button type="button" onclick="this.closest('.spec-row').remove()" class="spec-remove">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <?php else: ?>
        <?php foreach ($specs as $spec): ?>
        <div class="spec-row">
          <input type="text" name="spec_key[]" value="<?= htmlspecialchars($spec['spec_key']) ?>" class="form-input" placeholder="Tulajdonság neve">
          <input type="text" name="spec_val[]" value="<?= htmlspecialchars($spec['spec_value']) ?>" class="form-input" placeholder="Érték">
          <button type="button" onclick="this.closest('.spec-row').remove()" class="spec-remove">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Kép -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Kép</span>
    </div>
    <div class="card-body">
      <?php if (!empty($machine['image'])): ?>
      <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:16px;">
        <img src="../<?= htmlspecialchars($machine['image']) ?>" alt="" class="img-preview">
        <label class="form-check" style="margin-top:6px;color:rgba(248,113,113,0.7);">
          <input type="checkbox" name="delete_image" value="1">
          Kép törlése
        </label>
      </div>
      <?php endif; ?>
      <label class="form-label">Kép feltöltése <span style="font-weight:400;color:var(--subtle)">(JPG, PNG, WebP – max. 3 MB)</span></label>
      <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="file-input">
    </div>
  </div>

  <!-- Submit -->
  <div style="display:flex;align-items:center;gap:10px;">
    <button type="submit" class="btn-primary">Mentés</button>
    <a href="machines.php" class="btn-secondary">Mégsem</a>
  </div>

</form>

<script>
function addSpec() {
  const container = document.getElementById('specs-container');
  const row = document.createElement('div');
  row.className = 'spec-row';
  row.innerHTML = `
    <input type="text" name="spec_key[]" class="form-input" placeholder="Tulajdonság neve">
    <input type="text" name="spec_val[]" class="form-input" placeholder="Érték">
    <button type="button" onclick="this.closest('.spec-row').remove()" class="spec-remove">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>`;
  container.appendChild(row);
  row.querySelector('input').focus();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
