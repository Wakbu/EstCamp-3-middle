<?php
declare(strict_types=1);
require __DIR__ . '/../api/db.php';

$challengeId = 'blind-notice';
$q = $_GET['q'] ?? '';
$searched = array_key_exists('q', $_GET);
$match = false;
$error = '';

if ($searched) {
    $conn = db();
    $sql = "SELECT id FROM blind_notices WHERE is_public = 1 AND title LIKE '%" . $q . "%' LIMIT 1";
    try {
        $result = $conn->query($sql);
        $match = $result && $result->num_rows > 0;
    } catch (Throwable $e) {
        $error = '議고쉶 ?ㅽ뙣.';
    }
}
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>비문 공지 검색소 | EST ?袁⑸떊癰귣똻釉??紐낅뱜??곌쉬</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">???臾믪읈 ?⑥눘??筌뤴뫖以?/a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>鍮꾨Ц 怨듭? 寃?됱냼</span>
          <small>??쑨?у첎??⑤벊? ?類ㅺ컳</small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">250 ?袁㏓궢 / 餓λ쵌???臾믪읈</p>
          <h1>??쑬揆 ?⑤벊? 野꺜??깅꺖</h1>
          <p>?봔?? 野껊슣???野꺜??깅꺖???⑤벀而??⑤벊?筌?癰귣똻肉т틠??筌? 筌욌뜆?썼눧紐꾨퓠 ?怨뺤뵬 獄쏆꼷???沃섎챷苑??띿쓺 ???わ쭪臾먮빍?? ??곸뒠癰귣???鈺곕똻????? ?醫륁깈???온筌↔퀬鍮???쑨?у첎???깆벥 ?ル슦紐당몴??곕뗄???뤿뼏??뽰궎.</p>
          <form class="tool-form" method="get">
            <label for="q">野꺜??筌욌뜆??/label>
            <div class="submit-row compact-row">
              <input id="q" name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off" />
              <button class="primary-button" type="submit">鈺곌퀬??/button>
            </div>
          </form>
          <?php if ($searched): ?>
            <div class="hint-box visible <?php echo $match ? 'success-box' : ''; ?>">
              <strong>鈺곌퀬??&#xACB0;&#xACFC;</strong>
              <p><?php echo $error ? '&#xC870;&#xD68C; &#xC624;&#xB958;' : ($match ? '&#xC870;&#xAC74;&#xC5D0; &#xB9DE;&#xB294; &#xACF5;&#xC9C0;&#xAC00; &#xC874;&#xC7AC;&#xD569;&#xB2C8;&#xB2E4;.' : '鈺곌퀬??#xB41C; &#xACF5;&#xC9C0;&#xAC00; &#xC5C6;&#xC2B5;&#xB2C8;&#xB2E4;.'); ?></p>
            </div>
          <?php endif; ?>
                    <!-- 작전 단서: 검색어는 SQL LIKE 조건 안으로 직접 들어간다. 결과 문구의 참/거짓 차이를 관찰하라. -->
          <!-- 작전 단서: 한 번에 읽으려 하지 말고 조건을 바꿔가며 존재 여부를 신호로 삼아라. -->
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
