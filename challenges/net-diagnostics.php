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

          <div class="hint-box visible staged-hints">
            <strong>?묒쟾 硫붾え</strong>
            <p>留됲엳硫??쒖꽌?濡??대엺?섏떗?쒖삤.</p>
            <details><summary>1?④퀎</summary><p>癒쇱? ?뺤긽 ?몄뒪?몃? ?ｊ퀬, ???뱀닔臾몄옄瑜??욎뿀????異쒕젰???대뼸寃??щ씪吏?붿? 鍮꾧탳?섏떗?쒖삤.</p></details>
            <details><summary>2?④퀎</summary><p>?쒕쾭 紐낅졊? 怨좎젙??ping ?묐몢???ㅼ뿉 ?낅젰媛믪쓣 遺숈뿬 議곕┰?⑸땲??</p></details>
            <details><summary>3?④퀎</summary><p>?쒖떇? <code>/tmp/est-net-diagnostics-flag.txt</code>??湲곕줉?섏뼱 ?덉뒿?덈떎.</p></details>
          </div>

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
