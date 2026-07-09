<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

$conn = db();
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Intentionally vulnerable for this local wargame challenge.
$query = "SELECT id FROM users WHERE username = '{$username}' AND password = '{$password}' LIMIT 1";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $flag = get_flag($conn, 'admin-login');
    echo json_encode(['ok' => true, 'message' => "Login bypass success: {$flag}"]);
    exit;
}

echo json_encode(['ok' => false, 'message' => 'Access denied']);
