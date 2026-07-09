<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

function normalize_team_name(string $name): string {
    return substr(trim($name), 0, 64);
}

function ensure_team(mysqli $conn, string $name): int {
    $stmt = $conn->prepare('INSERT IGNORE INTO teams (name) VALUES (?)');
    $stmt->bind_param('s', $name);
    $stmt->execute();

    $stmt = $conn->prepare('SELECT id FROM teams WHERE name = ? LIMIT 1');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int) ($row['id'] ?? 0);
}

$id = $_POST['id'] ?? '';
$flag = trim($_POST['flag'] ?? '');
$teamName = normalize_team_name($_POST['team'] ?? '');

if ($teamName === '' || strtolower($teamName) === 'you') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'needsTeam' => true, 'message' => '팀명을 먼저 등록하십시오.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$conn = db();
$teamId = ensure_team($conn, $teamName);
$expected = get_flag($conn, $id);
$isCorrect = $expected !== null && hash_equals($expected, $flag);
$correctInt = $isCorrect ? 1 : 0;

$stmt = $conn->prepare('INSERT INTO submissions (team_id, challenge_id, submitted_flag, is_correct) VALUES (?, ?, ?, ?)');
$stmt->bind_param('issi', $teamId, $id, $flag, $correctInt);
$stmt->execute();

if ($isCorrect) {
    $stmt = $conn->prepare('INSERT IGNORE INTO solves (team_id, challenge_id) VALUES (?, ?)');
    $stmt->bind_param('is', $teamId, $id);
    $stmt->execute();
    echo json_encode(['ok' => true, 'team' => $teamName], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['ok' => false, 'team' => $teamName], JSON_UNESCAPED_UNICODE);