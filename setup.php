<?php
/**
 * Linardics CMS – Telepítő varázsló
 * FONTOS: Töröld ezt a fájlt a telepítés után!
 */

define('CONFIG_FILE', __DIR__ . '/cms/config.php');
define('MACHINES_IMG_DIR', __DIR__ . '/assets/images/machines/');

$errors  = [];
$success = false;
$db_test = false;

if (!is_dir(__DIR__ . '/cms')) {
    mkdir(__DIR__ . '/cms', 0755, true);
}
if (!is_dir(MACHINES_IMG_DIR)) {
    mkdir(MACHINES_IMG_DIR, 0755, true);
}

/* ── SEED DATA ─────────────────────────────────────────────────── */

function get_machines(): array {
    return [
        /* ── TRUMPF LÉZERVÁGÓK ── */
        [
            'name'             => 'TruLaser 3030 Fiber',
            'manufacturer'     => 'TRUMPF',
            'category'         => 'lezervago',
            'category_label'   => 'Fő gép · Fiber lézer',
            'badge'            => '5 kW',
            'section'          => 'trulaser',
            'short_description'=> 'Nagy teljesítményű fiber lézervágó alacsony és közepes vastagságú lemezekhez. Kiváló vágásfény és szélsebesség acél, rozsdamentes és alumínium anyagokon.',
            'is_featured'      => 0,
            'sort_order'       => 1,
            'specs' => [
                ['Munkaterület',           '3000 × 1500 mm'],
                ['Lézerforrás',            'Fiber, 5000 W'],
                ['Max. vastagság (acél)',  '25 mm'],
                ['Max. vastagság (Al)',    '20 mm'],
                ['Max. vastagság (rozsd.)','20 mm'],
                ['Pozicionálási pontosság','±0,05 mm'],
            ],
        ],
        [
            'name'             => 'TruLaser 5030 TLF',
            'manufacturer'     => 'TRUMPF',
            'category'         => 'lezervago',
            'category_label'   => 'CO₂ lézer',
            'badge'            => '6 kW',
            'section'          => 'trulaser',
            'short_description'=> 'Nagyteljesítményű CO₂ lézervágó vastagabb lemezekhez és különleges anyagokhoz. Kivételesen sima vágófelület, kiváló minőség vastag acélokon.',
            'is_featured'      => 0,
            'sort_order'       => 2,
            'specs' => [
                ['Munkaterület',           '3000 × 1500 mm'],
                ['Lézerforrás',            'CO₂, 6000 W TLF'],
                ['Max. vastagság (acél)',  '32 mm'],
                ['Max. vastagság (Al)',    '25 mm'],
                ['Max. vastagság (rozsd.)','25 mm'],
                ['Pozicionálási pontosság','±0,05 mm'],
            ],
        ],
        [
            'name'             => 'TruMatic 7000',
            'manufacturer'     => 'TRUMPF',
            'category'         => 'lezervago',
            'category_label'   => 'Kombigép · Vágás + Lyukasztás',
            'badge'            => 'Kombi',
            'section'          => 'trulaser',
            'short_description'=> 'Lézervágás és lyukasztás egyetlen gépen. Komplex lemezalkatrészek gyártásához ideális – kombinált megmunkálással csökkentett átfutási idő, kevesebb átállás.',
            'is_featured'      => 0,
            'sort_order'       => 3,
            'specs' => [
                ['Munkaterület',   '2500 × 1250 mm'],
                ['Lézerforrás',    'CO₂, 4000 W'],
                ['Lyukasztóerő',   '220 kN'],
                ['Max. vastagság', '6,4 mm (acél)'],
                ['Szerszámtár',    'automata, 25 állás'],
            ],
        ],
        [
            'name'             => 'TruLaser Tube 5000',
            'manufacturer'     => 'TRUMPF',
            'category'         => 'lezervago',
            'category_label'   => 'Cső- és profillézer',
            'badge'            => 'Cső',
            'section'          => 'trulaser',
            'short_description'=> 'Csövek és profilok precíziós lézervágása. Kör-, szögletes és nyitott profilok megmunkálása rövid átfutási idővel, minimális utómunkával.',
            'is_featured'      => 0,
            'sort_order'       => 4,
            'specs' => [
                ['Max. csőátmérő',     'Ø152 mm'],
                ['Max. hossz',         '6500 mm'],
                ['Lézerforrás',        'Fiber, 3000 W'],
                ['Falvastagság',       'max. 8 mm (acél)'],
                ['Forgatási tengely',  'NC vezérelt, 360°'],
            ],
        ],
        /* ── TRUMPF HAJLÍTÓK ── */
        [
            'name'             => 'TruBend Center 7030',
            'manufacturer'     => 'TRUMPF',
            'category'         => 'hajlito',
            'category_label'   => 'Automatizált hajlítócella · Kiemelt gép',
            'badge'            => '',
            'section'          => 'trubend',
            'short_description'=> 'Automatizált hajlítócella robotos anyagkezeléssel. Nagy sorozatok hatékony gyártásához – minimális operátori beavatkozással, maximális ismételhetőséggel. Az üzem zászlósgépe.',
            'is_featured'      => 1,
            'sort_order'       => 5,
            'specs' => [
                ['Hajlítóhossz',   '3000 mm'],
                ['Hajlítóerő',     '100 t'],
                ['Tengelyek',      '7 tengely (CNC)'],
                ['Max. vastagság', '6 mm acél'],
                ['Robot',          'automata adagoló'],
            ],
        ],
        [
            'name'             => 'TruBend 5130',
            'manufacturer'     => 'TRUMPF',
            'category'         => 'hajlito',
            'category_label'   => 'Élhajlítóprés',
            'badge'            => '×2',
            'section'          => 'trubend',
            'short_description'=> 'Nagy pontosságú CNC élhajlítóprés lemezmegmunkáláshoz. Kétpéldányos gép, párhuzamos termelési kapacitást biztosít.',
            'is_featured'      => 0,
            'sort_order'       => 6,
            'specs' => [
                ['Hajlítóhossz', '3000 mm'],
                ['Hajlítóerő',   '130 t'],
                ['Tengelyek',    '6+1 CNC'],
                ['Szögismétlés', '±0,1°'],
            ],
        ],
        [
            'name'             => 'TruBend 3100',
            'manufacturer'     => 'TRUMPF',
            'category'         => 'hajlito',
            'category_label'   => 'Élhajlítóprés',
            'badge'            => '×2',
            'section'          => 'trubend',
            'short_description'=> 'Megbízható CNC élhajlítóprés változatos lemezmegmunkálási feladatokhoz. Kétpéldányos elrendezés rugalmas kapacitást biztosít.',
            'is_featured'      => 0,
            'sort_order'       => 7,
            'specs' => [
                ['Hajlítóhossz', '3100 mm'],
                ['Hajlítóerő',   '100 t'],
                ['Tengelyek',    '4 CNC'],
                ['Szögismétlés', '±0,2°'],
            ],
        ],
        [
            'name'             => 'TruBend 7036',
            'manufacturer'     => 'TRUMPF',
            'category'         => 'hajlito',
            'category_label'   => 'Robusztus élhajlítóprés',
            'badge'            => '',
            'section'          => 'trubend',
            'short_description'=> 'Nehézipari élhajlítóprés nagy vastagságú lemezekhez. A széles munkaterület és magas hajlítóerő vastagabb anyagok megmunkálását teszi lehetővé.',
            'is_featured'      => 0,
            'sort_order'       => 8,
            'specs' => [
                ['Hajlítóhossz',   '3600 mm'],
                ['Hajlítóerő',     '360 t'],
                ['Tengelyek',      '6 CNC'],
                ['Max. vastagság', '25 mm acél'],
            ],
        ],
        /* ── AMADA HAJLÍTÓK ── */
        [
            'name'             => 'AMADA IT 1250/20t',
            'manufacturer'     => 'AMADA',
            'category'         => 'hajlito',
            'category_label'   => 'Elektromos szervó prés',
            'badge'            => '20t',
            'section'          => 'amada',
            'short_description'=> 'Precíziós elektromos szervóhajtású élhajlítóprés kis és közepes méretű alkatrészekhez, rendkívül pontos szögismétléssel.',
            'is_featured'      => 0,
            'sort_order'       => 9,
            'specs' => [
                ['Hajlítóhossz',   '1250 mm'],
                ['Hajlítóerő',     '20 t'],
                ['Tengelyek',      '4 CNC tengely'],
                ['Szögpontosság',  '±0,1°'],
                ['Max. vastagság', '3 mm acél'],
            ],
        ],
        [
            'name'             => 'AMADA HFE 3000/100t',
            'manufacturer'     => 'AMADA',
            'category'         => 'hajlito',
            'category_label'   => 'Szinkronprés',
            'badge'            => '100t',
            'section'          => 'amada',
            'short_description'=> 'Szinkronizált hidraulikus élhajlítóprés széles munkaterülettel, közepes vastagságú lemezek megmunkálásához.',
            'is_featured'      => 0,
            'sort_order'       => 10,
            'specs' => [
                ['Hajlítóhossz',   '3000 mm'],
                ['Hajlítóerő',     '100 t'],
                ['Tengelyek',      '7 CNC tengely'],
                ['Szögpontosság',  '±0,2°'],
                ['Max. vastagság', '10 mm acél'],
            ],
        ],
        [
            'name'             => 'AMADA HFE 3000/130Lt',
            'manufacturer'     => 'AMADA',
            'category'         => 'hajlito',
            'category_label'   => 'Nagy teljesítményű szinkronprés',
            'badge'            => '130t',
            'section'          => 'amada',
            'short_description'=> 'Nagy teljesítményű hibrid szervóhajtású szinkronprés vastag lemezek precíziós hajlításához.',
            'is_featured'      => 0,
            'sort_order'       => 11,
            'specs' => [
                ['Hajlítóhossz', '3000 mm'],
                ['Hajlítóerő',   '130 t'],
                ['Tengelyek',    '7 CNC tengely'],
                ['Szögpontosság','±0,2°'],
                ['Hajtás',       'hibrid szervó'],
            ],
        ],
        [
            'name'             => 'AMADA HFP 4000/220t',
            'manufacturer'     => 'AMADA',
            'category'         => 'hajlito',
            'category_label'   => 'Nehézipari prés · 4 méteres',
            'badge'            => '220t',
            'section'          => 'amada',
            'short_description'=> 'Nehézipari 4 méteres szinkronprés nagy sorozatok és vastag lemezek hajlításához, kiemelkedő hajlítóerővel.',
            'is_featured'      => 0,
            'sort_order'       => 12,
            'specs' => [
                ['Hajlítóhossz',   '4000 mm'],
                ['Hajlítóerő',     '220 t'],
                ['Tengelyek',      '7 CNC tengely'],
                ['Max. vastagság', '20 mm acél'],
                ['Különlegesség',  '4000 mm géphossz'],
            ],
        ],
        /* ── CSŐHAJLÍTÁS + EGYÉB ── */
        [
            'name'             => 'SOCO SB-52X10A',
            'manufacturer'     => 'SOCO',
            'category'         => 'csohajlito',
            'category_label'   => 'CNC Csőhajlító',
            'badge'            => '',
            'section'          => 'egyeb',
            'short_description'=> 'CNC csőhajlító kör- és szögletes profilokhoz, rugalmas és precíz csőmegmunkálás acél, rozsdamentes, alumínium és réz anyagokon.',
            'is_featured'      => 0,
            'sort_order'       => 13,
            'specs' => [
                ['Max. csőátmérő',      'Ø50,8 mm'],
                ['Max. csőhossz',       '4500 mm'],
                ['Hajlítás iránya',     'kétirányú CNC'],
                ['Max. hajlítási szög', '190°'],
                ['Anyagok',             'acél, rozsd., Al, réz'],
            ],
        ],
        [
            'name'             => 'Gema rendszer',
            'manufacturer'     => 'Gema',
            'category'         => 'porfesto',
            'category_label'   => 'Porfestő sor',
            'badge'            => '',
            'section'          => 'egyeb',
            'short_description'=> 'Automata porfestő sor teljes RAL palettával, foszfátozásos előkezeléssel. Nagy méretű alkatrészek felületkezelésére alkalmas.',
            'is_featured'      => 0,
            'sort_order'       => 14,
            'specs' => [
                ['Kemence hossz',  '7500 mm'],
                ['Max. alkatrész', '800×1600×2200 mm'],
                ['Fülkék száma',   '3 db'],
                ['Előkezelés',     'foszfátozás'],
                ['Szín',           'teljes RAL paletta'],
            ],
        ],
        [
            'name'             => 'Wagner rendszer',
            'manufacturer'     => 'Wagner',
            'category'         => 'porfesto',
            'category_label'   => 'Porfestő sor 2',
            'badge'            => '',
            'section'          => 'egyeb',
            'short_description'=> 'Kompakt porfestő sor kis- és közepes méretű alkatrészek gyors felületkezeléséhez, teljes RAL szín palettával.',
            'is_featured'      => 0,
            'sort_order'       => 15,
            'specs' => [
                ['Kemence hossz', '3500 mm'],
                ['Fülkék száma',  '2 db'],
                ['Típus',         'kis- és közepes részek'],
                ['Hőmérséklet',   '180–200 °C'],
            ],
        ],
        /* ── KÖTÉSTECHNIKA ── */
        [
            'name'             => 'STEELINE BSM1100',
            'manufacturer'     => 'STEELINE',
            'category'         => 'kotestechnika',
            'category_label'   => 'Sávcsiszoló',
            'badge'            => '',
            'section'          => 'kotestechnika',
            'short_description'=> '1100 mm munkalemez-szél',
            'is_featured'      => 0,
            'sort_order'       => 16,
            'specs'            => [],
        ],
        [
            'name'             => 'VS-W ECO-I',
            'manufacturer'     => 'VS',
            'category'         => 'kotestechnika',
            'category_label'   => 'Hegesztő ×2',
            'badge'            => '',
            'section'          => 'kotestechnika',
            'short_description'=> 'MIG/MAG hegesztés',
            'is_featured'      => 0,
            'sort_order'       => 17,
            'specs'            => [],
        ],
        [
            'name'             => 'SOYER BMS9',
            'manufacturer'     => 'SOYER',
            'category'         => 'kotestechnika',
            'category_label'   => 'Kondenzátoros hegesztő',
            'badge'            => '',
            'section'          => 'kotestechnika',
            'short_description'=> 'Csavaranyás hegesztés',
            'is_featured'      => 0,
            'sort_order'       => 18,
            'specs'            => [],
        ],
        [
            'name'             => 'PEMSERTER Series 4',
            'manufacturer'     => 'PEMSERTER',
            'category'         => 'kotestechnika',
            'category_label'   => 'Beültetőgép',
            'badge'            => '',
            'section'          => 'kotestechnika',
            'short_description'=> 'Menetbetét beültetés',
            'is_featured'      => 0,
            'sort_order'       => 19,
            'specs'            => [],
        ],
    ];
}

