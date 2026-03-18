<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = dirname(__DIR__, 2) . '/cms/config.php';
if (!file_exists($config)) {
    header('Location: ' . str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/', 1) - 1) . 'setup.php');
    exit;
}
require_once $config;

function require_auth(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . rtrim(dirname($_SERVER['PHP_SELF']), '/admin') . '/admin/login.php');
        exit;
    }
}

function current_admin(): array {
    return [
        'id'   => $_SESSION['admin_id']   ?? 0,
        'name' => $_SESSION['admin_name'] ?? 'Admin',
        'email'=> $_SESSION['admin_email']?? '',
    ];
}
