<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

function clear_dispatch_uploads(): void {
    $dir = __DIR__ . '/../assets/dispatch_uploads';
    if (!is_dir($dir)) {
        return;
    }
    foreach (scandir($dir) ?: [] as $name) {
        if ($name === '.' || $name === '..') {
            continue;
        }
        $path = $dir . '/' . $name;
        if (is_file($path)) {
            unlink($path);
        }
    }
}
function normalize_team_name(string $name): string {
    $name = trim($name);
    if ($name === '') {
        return '';
    }
    return substr($name, 0, 64);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$teamName = normalize_team_name($_POST['team'] ?? '');
$conn = db();

$conn->query('DELETE FROM admin_memos');
clear_dispatch_uploads();

if ($teamName === '') {
    echo json_encode(['ok' => true, 'team' => ''], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conn->prepare('SELECT id FROM teams WHERE name = ? LIMIT 1');
$stmt->bind_param('s', $teamName);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(['ok' => true, 'team' => $teamName], JSON_UNESCAPED_UNICODE);
    exit;
}

$teamId = (int) $row['id'];
$stmt = $conn->prepare('DELETE FROM solves WHERE team_id = ?');
$stmt->bind_param('i', $teamId);
$stmt->execute();

$stmt = $conn->prepare('DELETE FROM submissions WHERE team_id = ?');
$stmt->bind_param('i', $teamId);
$stmt->execute();


echo json_encode(['ok' => true, 'team' => $teamName], JSON_UNESCAPED_UNICODE);