function get_page_settings(): array {
    return [
        ['geppark_title',          'Géppark',                   'text',     'geppark'],
        ['geppark_eyebrow',        'Prémium technológia',       'text',     'geppark'],
        ['geppark_intro',          'TRUMPF és AMADA gépek – a világ vezető ipari márkái garantálják a pontosságot és ismételhetőséget minden alkatrészen. 15+ géppel, 3 gyártócsarnokban, folyamatosan bővülő kapacitással.', 'textarea', 'geppark'],
        ['geppark_badge_1',        'TRUMPF lézervágók – 4 gép', 'text',     'geppark'],
        ['geppark_badge_2',        'TRUMPF hajlítók – 6 gép',   'text',     'geppark'],
        ['geppark_badge_3',        'AMADA hajlítók – 4 gép',    'text',     'geppark'],
        ['geppark_badge_4',        'Porfestés + csőhajlítás',   'text',     'geppark'],
        ['trulaser_eyebrow',       'Lézervágás',                'text',     'geppark'],
        ['trulaser_title',         'TRUMPF Lézervágók',         'text',     'geppark'],
        ['trubend_eyebrow',        'Lemezhajlítás',             'text',     'geppark'],
        ['trubend_title',          'TRUMPF Hajlítók',           'text',     'geppark'],
        ['amada_eyebrow',          'Élhajlítás',                'text',     'geppark'],
        ['amada_title',            'AMADA Hajlítók',            'text',     'geppark'],
        ['egyeb_eyebrow',          'Kiegészítő technológiák',   'text',     'geppark'],
        ['egyeb_title',            'Csőhajlítás &amp; Felületkezelés', 'text', 'geppark'],
    ];
}

