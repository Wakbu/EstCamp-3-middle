<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

$displayOverrides = [
    'session-shadow' => [
        'title' => '세션 그림자',
        'category' => '출입증',
        'difficulty' => '초급',
        'summary' => '교육망 출입증에 기록된 보직 값을 확인하고 상위 권한으로 통제소를 통과하십시오.',
    ],
    'blind-notice' => [
        'title' => '비문 공지 검색소',
        'category' => '공지 검색',
        'difficulty' => '중급',
        'summary' => '공개 게시판의 응답 신호를 관찰해 비공개 작전 메모의 인증 표식을 추적하십시오.',
    ],
    'image-vault' => [
        'title' => '영상 보급창',
        'category' => '보급창',
        'difficulty' => '상급 입문',
        'summary' => '영상 보급창의 파일명 검문 규칙을 우회해 내부 보관 자료를 열람하십시오.',
    ],
    'idle-empire' => [
        'title' => '코인 제국 보급 작전',
        'category' => '보급 자산',
        'difficulty' => '중급',
        'summary' => '브라우저 저장소와 최종 보고 요청을 분석해 클라이언트 신뢰 취약점을 확인하십시오.',
    ],
    'admin-memo-chain' => [
        'title' => '관리관 메모 연쇄',
        'category' => '검문소',
        'difficulty' => '고급',
        'summary' => '관리관 메모 체계에서 저장된 작전 메모를 이용해 최종 인증 표식까지 연결하십시오.',
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
