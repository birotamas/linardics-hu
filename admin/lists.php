<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

$pdo = cms_db();

// ── Auto-migration: create tables if they don't exist ──────────────────────

$pdo->exec("
    CREATE TABLE IF NOT EXISTS sections (
      id INT AUTO_INCREMENT PRIMARY KEY,
      slug VARCHAR(50) NOT NULL UNIQUE,
      label VARCHAR(100) NOT NULL,
      sort_order INT DEFAULT 0
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS categories (
      id INT AUTO_INCREMENT PRIMARY KEY,
      slug VARCHAR(50) NOT NULL UNIQUE,
      label VARCHAR(100) NOT NULL,
      sort_order INT DEFAULT 0
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS manufacturers (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL UNIQUE,
      sort_order INT DEFAULT 0
    )
");

// ── Seed data (idempotent) ─────────────────────────────────────────────────

$pdo->exec("
    INSERT IGNORE INTO sections (slug, label, sort_order) VALUES
      ('trulaser',      'TRUMPF Lézervágók',    1),
      ('trubend',       'TRUMPF Hajlítók',       2),
      ('amada',         'AMADA Hajlítók',        3),
      ('egyeb',         'Csőhajlítás + Egyéb',  4),
      ('kotestechnika', 'Kötéstechnika',         5)
");

$pdo->exec("
    INSERT IGNORE INTO categories (slug, label, sort_order) VALUES
      ('lezervago',     'Lézervágó',     1),
      ('hajlito',       'Hajlító',       2),
      ('csohajlito',    'Csőhajlító',    3),
      ('porfesto',      'Porfestő',      4),
      ('kotestechnika', 'Kötéstechnika', 5)
");

$pdo->exec("
    INSERT IGNORE INTO manufacturers (name, sort_order) VALUES
      ('TRUMPF',    1),
      ('AMADA',     2),
      ('SOCO',      3),
      ('Gema',      4),
      ('Wagner',    5),
      ('STEELINE',  6),
      ('VS',        7),
      ('SOYER',     8),
      ('PEMSERTER', 9),
      ('Egyéb',    10)
");

// ── POST handler ───────────────────────────────────────────────────────────

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── SECTIONS ────────────────────────────────────────────────────────────
    if ($action === 'add_section') {
        $slug  = trim($_POST['slug'] ?? '');
        $label = trim($_POST['label'] ?? '');
        if ($slug && $label) {
            $max = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM sections")->fetchColumn();
            $pdo->prepare("INSERT INTO sections (slug, label, sort_order) VALUES (?, ?, ?)")
                ->execute([$slug, $label, $max + 1]);
        }
        header('Location: lists.php?saved=1'); exit;
    }

    if ($action === 'edit_section') {
        $id       = (int)($_POST['id'] ?? 0);
        $new_slug = trim($_POST['slug'] ?? '');
        $label    = trim($_POST['label'] ?? '');
        if ($id && $new_slug && $label) {
            $old_slug = $pdo->prepare("SELECT slug FROM sections WHERE id = ?");
            $old_slug->execute([$id]);
            $old_slug = $old_slug->fetchColumn();
            $pdo->prepare("UPDATE sections SET slug = ?, label = ? WHERE id = ?")
                ->execute([$new_slug, $label, $id]);
            if ($old_slug && $old_slug !== $new_slug) {
                $pdo->prepare("UPDATE machines SET section = ? WHERE section = ?")
                    ->execute([$new_slug, $old_slug]);
            }
        }
        header('Location: lists.php?saved=1'); exit;
    }

    if ($action === 'delete_section') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $slug = $pdo->prepare("SELECT slug FROM sections WHERE id = ?");
            $slug->execute([$id]);
            $slug = $slug->fetchColumn();
            $in_use = $slug
                ? (int)$pdo->prepare("SELECT COUNT(*) FROM machines WHERE section = ?")->execute([$slug]) && false
                : false;
            // Correct in-use check:
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM machines WHERE section = ?");
            $stmt->execute([$slug]);
            $count = (int)$stmt->fetchColumn();
            if ($count > 0) {
                header('Location: lists.php?error=in_use'); exit;
            }
            $pdo->prepare("DELETE FROM sections WHERE id = ?")->execute([$id]);
        }
        header('Location: lists.php?deleted=1'); exit;
    }

    // ── CATEGORIES ──────────────────────────────────────────────────────────
    if ($action === 'add_category') {
        $slug  = trim($_POST['slug'] ?? '');
        $label = trim($_POST['label'] ?? '');
        if ($slug && $label) {
            $max = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM categories")->fetchColumn();
            $pdo->prepare("INSERT INTO categories (slug, label, sort_order) VALUES (?, ?, ?)")
                ->execute([$slug, $label, $max + 1]);
        }
        header('Location: lists.php?saved=1'); exit;
    }

    if ($action === 'edit_category') {
        $id       = (int)($_POST['id'] ?? 0);
        $new_slug = trim($_POST['slug'] ?? '');
        $label    = trim($_POST['label'] ?? '');
        if ($id && $new_slug && $label) {
            $old_stmt = $pdo->prepare("SELECT slug FROM categories WHERE id = ?");
            $old_stmt->execute([$id]);
            $old_slug = $old_stmt->fetchColumn();
            $pdo->prepare("UPDATE categories SET slug = ?, label = ? WHERE id = ?")
                ->execute([$new_slug, $label, $id]);
            if ($old_slug && $old_slug !== $new_slug) {
                $pdo->prepare("UPDATE machines SET category = ? WHERE category = ?")
                    ->execute([$new_slug, $old_slug]);
            }
        }
        header('Location: lists.php?saved=1'); exit;
    }

    if ($action === 'delete_category') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $slug_stmt = $pdo->prepare("SELECT slug FROM categories WHERE id = ?");
            $slug_stmt->execute([$id]);
            $slug = $slug_stmt->fetchColumn();
            $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM machines WHERE category = ?");
            $count_stmt->execute([$slug]);
            $count = (int)$count_stmt->fetchColumn();
            if ($count > 0) {
                header('Location: lists.php?error=in_use'); exit;
            }
            $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        }
        header('Location: lists.php?deleted=1'); exit;
    }

    // ── MANUFACTURERS ───────────────────────────────────────────────────────
    if ($action === 'add_manufacturer') {
        $name = trim($_POST['name'] ?? '');
        if ($name) {
            $max = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM manufacturers")->fetchColumn();
            $pdo->prepare("INSERT INTO manufacturers (name, sort_order) VALUES (?, ?)")
                ->execute([$name, $max + 1]);
        }
        header('Location: lists.php?saved=1'); exit;
    }

    if ($action === 'edit_manufacturer') {
        $id      = (int)($_POST['id'] ?? 0);
        $new_name = trim($_POST['name'] ?? '');
        if ($id && $new_name) {
            $old_stmt = $pdo->prepare("SELECT name FROM manufacturers WHERE id = ?");
            $old_stmt->execute([$id]);
            $old_name = $old_stmt->fetchColumn();
            $pdo->prepare("UPDATE manufacturers SET name = ? WHERE id = ?")
                ->execute([$new_name, $id]);
            if ($old_name && $old_name !== $new_name) {
                $pdo->prepare("UPDATE machines SET manufacturer = ? WHERE manufacturer = ?")
                    ->execute([$new_name, $old_name]);
            }
        }
        header('Location: lists.php?saved=1'); exit;
    }

    if ($action === 'delete_manufacturer') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $name_stmt = $pdo->prepare("SELECT name FROM manufacturers WHERE id = ?");
            $name_stmt->execute([$id]);
            $name = $name_stmt->fetchColumn();
            $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM machines WHERE manufacturer = ?");
            $count_stmt->execute([$name]);
            $count = (int)$count_stmt->fetchColumn();
            if ($count > 0) {
                header('Location: lists.php?error=in_use'); exit;
            }
            $pdo->prepare("DELETE FROM manufacturers WHERE id = ?")->execute([$id]);
        }
        header('Location: lists.php?deleted=1'); exit;
    }
}

