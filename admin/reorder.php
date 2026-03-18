<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$ids   = $input['ids'] ?? [];

if (!is_array($ids)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$pdo  = cms_db();
$stmt = $pdo->prepare("UPDATE machines SET sort_order = ? WHERE id = ?");

foreach ($ids as $order => $id) {
    if (is_numeric($id)) {
        $stmt->execute([$order + 1, (int)$id]);
    }
}

echo json_encode(['ok' => true]);
