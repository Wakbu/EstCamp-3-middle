<?php
declare(strict_types=1);
$challengeId='session-shadow';
$role=$_COOKIE['shadow_role']??'guest';
if(!isset($_COOKIE['shadow_role'])){setcookie('shadow_role','guest',0,'/','',false,false);$role='guest';}
$flag=null;
if($role==='admin'){require __DIR__.'/../api/db.php';$conn=db();$flag=get_flag($conn,$challengeId);}
?>
<!doctype html><html lang='ko'><head><meta charset='UTF-8'/><meta name='viewport' content='width=device-width, initial-scale=1.0'/><title>세션 그림자 | EST 전술보안 인트라넷</title><link rel='stylesheet' href='/styles.css?v=military-3'/></head><body><main class='challenge-page'><a class='back-link' href='/'>작전 과제 목록</a><section class='challenge-detail'><div class='section-heading'><span>세션 그림자</span><small>출입증 / 초급</small></div><div class='challenge-body'><p class='eyebrow'>150 전과 / 초급 작전</p><h1>보이지 않는 계급장</h1><p>교육망 출입증에는 현재 보직이 기록되어 있습니다. 출입 판정에 쓰이는 값을 확인하고 상위 권한으로 통과하십시오.</p><div class='terminal'><span>출입 판정</span><code><?php echo $role==='admin'?'granted':'limited'; ?></code></div><?php if($flag): ?><div class='hint-box visible success-box'><strong>인증 표식</strong><code><?php echo htmlspecialchars($flag,ENT_QUOTES,'UTF-8'); ?></code></div><?php endif; ?><!-- 소스 힌트: shadow_role 쿠키 값을 확인하라. 출입 판정은 이 값을 그대로 신뢰한다. --><!-- 소스 힌트: 상위 권한 문자열은 이 파일 안에서 그대로 비교된다. --><form class='submit-row' data-flag-form data-challenge-id='<?php echo $challengeId; ?>'><input name='flag' placeholder='EST{...}' autocomplete='off'/><button class='primary-button' type='submit'>보고</button></form></div></section></main><div class='toast' id='toast' role='status' aria-live='polite'></div><script src='/app.js?v=team-required-1'></script></body></html>