// ── Load data ──────────────────────────────────────────────────────────────

$sections      = $pdo->query("SELECT * FROM sections      ORDER BY sort_order, id")->fetchAll();
$categories    = $pdo->query("SELECT * FROM categories    ORDER BY sort_order, id")->fetchAll();
$manufacturers = $pdo->query("SELECT * FROM manufacturers ORDER BY sort_order, id")->fetchAll();

// ── Edit mode targets ──────────────────────────────────────────────────────

$edit_section_id      = isset($_GET['edit_section'])      ? (int)$_GET['edit_section']      : 0;
$edit_category_id     = isset($_GET['edit_category'])     ? (int)$_GET['edit_category']     : 0;
$edit_manufacturer_id = isset($_GET['edit_manufacturer']) ? (int)$_GET['edit_manufacturer'] : 0;

// ── Toast ──────────────────────────────────────────────────────────────────

$toast_msg   = '';
$toast_error = '';
if (isset($_GET['saved']))   $toast_msg   = 'Mentve!';
if (isset($_GET['deleted'])) $toast_msg   = 'Törölve.';
if (isset($_GET['error']) && $_GET['error'] === 'in_use') {
    $toast_error = 'Nem törölhető: gépek használják.';
}

$page_title = 'Listák';
include __DIR__ . '/includes/header.php';
?>

