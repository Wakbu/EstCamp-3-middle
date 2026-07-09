<?php
declare(strict_types=1);
require __DIR__ . '/../api/db.php';

$challengeId = 'upload-dispatch';
$uploadDir = __DIR__ . '/../assets/dispatch_uploads';
$publicPath = '/assets/dispatch_uploads';
$message = '';
$uploadedUrl = '';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$flagFile = __DIR__ . '/../assets/dispatch_uploads/.dispatch_flag';
if (!is_file($flagFile)) {
    $conn = db();
    $flag = get_flag($conn, $challengeId) ?? 'EST{missing_flag}';
    file_put_contents($flagFile, $flag . PHP_EOL);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['dispatch_file'])) {
    $file = $_FILES['dispatch_file'];
    $originalName = basename((string) $file['name']);
    $lowerName = strtolower($originalName);

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = '?낅줈???ㅽ뙣.';
    } elseif (strpos($lowerName, '.png') === false && strpos($lowerName, '.jpg') === false && strpos($lowerName, '.jpeg') === false) {
        $message = '?꾩넚?뚮뒗 ?뚯씪紐낆뿉 .png, .jpg, .jpeg媛 ?ы븿??泥⑤?留??묒닔?⑸땲??';
    } else {
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
        $targetPath = $uploadDir . '/' . $safeName;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $uploadedUrl = $publicPath . '/' . rawurlencode($safeName);
            $message = '寃???湲?泥⑤?濡???λ릺?덉뒿?덈떎.';
        } else {
            $message = '泥⑤? ?뚯씪????ν븯吏 紐삵뻽?듬땲??';
        }
    }
}
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>?꾩넚 ?뚯씪 ?낅줈??| EST ?꾩닠蹂댁븞 ?명듃?쇰꽬</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">?묒쟾 怨쇱젣 紐⑸줉</a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>?꾩넚 ?뚯씪 ?낅줈??/span>
          <small>?뚯씪 ?낅줈???고쉶</small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">450 ?꾧낵 / 怨좉툒 ?묒쟾</p>
          <h1>?ㅽ뻾 媛?ν븳 泥⑤? ???/h1>
          <p>?꾩넚 ?ы꽭? ?묒쟾 利앸튃 ?대?吏瑜??묒닔??怨듦컻 寃쎈줈????ν빀?덈떎. ?뚯씪紐?寃?ш? ?덉닠?섎?濡? ???쒕쾭媛 ?ㅽ뻾?????덈뒗 泥⑤?瑜??щ젮 ?④꺼吏??꾩넚 ?쒖떇???쎌뼱?댁떗?쒖삤.</p>

          <form class="tool-form" method="post" enctype="multipart/form-data">
            <label for="dispatch-file">利앸튃 ?대?吏</label>
            <input id="dispatch-file" name="dispatch_file" type="file" />
            <button class="primary-button" type="submit">?낅줈??/button>
          </form>

          <?php if ($message): ?>
            <div class="hint-box visible <?php echo $uploadedUrl ? 'success-box' : ''; ?>">
              <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
              <?php if ($uploadedUrl): ?>
                <p>???寃쎈줈: <a href="<?php echo htmlspecialchars($uploadedUrl, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($uploadedUrl, ENT_QUOTES, 'UTF-8'); ?></a></p>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <div class="hint-box visible staged-hints">
            <strong>?묒쟾 硫붾え</strong>
            <p>留됲엳硫??쒖꽌?濡??대엺?섏떗?쒖삤.</p>
            <details><summary>1?④퀎</summary><p>?ы꽭? ?뚯씪紐낆씠 ?대?吏 ?뺤옣?먮줈 ?앸굹?붿?媛 ?꾨땲?? ?대?吏 ?뺤옣??臾몄옄?댁쓣 ?ы븿?섎뒗吏留??뺤씤?⑸땲??</p></details>
            <details><summary>2?④퀎</summary><p>Apache???낅줈?쒕맂 ?뚯씪??留덉?留??뺤옣?먮? 湲곗??쇰줈 ?ㅽ뻾 諛⑹떇??寃곗젙?⑸땲??</p></details>
            <details><summary>3?④퀎</summary><p>?쒖떇? ?낅줈???뚯씪 湲곗? <code>../assets/dispatch_uploads/.dispatch_flag</code>??蹂닿??섏뼱 ?덉뒿?덈떎.</p></details>
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
