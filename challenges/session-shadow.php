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
    <title>Session Shadow | EST 전술보안 인트라넷</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">← 작전 과제 목록</a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>Session Shadow</span>
          <small>인증 쿠키 점검</small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">150 전과 / 초급 작전</p>
          <h1>보이지 않는 계급장</h1>
          <p>교육망 출입 통제소가 접속자의 보직 값을 확인하고 있습니다. 내게 발급된 출입증이 어디에 저장되는지 확인하고, 더 높은 권한의 통행 표식을 재현하십시오.</p>
          <div class="terminal">
            <span>출입 판정</span>
            <code><?php echo $role === 'admin' ? 'granted' : 'limited'; ?></code>
          </div>
          <?php if ($flag): ?>
            <div class="hint-box visible success-box">
              <strong>FLAG</strong>
              <code><?php echo htmlspecialchars($flag, ENT_QUOTES, 'UTF-8'); ?></code>
            </div>
          <?php else: ?>
            <div class="hint-box visible staged-hints">
              <strong>작전 메모</strong>
              <details open><summary>1단계</summary><p>브라우저 보급품 창고(Application/Storage)에서 이 교육망이 발급한 출입증 값을 확인하십시오.</p></details>
              <details><summary>2단계</summary><p>보직을 나타내는 값이 있다면, 통제소 판정이 바뀌는지 조심스럽게 조정해 보십시오.</p></details>
            </div>
          <?php endif; ?>
          <form class="submit-row" data-flag-form data-challenge-id="<?php echo $challengeId; ?>">
            <input name="flag" placeholder="EST{...}" autocomplete="off" />
            <button class="primary-button" type="submit">보고</button>
          </form>
        </div>
      </section>
    </main>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
    <script src="/app.js?v=team-required-1"></script>
  </body>
</html>
