<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

$displayOverrides = [
    'session-shadow' => [
        'category' => '출입증',
        'difficulty' => '초급',
        'summary' => '교육망 출입증에 기록된 보직 값을 확인하고 상위 권한으로 통제소를 통과하십시오.',
    ],
    'blind-notice' => [
        'category' => '공지 검색',
        'difficulty' => '중급',
        'summary' => '공개 게시판의 존재 여부 신호만으로 비공개 작전 메모의 인증 표식을 추적하십시오.',
    ],
    'image-vault' => [
        'category' => '보급창',
        'difficulty' => '상급 전초',
        'summary' => '영상 보급창의 파일명 검문 규칙을 우회해 내부 보관 자료를 열람하십시오.',
    ],
    'admin-memo-chain' => [
        'category' => '검열관',
        'difficulty' => '고급',
        'summary' => '검열관 단말에서 다시 열리는 야전 메모를 이용해 내부 전용 호출까지 연결하십시오.',
    ],
];

$conn = db();
$result = $conn->query(
    'SELECT challenge_id, title, category, difficulty, points, path, summary
     FROM challenges
     WHERE is_active = 1
     ORDER BY sort_order ASC, points ASC, challenge_id ASC'
);

$challenges = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['points'] = (int) $row['points'];
        if (isset($displayOverrides[$row['challenge_id']])) {
            $row = array_merge($row, $displayOverrides[$row['challenge_id']]);
        }
        $challenges[] = $row;
    }
}

echo json_encode(['ok' => true, 'challenges' => $challenges], JSON_UNESCAPED_UNICODE);