<?php
declare(strict_types=1);
require __DIR__ . '/../api/db.php';

$challengeId = 'net-diagnostics';
$target = $_GET['host'] ?? '127.0.0.1';
$output = '';

$flagPath = '/tmp/est-net-diagnostics-flag.txt';
if (!is_file($flagPath)) {
    $conn = db();
    $flag = get_flag($conn, $challengeId) ?? 'EST{missing_flag}';
    file_put_contents($flagPath, $flag . PHP_EOL);
}

if (array_key_exists('host', $_GET)) {
    $target = substr($target, 0, 120);
    $command = 'timeout 3 ping -c 1 ' . $target . ' 2>&1';
    $output = shell_exec($command) ?? '異쒕젰 ?놁쓬.';
}
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>?ㅽ듃?뚰겕 吏꾨떒 | EST ?꾩닠蹂댁븞 ?명듃?쇰꽬</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">?묒쟾 怨쇱젣 紐⑸줉</a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>?ㅽ듃?뚰겕 吏꾨떒</span>
          <small>紐낅졊??二쇱엯</small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">350 ?꾧낵 / 以묒긽湲??묒쟾</p>
          <h1>?쇱쟾 ?ㅽ듃?뚰겕 ?먭?湲??낆슜</h1>
          <p>?묒쟾 肄섏넄? 遺꾩꽍愿???낅젰???몄뒪?몃줈 媛꾨떒???꾨떖???먭????섑뻾?⑸땲?? ?낅젰媛믪씠 ?쒖뒪??紐낅졊?쇰줈 ?섏뼱媛??吏?먯쓣 李얠븘 ?쒕쾭??蹂닿???吏꾨떒 ?쒖떇???뚯닔?섏떗?쒖삤.</p>

          <form class="tool-form" method="get">
            <label for="host">?몄뒪???먮뒗 IP 二쇱냼</label>
            <div class="submit-row compact-row">
              <input id="host" name="host" value="<?php echo htmlspecialchars($target, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off" />
              <button class="primary-button" type="submit">?먭? ?ㅽ뻾</button>
            </div>
          </form>

          <?php if ($output !== ''): ?>
            <pre class="terminal"><?php echo htmlspecialchars($output, ENT_QUOTES, 'UTF-8'); ?></pre>
          <?php endif; ?>

                    <!-- 작전 단서: host 입력은 고정 ping 명령 뒤에 붙는다. 셸 구분자를 넣었을 때 출력 변화를 보라. -->
          <!-- 작전 단서: 진단 표식은 /tmp/est-net-diagnostics-flag.txt 에 기록된다. -->
          <form class="submit-row" data-flag-form data-challenge-id="<?php echo $challengeId; ?>">
            <input name="flag" placeholder="EST{...}" autocomplete="off" />
            <button class="primary-button" type="submit">蹂닿퀬</button>
          </form>
        </div>
      </section>
    </main>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
    <script src="/app.js?v=team-required-1"></script>
  </body>
</html>
