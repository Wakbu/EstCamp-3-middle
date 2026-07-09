<?php
declare(strict_types=1);

$challengeId = 'session-shadow';
$role = $_COOKIE['shadow_role'] ?? 'guest';

if (!isset($_COOKIE['shadow_role'])) {
    setcookie('shadow_role', 'guest', 0, '/', '', false, false);
    $role = 'guest';
}

$flag = null;
if ($role === 'admin') {
    require __DIR__ . '/../api/db.php';
    $conn = db();
    $flag = get_flag($conn, $challengeId);
}
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>세션 그림자 | EST ?袁⑸떊癰귣똻釉??紐낅뱜??곌쉬</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">???臾믪읈 ?⑥눘??筌뤴뫖以?/a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>?몄뀡 洹몃┝??/span>
          <small>?紐꾩쵄 ?묒쥚沅??癒?</small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">150 ?袁㏓궢 / ?λ뜃???臾믪읈</p>
          <h1>癰귣똻?좑쭪? ??낅뮉 ?④쑨???/h1>
          <p>?대Ŋ?곻쭕??곗뮇?????젫??? ?臾믩꺗?癒?벥 癰귣똻彛?揶쏅????類ㅼ뵥??랁???됰뮸??덈뼄. ??우쓺 獄쏆뮄????곗뮇??쭩?뱀뵠 ??逾?????貫由?遺? ?類ㅼ뵥??랁? ???誘? 亦낅슦釉?????뻬 ??뽯뻼???????뤿뼏??뽰궎.</p>
          <div class="terminal">
            <span>?곗뮇???癒?젟</span>
            <code><?php echo $role === 'admin' ? 'granted' : 'limited'; ?></code>
          </div>
          <?php if ($flag): ?>
            <div class="hint-box visible success-box">
              <strong>FLAG</strong>
              <code><?php echo htmlspecialchars($flag, ENT_QUOTES, 'UTF-8'); ?></code>
            </div>
          <?php else: ?>
          <?php endif; ?>
                    <!-- 작전 단서: 브라우저 저장소의 쿠키 값을 확인하라. shadow_role 값은 출입 판정에 직접 사용된다. -->
          <!-- 작전 단서: 상위 권한 명칭은 서버 조건문에 그대로 비교된다. -->
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
