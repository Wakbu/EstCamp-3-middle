<?php
declare(strict_types=1);
require __DIR__.'/../api/db.php';
$challengeId='image-vault';
$baseDir=__DIR__.'/../assets/vault';
$requested=$_GET['file']??'cover.png';
$content=null;$message='';
if(!is_dir($baseDir)){mkdir($baseDir,0755,true);}
$flagFile=$baseDir.'/flag.txt';
if(!is_file($flagFile)){$conn=db();$flag=get_flag($conn,$challengeId)??'EST{missing_flag}';file_put_contents($flagFile,$flag);}
$coverFile=$baseDir.'/cover.png';if(!is_file($coverFile)){file_put_contents($coverFile,'PNG preview placeholder'.PHP_EOL);}
$allowedDir=$baseDir.'/allowed.png';if(!is_dir($allowedDir)){mkdir($allowedDir,0755,true);}
if(isset($_GET['file'])){if(strpos($requested,'.png')===false){$message='파일명에 .png가 포함되어야 합니다.';}else{$path=$baseDir.'/'.$requested;if(is_file($path)){$content=file_get_contents($path);}else{$message='파일을 찾을 수 없습니다.';}}}
?>
<!doctype html><html lang='ko'><head><meta charset='UTF-8'/><meta name='viewport' content='width=device-width, initial-scale=1.0'/><title>영상 보급창 | EST 전술보안 인트라넷</title><link rel='stylesheet' href='/styles.css?v=military-3'/></head><body><main class='challenge-page'><a class='back-link' href='/'>작전 과제 목록</a><section class='challenge-detail'><div class='section-heading'><span>영상 보급창</span><small>보급창 / 상급 입문</small></div><div class='challenge-body'><p class='eyebrow'>300 전과 / 상급 입문</p><h1>영상 보급창의 허술한 검문</h1><p>보급창은 파일명에 포함된 표식만 보고 자료 열람을 허가합니다. 필터가 보는 문자열과 서버가 실제로 해석하는 경로의 차이를 이용하십시오.</p><form class='tool-form' method='get'><label for='file'>보급 파일명</label><div class='submit-row compact-row'><input id='file' name='file' value='<?php echo htmlspecialchars($requested,ENT_QUOTES,'UTF-8'); ?>' autocomplete='off'/><button class='primary-button' type='submit'>열람</button></div></form><?php if($content!==null): ?><pre class='terminal'><?php echo htmlspecialchars($content,ENT_QUOTES,'UTF-8'); ?></pre><?php elseif($message): ?><div class='hint-box visible'><p><?php echo htmlspecialchars($message,ENT_QUOTES,'UTF-8'); ?></p></div><?php endif; ?><!-- SOURCE HINT: The filter only checks whether the filename contains .png. Path resolution happens later. --><!-- SOURCE HINT: Keep a string that passes the filter while making the resolved path point elsewhere. --><form class='submit-row' data-flag-form data-challenge-id='<?php echo $challengeId; ?>'><input name='flag' placeholder='EST{...}' autocomplete='off'/><button class='primary-button' type='submit'>보고</button></form></div></section></main><div class='toast' id='toast' role='status' aria-live='polite'></div><script src='/app.js?v=team-required-1'></script></body></html>
