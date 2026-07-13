<?php
declare(strict_types=1);
require __DIR__.'/../api/db.php';
$challengeId='internal-supply';
$conn=db();

function is_loopback_client(): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return $ip === '127.0.0.1' || $ip === '::1';
}

if (($_GET['internal'] ?? '') === 'flag') {
    header('Content-Type: text/plain; charset=utf-8');
    if (!is_loopback_client()) {
        http_response_code(403);
        echo "내부 보급망에서만 열람 가능합니다.";
        exit;
    }
    echo get_flag($conn,$challengeId) ?? 'FLAG{missing_flag}';
    exit;
}

$url = $_GET['url'] ?? 'http://example.com/';
$output = '';
$error = '';
if (array_key_exists('url', $_GET)) {
    $url = substr($url, 0, 240);
    $context = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
    $result = @file_get_contents($url, false, $context);
    if ($result === false) {
        $error = '대리 조회 실패.';
    } else {
        $output = substr($result, 0, 1600);
    }
}
?>
<!doctype html><html lang='ko'><head><meta charset='UTF-8'/><meta name='viewport' content='width=device-width, initial-scale=1.0'/><title>내부 보급망 점검기 | EST 전술보안 인트라넷</title><link rel='stylesheet' href='/styles.css?v=military-3'/></head><body><main class='challenge-page'><a class='back-link' href='/'>작전 과제 목록</a><section class='challenge-detail'><div class='section-heading'><span>내부 보급망 점검기</span><small>내부망 요청 / 중상급</small></div><div class='challenge-body'><p class='eyebrow'>375 전과 / 중상급 작전</p><h1>차단된 보급망 대리 조회</h1><p>외부 단말은 내부 보급망 인증 페이지에 접근할 수 없습니다. 대신 작전 서버의 URL 대리 조회 기능이 어느 위치에서 요청을 보내는지 확인하십시오.</p><form class='tool-form' method='get'><label for='url'>조회 대상 URL</label><div class='submit-row compact-row'><input id='url' name='url' value='<?php echo htmlspecialchars($url,ENT_QUOTES,'UTF-8'); ?>' autocomplete='off'/><button class='primary-button' type='submit'>대리 조회</button></div></form><?php if($error): ?><div class='hint-box visible'><p><?php echo htmlspecialchars($error,ENT_QUOTES,'UTF-8'); ?></p></div><?php elseif($output !== ''): ?><pre class='terminal'><?php echo htmlspecialchars($output,ENT_QUOTES,'UTF-8'); ?></pre><?php endif; ?><!-- 소스 힌트: URL은 서버가 대신 file_get_contents로 요청한다. 요청자의 위치가 브라우저가 아니라 작전 서버라는 점을 이용하라. --><!-- 소스 힌트: 내부 전용 경로는 /challenges/internal-supply.php?internal=flag 이며, localhost에서 접근할 때만 열린다. --><form class='submit-row' data-flag-form data-challenge-id='<?php echo $challengeId; ?>'><input name='flag' placeholder='FLAG{...}' autocomplete='off'/><button class='primary-button' type='submit'>보고</button></form></div></section></main><div class='toast' id='toast' role='status' aria-live='polite'></div><script src='/app.js?v=team-required-1'></script></body></html>