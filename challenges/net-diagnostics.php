<?php
declare(strict_types=1);
require __DIR__.'/../api/db.php';
$challengeId='net-diagnostics';
$target=$_GET['host']??'127.0.0.1';
$output='';
$flagPath='/tmp/est-net-diagnostics-flag.txt';
if(!is_file($flagPath)){$conn=db();$flag=get_flag($conn,$challengeId)??'EST{missing_flag}';file_put_contents($flagPath,$flag.PHP_EOL);}
if(array_key_exists('host',$_GET)){$target=substr($target,0,120);$command='timeout 3 ping -c 1 '.$target.' 2>&1';$output=shell_exec($command)??'출력 없음.';}
?>
<!doctype html><html lang='ko'><head><meta charset='UTF-8'/><meta name='viewport' content='width=device-width, initial-scale=1.0'/><title>네트워크 진단 | EST 전술보안 인트라넷</title><link rel='stylesheet' href='/styles.css?v=military-3'/></head><body><main class='challenge-page'><a class='back-link' href='/'>작전 과제 목록</a><section class='challenge-detail'><div class='section-heading'><span>네트워크 진단</span><small>명령어 주입 / 중상급</small></div><div class='challenge-body'><p class='eyebrow'>350 전과 / 중상급 작전</p><h1>야전 네트워크 점검기 악용</h1><p>작전 콘솔은 입력한 호스트로 도달성 점검을 수행합니다. 입력값이 시스템 명령으로 넘어가는 지점을 찾아 서버에 보관된 진단 표식을 회수하십시오.</p><form class='tool-form' method='get'><label for='host'>호스트 또는 IP 주소</label><div class='submit-row compact-row'><input id='host' name='host' value='<?php echo htmlspecialchars($target,ENT_QUOTES,'UTF-8'); ?>' autocomplete='off'/><button class='primary-button' type='submit'>점검 실행</button></div></form><?php if($output!==''): ?><pre class='terminal'><?php echo htmlspecialchars($output,ENT_QUOTES,'UTF-8'); ?></pre><?php endif; ?><!-- 소스 힌트: host 파라미터는 고정된 ping 명령 뒤에 붙는다. 셸 구분자를 시도하라. --><!-- 소스 힌트: 진단 표식은 /tmp/est-net-diagnostics-flag.txt 에 기록된다. --><form class='submit-row' data-flag-form data-challenge-id='<?php echo $challengeId; ?>'><input name='flag' placeholder='EST{...}' autocomplete='off'/><button class='primary-button' type='submit'>보고</button></form></div></section></main><div class='toast' id='toast' role='status' aria-live='polite'></div><script src='/app.js?v=team-required-1'></script></body></html>
