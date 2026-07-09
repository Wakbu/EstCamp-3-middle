<?php
function db(): mysqli {
    $host = getenv('WARGAME_DB_HOST') ?: 'localhost';
    $user = getenv('WARGAME_DB_USER') ?: 'wargame_app';
    $pass = getenv('WARGAME_DB_PASSWORD');
    $name = getenv('WARGAME_DB_NAME') ?: 'wargame_lab';

    if ($pass === false || $pass === '') {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => 'Database password is not configured']);
        exit;
    }

    $conn = new mysqli($host, $user, $pass, $name);
    if ($conn->connect_error) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => 'Database connection failed']);
        exit;
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
function get_flag(mysqli $conn, string $challengeId): ?string {
    $stmt = $conn->prepare('SELECT flag FROM challenge_flags WHERE challenge_id = ? LIMIT 1');
    $stmt->bind_param('s', $challengeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['flag'] ?? null;
}
