<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

function normalize_team_name(string $name): string {
    $name = trim($name);
    if ($name === '') {
        return 'you';
    }
    return substr($name, 0, 64);
}

function find_team_id(mysqli $conn, string $name): int {
    $stmt = $conn->prepare('SELECT id FROM teams WHERE name = ? LIMIT 1');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int) ($row['id'] ?? 0);
}

function solved_for_team(mysqli $conn, int $teamId): array {
    if ($teamId <= 0) {
        return [];
    }
    $stmt = $conn->prepare('SELECT challenge_id FROM solves WHERE team_id = ? ORDER BY solved_at ASC');
    $stmt->bind_param('i', $teamId);
    $stmt->execute();
    $result = $stmt->get_result();
    $solved = [];
    while ($row = $result->fetch_assoc()) {
        $solved[] = $row['challenge_id'];
    }
    return $solved;
}

function scoreboard(mysqli $conn): array {
    $sql = 'SELECT t.name, COUNT(s.challenge_id) AS solved, COALESCE(SUM(c.points), 0) AS score
            FROM teams t
            LEFT JOIN solves s ON s.team_id = t.id
            LEFT JOIN challenges c ON c.challenge_id = s.challenge_id
            GROUP BY t.id, t.name
            ORDER BY score DESC, solved DESC, MIN(s.solved_at) ASC, t.name ASC
            LIMIT 20';
    $result = $conn->query($sql);
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = [
                'name' => $row['name'],
                'solved' => (int) $row['solved'],
                'score' => (int) $row['score'],
            ];
        }
    }
    return $rows;
}

$conn = db();
$teamName = normalize_team_name($_GET['team'] ?? 'you');
$teamId = find_team_id($conn, $teamName);

echo json_encode([
    'ok' => true,
    'team' => $teamName,
    'solved' => solved_for_team($conn, $teamId),
    'scoreboard' => scoreboard($conn),
], JSON_UNESCAPED_UNICODE);