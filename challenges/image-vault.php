<?php
declare(strict_types=1);
require __DIR__ . '/../api/db.php';

$challengeId = 'image-vault';
$baseDir = __DIR__ . '/../assets/vault';
$requested = $_GET['file'] ?? 'cover.png';
$content = null;
$message = '';

if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
}

$flagFile = $baseDir . '/flag.txt';
if (!is_file($flagFile)) {
    $conn = db();
    $flag = get_flag($conn, $challengeId) ?? 'EST{missing_flag}';
    file_put_contents($flagFile, $flag);
}

$coverFile = $baseDir . '/cover.png';
if (!is_file($coverFile)) {
    file_put_contents($coverFile, "PNG preview placeholder\n");
}


$allowedDir = $baseDir . '/allowed.png';
if (!is_dir($allowedDir)) {
    mkdir($allowedDir, 0755, true);
}

if (isset($_GET['file'])) {
    if (strpos($requested, '.png') === false) {
        $message = '&#xD30C;&#xC77C;&#xBA85;&#xC5D0; .png&#xAC00; &#xD3EC;&#xD568;&#xB418;&#xC5B4;&#xC57C; &#xD569;&#xB2C8;&#xB2E4;.';
    } else {
        $path = $baseDir . '/' . $requested;
        if (is_file($path)) {
            $content = file_get_contents($path);
        } else {
            $message = '&#xD30C;&#xC77C;&#xC744; &#xCC3E;&#xC744; &#xC218; &#xC5C6;&#xC2B5;&#xB2C8;&#xB2E4;.';
        }
    }
}
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>영상 보급창 | EST ?袁⑸떊癰귣똻釉??紐낅뱜??곌쉬</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">???臾믪읈 ?⑥눘??筌뤴뫖以?/a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>?곸긽 蹂닿툒李?/span>
          <small>癰귣떯?믭㎕????뵬 ????/small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">300 ?袁㏓궢 / ?怨댄닋 ?袁⑺겧</p>
          <h1>?怨멸맒 癰귣떯?믭㎕?뚯벥 ??됰떊??野꺜??/h1>
          <p>癰귣떯?믭㎕?沃섎챶?곮퉪?용┛ ?貫??????뵬筌?野꺜?얜챶彛????궢??롢늺 筌≪럡????? ?癒?┷???곗눖沅???щ빍?? 野꺜??域뱀뮇?껅???쇱젫 ??猷?野껋럥以덂첎? ??욱닎??롫뮉 筌왖?癒?뱽 筌≪뼚釉??紐꾩쵄 ??뽯뻼?????땾??뤿뼏??뽰궎.</p>
          <form class="tool-form" method="get">
            <label for="file">???????????뵬</label>
            <div class="submit-row compact-row">
              <input id="file" name="file" value="<?php echo htmlspecialchars($requested, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off" />
              <button class="primary-button" type="submit">????/button>
            </div>
          </form>
          <?php if ($content !== null): ?>
            <pre class="terminal"><?php echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); ?></pre>
          <?php elseif ($message): ?>
            <div class="hint-box visible"><p><?php echo $message; ?></p></div>
          <?php endif; ?>
          <div class="hint-box visible staged-hints">
            <strong>?臾믪읈 筌롫뗀??/strong>
            <p>筌띾맪?놂쭖??袁⑥삋 ??뽮퐣??嚥??類ㅺ컳??뤿뼏??뽰궎.</p>
            <details><summary>1??ｍ?/summary><p>&#xD5C8;&#xC6A9;&#xB418;&#xB294; &#xD30C;&#xC77C;&#xBA85;&#xACFC; &#xAC70;&#xBD80;&#xB418;&#xB294; &#xD30C;&#xC77C;&#xBA85;&#xC744; &#xBE44;&#xAD50;&#xD574; &#xD544;&#xD130;&#xAC00; &#xBB38;&#xC790;&#xC5F4;&#xC758; &#xC5B4;&#xB5A4; &#xC870;&#xAC74;&#xC744; &#xBCF4;&#xB294;&#xC9C0; &#xCC3E;&#xC73C;&#xC138;&#xC694;.</p></details>
            <details><summary>2??ｍ?/summary><p>&#xACBD;&#xB85C; &#xAD6C;&#xC131;&#xC694;&#xC18C;&#xB294; &#xD544;&#xD130; &#xD1B5;&#xACFC; &#xD6C4; &#xC11C;&#xBC84;&#xC5D0;&#xC11C; &#xB2E4;&#xC2DC; &#xD574;&#xC11D;&#xB429;&#xB2C8;&#xB2E4;. &#xC911;&#xAC04; &#xACBD;&#xB85C;&#xB97C; &#xC774;&#xB3D9;&#xD558;&#xB294; &#xD45C;&#xD604;&#xC744; &#xB5A0;&#xC62C;&#xB824;&#xBCF4;&#xC138;&#xC694;.</p></details>
            <details><summary>3??ｍ?/summary><p>&#xD544;&#xD130;&#xAC00; &#xD1B5;&#xACFC;&#xD560; &#xB9CC;&#xD55C; &#xD45C;&#xC2DD;&#xC744; &#xB0A8;&#xAE30;&#xACE0;, &#xC2E4;&#xC81C; &#xC11C;&#xBC84; &#xD574;&#xC11D; &#xACB0;&#xACFC;&#xB294; &#xB2E4;&#xB978; &#xD30C;&#xC77C;&#xB85C; &#xD5A5;&#xD558;&#xAC8C; &#xB9CC;&#xB4E4;&#xC5B4;&#xBCF4;&#xC138;&#xC694;.</p></details>
          </div>
          <form class="submit-row" data-flag-form data-challenge-id="<?php echo $challengeId; ?>">
            <input name="flag" placeholder="EST{...}" autocomplete="off" />
            <button class="primary-button" type="submit">癰귣떯??/button>
          </form>
        </div>
      </section>
    </main>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
    <script src="/app.js?v=team-required-1"></script>
  </body>
</html>
