<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

$conn = db();
if (($_COOKIE['role'] ?? '') === 'admin') {
    $flag = get_flag($conn, 'cookie-role');
    echo json_encode(['ok' => true, 'message' => "Admin role accepted: {$flag}"]);
    exit;
}

$role = $_COOKIE['role'] ?? 'user';
echo json_encode(['ok' => false, 'message' => "Current role={$role}. Elevated access is required."]);