<style>
  .lists-stack { display: flex; flex-direction: column; gap: 24px; max-width: 760px; }
  .edit-inline-form {
    padding: 16px 22px;
    border-bottom: 1px solid var(--border);
    background: var(--elevated);
  }
  .edit-inline-form .form-row {
    display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;
  }
  .edit-inline-form .form-row .form-group { flex: 1; min-width: 130px; }
  .edit-inline-form .form-row .form-actions { display: flex; gap: 8px; align-items: flex-end; padding-bottom: 1px; }
  .add-row {
    padding: 14px 22px;
    border-top: 1px solid var(--border);
    background: transparent;
  }
  .add-row .form-row {
    display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;
  }
  .add-row .form-row .form-group { flex: 1; min-width: 130px; }
  .add-row .form-row .form-actions { display: flex; align-items: flex-end; padding-bottom: 1px; }
  .code-slug {
    font-family: ui-monospace, 'Cascadia Code', Consolas, monospace;
    font-size: 12px;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--border);
    border-radius: 4px;
    padding: 2px 6px;
    color: var(--muted);
  }
</style>

<div class="lists-stack">

  <!-- ══════════════════════════════════════════════════════ SECTIONS CARD -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Szekciók</span>
      <span class="pill pill-slate"><?= count($sections) ?></span>
    </div>

    <?php if ($edit_section_id): ?>
    <?php $es = array_values(array_filter($sections, fn($r) => $r['id'] === $edit_section_id))[0] ?? null; ?>
    <?php if ($es): ?>
    <div class="edit-inline-form">
      <div style="font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--subtle);margin-bottom:10px;">Szerkesztés</div>
      <form method="POST">
        <input type="hidden" name="action" value="edit_section">
        <input type="hidden" name="id" value="<?= $es['id'] ?>">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" value="<?= htmlspecialchars($es['slug']) ?>" required class="form-input" placeholder="pl. uj-szekció">
          </div>
          <div class="form-group">
            <label class="form-label">Megnevezés</label>
            <input type="text" name="label" value="<?= htmlspecialchars($es['label']) ?>" required class="form-input" placeholder="Megnevezés">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary">Mentés</button>
            <a href="lists.php" class="btn-secondary">Mégse</a>
          </div>
        </div>
      </form>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if (empty($sections)): ?>
    <div class="empty-state">Nincs szekció.</div>
    <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>Slug</th>
          <th>Megnevezés</th>
          <th style="text-align:right;">Műveletek</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($sections as $s): ?>
        <tr>
          <td><code class="code-slug"><?= htmlspecialchars($s['slug']) ?></code></td>
          <td style="color:var(--text);"><?= htmlspecialchars($s['label']) ?></td>
          <td style="text-align:right;">
            <div style="display:flex;gap:6px;justify-content:flex-end;">
              <a href="lists.php?edit_section=<?= $s['id'] ?>" class="btn-ghost">Szerkesztés</a>
              <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="delete_section">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button type="submit" class="btn-danger-ghost"
                  onclick="return confirm('Biztosan törlöd?')">Törlés</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

    <div class="add-row">
      <form method="POST">
        <input type="hidden" name="action" value="add_section">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" required class="form-input" placeholder="pl. uj-szekció">
          </div>
          <div class="form-group">
            <label class="form-label">Megnevezés</label>
            <input type="text" name="label" required class="form-input" placeholder="Megnevezés">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary">Hozzáadás</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ════════════════════════════════════════════════════ CATEGORIES CARD -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Kategóriák</span>
      <span class="pill pill-slate"><?= count($categories) ?></span>
    </div>

    <?php if ($edit_category_id): ?>
    <?php $ec = array_values(array_filter($categories, fn($r) => $r['id'] === $edit_category_id))[0] ?? null; ?>
    <?php if ($ec): ?>
    <div class="edit-inline-form">
      <div style="font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--subtle);margin-bottom:10px;">Szerkesztés</div>
      <form method="POST">
        <input type="hidden" name="action" value="edit_category">
        <input type="hidden" name="id" value="<?= $ec['id'] ?>">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" value="<?= htmlspecialchars($ec['slug']) ?>" required class="form-input" placeholder="pl. uj-kategoria">
          </div>
          <div class="form-group">
            <label class="form-label">Megnevezés</label>
            <input type="text" name="label" value="<?= htmlspecialchars($ec['label']) ?>" required class="form-input" placeholder="Megnevezés">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary">Mentés</button>
            <a href="lists.php" class="btn-secondary">Mégse</a>
          </div>
        </div>
      </form>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if (empty($categories)): ?>
    <div class="empty-state">Nincs kategória.</div>
    <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>Slug</th>
          <th>Megnevezés</th>
          <th style="text-align:right;">Műveletek</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $c): ?>
        <tr>
          <td><code class="code-slug"><?= htmlspecialchars($c['slug']) ?></code></td>
          <td style="color:var(--text);"><?= htmlspecialchars($c['label']) ?></td>
          <td style="text-align:right;">
            <div style="display:flex;gap:6px;justify-content:flex-end;">
              <a href="lists.php?edit_category=<?= $c['id'] ?>" class="btn-ghost">Szerkesztés</a>
              <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="delete_category">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button type="submit" class="btn-danger-ghost"
                  onclick="return confirm('Biztosan törlöd?')">Törlés</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

    <div class="add-row">
      <form method="POST">
        <input type="hidden" name="action" value="add_category">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" required class="form-input" placeholder="pl. uj-kategoria">
          </div>
          <div class="form-group">
            <label class="form-label">Megnevezés</label>
            <input type="text" name="label" required class="form-input" placeholder="Megnevezés">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary">Hozzáadás</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ════════════════════════════════════════════════ MANUFACTURERS CARD -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Gyártók</span>
      <span class="pill pill-slate"><?= count($manufacturers) ?></span>
    </div>

    <?php if ($edit_manufacturer_id): ?>
    <?php $em = array_values(array_filter($manufacturers, fn($r) => $r['id'] === $edit_manufacturer_id))[0] ?? null; ?>
    <?php if ($em): ?>
    <div class="edit-inline-form">
      <div style="font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--subtle);margin-bottom:10px;">Szerkesztés</div>
      <form method="POST">
        <input type="hidden" name="action" value="edit_manufacturer">
        <input type="hidden" name="id" value="<?= $em['id'] ?>">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Név</label>
            <input type="text" name="name" value="<?= htmlspecialchars($em['name']) ?>" required class="form-input" placeholder="Gyártó neve">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary">Mentés</button>
            <a href="lists.php" class="btn-secondary">Mégse</a>
          </div>
        </div>
      </form>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if (empty($manufacturers)): ?>
    <div class="empty-state">Nincs gyártó.</div>
    <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>Név</th>
          <th style="text-align:right;">Műveletek</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($manufacturers as $mfr): ?>
        <tr>
          <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($mfr['name']) ?></td>
          <td style="text-align:right;">
            <div style="display:flex;gap:6px;justify-content:flex-end;">
              <a href="lists.php?edit_manufacturer=<?= $mfr['id'] ?>" class="btn-ghost">Szerkesztés</a>
              <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="delete_manufacturer">
                <input type="hidden" name="id" value="<?= $mfr['id'] ?>">
                <button type="submit" class="btn-danger-ghost"
                  onclick="return confirm('Biztosan törlöd?')">Törlés</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

    <div class="add-row">
      <form method="POST">
        <input type="hidden" name="action" value="add_manufacturer">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Név</label>
            <input type="text" name="name" required class="form-input" placeholder="Gyártó neve">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary">Hozzáadás</button>
          </div>
        </div>
      </form>
    </div>
  </div>

</div><!-- /.lists-stack -->

<?php include __DIR__ . '/includes/footer.php'; ?>
