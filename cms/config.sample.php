<?php
/**
 * Linardics CMS – Adatbázis konfiguráció
 *
 * Ez egy MINTA fájl. A tényleges config.php-t a setup.php hozza létre automatikusan.
 * A cms/config.php gitignore-ban van – soha ne commitold éles adatokkal!
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'linardics_cms');
define('DB_USER', 'db_user');
define('DB_PASS', 'db_password');

function cms_db(): PDO {
    static $pdo;
    if ($pdo) return $pdo;
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    return $pdo;
}