/* ── SQL ────────────────────────────────────────────────────────── */

function get_create_tables_sql(): string {
    return "
    CREATE TABLE IF NOT EXISTS `machines` (
        `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `name`              VARCHAR(255) NOT NULL,
        `manufacturer`      VARCHAR(100) DEFAULT '',
        `category`          VARCHAR(100) DEFAULT '',
        `category_label`    VARCHAR(100) DEFAULT '',
        `badge`             VARCHAR(50)  DEFAULT '',
        `section`           VARCHAR(50)  NOT NULL DEFAULT 'egyeb',
        `short_description` TEXT,
        `image`             VARCHAR(255) DEFAULT NULL,
        `sort_order`        INT          DEFAULT 0,
        `is_active`         TINYINT(1)   DEFAULT 1,
        `is_featured`       TINYINT(1)   DEFAULT 0,
        `created_at`        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        `updated_at`        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE IF NOT EXISTS `machine_specs` (
        `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `machine_id` INT UNSIGNED NOT NULL,
        `spec_key`   VARCHAR(100) DEFAULT '',
        `spec_value` VARCHAR(255) DEFAULT '',
        `sort_order` INT          DEFAULT 0,
        FOREIGN KEY (`machine_id`) REFERENCES `machines`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE IF NOT EXISTS `page_settings` (
        `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `setting_key`   VARCHAR(100) UNIQUE NOT NULL,
        `setting_value` TEXT,
        `setting_type`  VARCHAR(50)  DEFAULT 'text',
        `setting_group` VARCHAR(50)  DEFAULT 'geppark'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE IF NOT EXISTS `admin_users` (
        `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `name`       VARCHAR(255) DEFAULT '',
        `email`      VARCHAR(255) UNIQUE NOT NULL,
        `password`   VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
}

/* ── SEED ───────────────────────────────────────────────────────── */

function seed_machines(PDO $pdo): void {
    $machines = get_machines();
    $stmt_m = $pdo->prepare("
        INSERT INTO machines (name, manufacturer, category, category_label, badge, section,
            short_description, sort_order, is_active, is_featured)
        VALUES (:name, :manufacturer, :category, :category_label, :badge, :section,
            :short_description, :sort_order, 1, :is_featured)
    ");
    $stmt_s = $pdo->prepare("
        INSERT INTO machine_specs (machine_id, spec_key, spec_value, sort_order)
        VALUES (:machine_id, :spec_key, :spec_value, :sort_order)
    ");

    foreach ($machines as $m) {
        $stmt_m->execute([
            ':name'              => $m['name'],
            ':manufacturer'      => $m['manufacturer'],
            ':category'          => $m['category'],
            ':category_label'    => $m['category_label'],
            ':badge'             => $m['badge'],
            ':section'           => $m['section'],
            ':short_description' => $m['short_description'],
            ':sort_order'        => $m['sort_order'],
            ':is_featured'       => $m['is_featured'],
        ]);
        $machine_id = $pdo->lastInsertId();
        foreach ($m['specs'] as $i => [$key, $val]) {
            $stmt_s->execute([
                ':machine_id' => $machine_id,
                ':spec_key'   => $key,
                ':spec_value' => $val,
                ':sort_order' => $i + 1,
            ]);
        }
    }
}

function seed_settings(PDO $pdo): void {
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO page_settings (setting_key, setting_value, setting_type, setting_group)
        VALUES (:k, :v, :t, :g)
    ");
    foreach (get_page_settings() as [$k, $v, $t, $g]) {
        $stmt->execute([':k' => $k, ':v' => $v, ':t' => $t, ':g' => $g]);
    }
}

function write_config(string $host, string $name, string $user, string $pass): void {
    $content = '<?php' . PHP_EOL
        . "define('DB_HOST', " . var_export($host, true) . ");" . PHP_EOL
        . "define('DB_NAME', " . var_export($name, true) . ");" . PHP_EOL
        . "define('DB_USER', " . var_export($user, true) . ");" . PHP_EOL
        . "define('DB_PASS', " . var_export($pass, true) . ");" . PHP_EOL
        . PHP_EOL
        . 'function cms_db(): PDO {' . PHP_EOL
        . '    static $pdo;' . PHP_EOL
        . '    if ($pdo) return $pdo;' . PHP_EOL
        . '    $pdo = new PDO(' . PHP_EOL
        . '        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",' . PHP_EOL
        . '        DB_USER, DB_PASS,' . PHP_EOL
        . '        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]' . PHP_EOL
        . '    );' . PHP_EOL
        . '    return $pdo;' . PHP_EOL
        . '}' . PHP_EOL;
    file_put_contents(CONFIG_FILE, $content);
}

/* ── PROCESS FORM ───────────────────────────────────────────────── */

$log = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host     = trim($_POST['db_host']     ?? 'localhost');
    $db_name     = trim($_POST['db_name']     ?? '');
    $db_user     = trim($_POST['db_user']     ?? '');
    $db_pass     =       $_POST['db_pass']    ?? '';
    $admin_name  = trim($_POST['admin_name']  ?? 'Admin');
    $admin_email = trim($_POST['admin_email'] ?? '');
    $admin_pass  =       $_POST['admin_pass'] ?? '';

    if (empty($db_name))  $errors[] = 'Az adatbázis neve kötelező.';
    if (empty($db_user))  $errors[] = 'Az adatbázis felhasználónév kötelező.';
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Érvényes admin e-mail cím szükséges.';
    if (strlen($admin_pass) < 8)                          $errors[] = 'Az admin jelszó legalább 8 karakter legyen.';

    if (empty($errors)) {
        try {
            // Connect without DB name first so we can create it
            $pdo = new PDO(
                "mysql:host={$db_host};charset=utf8mb4",
                $db_user, $db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $log[] = ['ok', 'Adatbázis-kiszolgálóhoz csatlakozva.'];

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$db_name}`");
            $log[] = ['ok', "Adatbázis létrehozva / megnyitva: <strong>{$db_name}</strong>"];

            foreach (array_filter(array_map('trim', explode(';', get_create_tables_sql()))) as $sql) {
                $pdo->exec($sql);
            }
            $log[] = ['ok', 'Táblák létrehozva: <code>machines</code>, <code>machine_specs</code>, <code>page_settings</code>, <code>admin_users</code>'];

            $count = $pdo->query("SELECT COUNT(*) FROM machines")->fetchColumn();
            if ($count == 0) {
                seed_machines($pdo);
                $log[] = ['ok', '19 gép adatai betöltve (seed).'];
            } else {
                $log[] = ['warn', "Gépek már léteznek ({$count} db) – seed kihagyva."];
            }

            seed_settings($pdo);
            $log[] = ['ok', 'Oldal-beállítások betöltve.'];

            $stmt = $pdo->prepare("INSERT IGNORE INTO admin_users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$admin_name, $admin_email, password_hash($admin_pass, PASSWORD_DEFAULT)]);
            if ($stmt->rowCount() > 0) {
                $log[] = ['ok', "Admin felhasználó létrehozva: <strong>{$admin_email}</strong>"];
            } else {
                $log[] = ['warn', "Admin felhasználó már létezik ({$admin_email}) – kihagyva."];
            }

            write_config($db_host, $db_name, $db_user, $db_pass);
            $log[] = ['ok', 'Konfigurációs fájl megírva: <code>cms/config.php</code>'];

            $success = true;
        } catch (Throwable $e) {
            $errors[] = 'Hiba: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Linardics CMS – Telepítő</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Inter', sans-serif; }
    .font-heading { font-family: 'Barlow Condensed', sans-serif; }
  </style>
</head>
<body class="bg-[#060f1a] text-white min-h-screen flex items-center justify-center py-12 px-4">

<div class="w-full max-w-lg">

  <!-- Header -->
  <div class="text-center mb-10">
    <div class="inline-flex items-center gap-2 bg-[#cc2222]/10 border border-[#cc2222]/30 px-4 py-1.5 mb-6">
      <span class="text-[#cc2222] text-xs font-semibold tracking-widest uppercase">CMS Telepítő</span>
    </div>
    <h1 class="font-heading font-bold text-5xl uppercase tracking-wide text-white mb-2">Linardics CMS</h1>
    <p class="text-white/45 text-sm">Adatbázis létrehozása és kezdeti adatok betöltése</p>
  </div>

  <?php if ($success): ?>
  <!-- SUCCESS -->
  <div class="bg-[#122135] border border-green-500/30 p-6 mb-6">
    <div class="flex items-center gap-3 mb-4">
      <svg class="w-6 h-6 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <h2 class="font-heading font-bold text-xl uppercase tracking-wide text-green-400">Telepítés sikeres!</h2>
    </div>
    <ul class="space-y-2 mb-6">
      <?php foreach ($log as [$type, $msg]): ?>
        <li class="flex items-start gap-2 text-sm">
          <?php if ($type === 'ok'): ?>
            <svg class="w-4 h-4 text-green-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <span class="text-white/70"><?= $msg ?></span>
          <?php else: ?>
            <svg class="w-4 h-4 text-yellow-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span class="text-yellow-300/80"><?= $msg ?></span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="border-t border-white/8 pt-4 space-y-2">
      <p class="text-sm font-semibold text-white/80 mb-3">Következő lépések:</p>
      <a href="admin/login.php" class="flex items-center justify-between bg-[#cc2222] hover:bg-[#a01818] px-5 py-3 text-white font-semibold text-sm uppercase tracking-wider transition-colors">
        <span>Admin felület megnyitása</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
      <a href="geppark.php" class="flex items-center justify-between bg-[#122135] border border-white/10 hover:border-white/20 px-5 py-3 text-white/70 hover:text-white text-sm uppercase tracking-wider transition-colors">
        <span>Géppark oldal megtekintése</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>
    <p class="mt-5 text-xs text-[#cc2222] font-semibold">⚠ Töröld a <code>setup.php</code> fájlt a szerverről!</p>
  </div>

  <?php else: ?>

  <!-- ERRORS -->
  <?php if (!empty($errors)): ?>
  <div class="bg-red-900/20 border border-red-500/30 p-4 mb-6">
    <ul class="space-y-1">
      <?php foreach ($errors as $e): ?>
        <li class="text-red-300 text-sm flex items-start gap-2">
          <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
          <?= htmlspecialchars($e) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <!-- FORM -->
  <form method="POST" class="space-y-6">
    <!-- DB config -->
    <div class="bg-[#0d1b2a] border border-white/8 p-6 space-y-4">
      <h2 class="font-heading font-bold text-lg uppercase tracking-widest text-white/80 mb-4">Adatbázis-kapcsolat</h2>

      <div>
        <label class="block text-xs text-white/50 uppercase tracking-widest mb-1.5">DB Host</label>
        <input type="text" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>"
          class="w-full bg-[#060f1a] border border-white/10 focus:border-[#cc2222] outline-none px-4 py-2.5 text-sm text-white placeholder-white/20"
          placeholder="localhost">
      </div>
      <div>
        <label class="block text-xs text-white/50 uppercase tracking-widest mb-1.5">Adatbázis neve *</label>
        <input type="text" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? '') ?>" required
          class="w-full bg-[#060f1a] border border-white/10 focus:border-[#cc2222] outline-none px-4 py-2.5 text-sm text-white placeholder-white/20"
          placeholder="linardics_cms">
        <p class="text-white/30 text-xs mt-1">Ha nem létezik, automatikusan létrehozzuk.</p>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs text-white/50 uppercase tracking-widest mb-1.5">DB Felhasználó *</label>
          <input type="text" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>" required
            class="w-full bg-[#060f1a] border border-white/10 focus:border-[#cc2222] outline-none px-4 py-2.5 text-sm text-white placeholder-white/20"
            placeholder="db_user">
        </div>
        <div>
          <label class="block text-xs text-white/50 uppercase tracking-widest mb-1.5">DB Jelszó</label>
          <input type="password" name="db_pass" value=""
            class="w-full bg-[#060f1a] border border-white/10 focus:border-[#cc2222] outline-none px-4 py-2.5 text-sm text-white placeholder-white/20"
            placeholder="••••••••">
        </div>
      </div>
    </div>

    <!-- Admin user -->
    <div class="bg-[#0d1b2a] border border-white/8 p-6 space-y-4">
      <h2 class="font-heading font-bold text-lg uppercase tracking-widest text-white/80 mb-4">Admin Fiók</h2>
      <div>
        <label class="block text-xs text-white/50 uppercase tracking-widest mb-1.5">Teljes név</label>
        <input type="text" name="admin_name" value="<?= htmlspecialchars($_POST['admin_name'] ?? 'Admin') ?>"
          class="w-full bg-[#060f1a] border border-white/10 focus:border-[#cc2222] outline-none px-4 py-2.5 text-sm text-white"
          placeholder="Admin">
      </div>
      <div>
        <label class="block text-xs text-white/50 uppercase tracking-widest mb-1.5">E-mail cím *</label>
        <input type="email" name="admin_email" value="<?= htmlspecialchars($_POST['admin_email'] ?? 'admin@linardics.hu') ?>" required
          class="w-full bg-[#060f1a] border border-white/10 focus:border-[#cc2222] outline-none px-4 py-2.5 text-sm text-white"
          placeholder="admin@linardics.hu">
      </div>
      <div>
        <label class="block text-xs text-white/50 uppercase tracking-widest mb-1.5">Jelszó * (min. 8 karakter)</label>
        <input type="password" name="admin_pass" required minlength="8"
          class="w-full bg-[#060f1a] border border-white/10 focus:border-[#cc2222] outline-none px-4 py-2.5 text-sm text-white"
          placeholder="••••••••">
      </div>
    </div>

    <button type="submit"
      class="w-full bg-[#cc2222] hover:bg-[#a01818] text-white font-heading font-bold text-lg uppercase tracking-widest py-4 transition-colors">
      Telepítés indítása
    </button>
  </form>

  <?php endif; ?>

  <p class="text-center text-white/20 text-xs mt-8">Linardics CMS v1.0 · Plain PHP + PDO · Composer-mentes</p>
</div>

</body>
</html>
