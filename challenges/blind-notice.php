<?php
declare(strict_types=1);
require __DIR__.'/../api/db.php';
$challengeId='blind-notice';
$q=$_GET['q']??'';
$searched=array_key_exists('q',$_GET);
$match=false;$error='';
if($searched){$conn=db();$sql='SELECT id FROM blind_notices WHERE is_public = 1 AND title LIKE '.chr(39).'%'.$q.'%'.chr(39).' LIMIT 1';try{$result=$conn->query($sql);$match=$result&&$result->num_rows>0;}catch(Throwable $e){$error='조회 실패.';}}
?>
<!doctype html><html lang='ko'><head><meta charset='UTF-8'/><meta name='viewport' content='width=device-width, initial-scale=1.0'/><title>비문 공지 검색소 | EST 전술보안 인트라넷</title><link rel='stylesheet' href='/styles.css?v=military-3'/></head><body><main class='challenge-page'><a class='back-link' href='/'>작전 과제 목록</a><section class='challenge-detail'><div class='section-heading'><span>비문 공지 검색소</span><small>공지 검색 / 중급</small></div><div class='challenge-body'><p class='eyebrow'>250 전과 / 중급 작전</p><h1>비문 공지 검색소</h1><p>공개 게시판은 공개 공지만 보여주지만 검색 조건에 따라 응답이 달라집니다. 결과 문구의 차이를 관찰해 비공개 메모의 인증 표식을 추적하십시오.</p><form class='tool-form' method='get'><label for='q'>검색 질의</label><div class='submit-row compact-row'><input id='q' name='q' value='<?php echo htmlspecialchars($q,ENT_QUOTES,'UTF-8'); ?>' autocomplete='off'/><button class='primary-button' type='submit'>조회</button></div></form><?php if($searched): ?><div class='hint-box visible <?php echo $match?'success-box':''; ?>'><strong>조회 결과</strong><p><?php echo $error?'조회 오류':($match?'조건에 맞는 공지가 존재합니다.':'조회된 공지가 없습니다.'); ?></p></div><?php endif; ?><!-- 소스 힌트: q 파라미터가 SQL LIKE 조건에 그대로 이어 붙는다. 참/거짓 응답 차이를 관찰하라. --><!-- 소스 힌트: 비밀값을 바로 출력하려 하지 말고 한 글자씩 존재 여부를 확인하라. --><form class='submit-row' data-flag-form data-challenge-id='<?php echo $challengeId; ?>'><input name='flag' placeholder='EST{...}' autocomplete='off'/><button class='primary-button' type='submit'>보고</button></form></div></section></main><div class='toast' id='toast' role='status' aria-live='polite'></div><script src='/app.js?v=team-required-1'></script></body></html>
