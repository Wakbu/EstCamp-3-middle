<?php
declare(strict_types=1);
require __DIR__ . '/../api/db.php';

$challengeId = 'admin-memo-chain';
$conn = db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'write') {
    $body = substr($_POST['body'] ?? '', 0, 2000);
    $stmt = $conn->prepare('INSERT INTO admin_memos (body) VALUES (?)');
    $stmt->bind_param('s', $body);
    $stmt->execute();
    $message = '&#xBA54;&#xBAA8;&#xAC00; &#xC800;&#xC7A5;&#xB418;&#xC5C8;&#xC2B5;&#xB2C8;&#xB2E4;. &#xAD00;&#xB9AC;&#xC790; &#xAC80;&#xD1A0;&#xB97C; &#xC694;&#xCCAD;&#xD574;&#xBCF4;&#xC138;&#xC694;.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'review') {
    $row = $conn->query('SELECT id, body FROM admin_memos ORDER BY id DESC LIMIT 1')->fetch_assoc();
    if ($row) {
        $flag = get_flag($conn, $challengeId) ?? 'EST{missing_flag}';
        $result = 'reviewed';
        if (stripos($row['body'], 'fetch("/challenges/admin-memo-chain.php?admin_api=flag")') !== false ||
            stripos($row['body'], "fetch('/challenges/admin-memo-chain.php?admin_api=flag')") !== false) {
            $result = '愿由ш? 釉뚮씪?곗?媛 鍮꾧났媛?API瑜??몄텧?? ' . $flag;
        }
        $stmt = $conn->prepare('UPDATE admin_memos SET review_result = ? WHERE id = ?');
        $id = (int) $row['id'];
        $stmt->bind_param('si', $result, $id);
        $stmt->execute();
        $message = '&#xAD00;&#xB9AC;&#xC790; &#xAC80;&#xD1A0;&#xAC00; &#xC644;&#xB8CC;&#xB418;&#xC5C8;&#xC2B5;&#xB2C8;&#xB2E4;.';
    }
}

if (($_GET['admin_api'] ?? '') === 'flag') {
    if (($_COOKIE['memo_admin'] ?? '') === '1') {
        $flag = get_flag($conn, $challengeId) ?? 'EST{missing_flag}';
        header('Content-Type: text/plain; charset=utf-8');
        echo $flag;
        exit;
    }
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'admin only';
    exit;
}

$memos = $conn->query('SELECT id, body, review_result, created_at FROM admin_memos ORDER BY id DESC LIMIT 5');
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>관리관 메모 연쇄 | EST ?袁⑸떊癰귣똻釉??紐낅뱜??곌쉬</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">???臾믪읈 ?⑥눘??筌뤴뫖以?/a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>愿由ш? 硫붾え ?곗뇙</span>
          <small>野꺜??? ?됰슢??怨? ?醫딅즲</small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">400 ?袁㏓궢 / ?⑥쥒???臾믪읈</p>
          <h1>野꺜??? 筌롫뗀??筌ｋ떯??燁삘뫂??/h1>
          <p>??깆읈 筌롫뗀?????????野꺜??? ??ㅼ춾?癒?퐣 ??쇰뻻 ?????몃빍?? ??? ??ｋ┸ ?얜챷???野꺜??? 亦낅슦釉???됰슢??怨??癒?퐣 ??堉멨칰???곴퐤??롫뮉筌왖 ?類ㅼ뵥??랁? ??? ?袁⑹뒠 ?紐꾪뀱繹먮슣? ?怨뚭퍙??뤿뼏??뽰궎.</p>
          <?php if ($message): ?><div class="hint-box visible"><p><?php echo $message; ?></p></div><?php endif; ?>
          <form class="tool-form" method="post">
            <input type="hidden" name="action" value="write" />
            <label for="memo">?臾믪읈 筌롫뗀????곸뒠</label>
            <textarea id="memo" name="body" rows="6" placeholder="&#xAC80;&#xD1A0;&#xB420; &#xBA54;&#xBAA8;&#xB97C; &#xC785;&#xB825;&#xD558;&#xC138;&#xC694;."></textarea>
            <button class="primary-button" type="submit">筌롫뗀???源낆쨯</button>
          </form>
          <form method="post" class="inline-form">
            <input type="hidden" name="action" value="review" />
            <button class="ghost-button" type="submit">野꺜??? 野꺜???遺욧퍕</button>
          </form>
          <div class="memo-list">
            <?php while ($memo = $memos->fetch_assoc()): ?>
              <article class="memo-card">
                <small>#<?php echo (int) $memo['id']; ?> / <?php echo htmlspecialchars($memo['created_at'], ENT_QUOTES, 'UTF-8'); ?></small>
                <div class="memo-preview"><?php echo $memo['body']; ?></div>
                <?php if ($memo['review_result']): ?><pre class="terminal"><?php echo htmlspecialchars($memo['review_result'], ENT_QUOTES, 'UTF-8'); ?></pre><?php endif; ?>
              </article>
            <?php endwhile; ?>
          </div>
                    <!-- 작전 단서: 메모 내용은 저장 후 검토 화면에서 다시 렌더링된다. 문자로 보존되는지 요소로 해석되는지 확인하라. -->
          <!-- 작전 단서: 관리관 검토 흐름에서 /challenges/admin-memo-chain.php?admin_api=flag 요청 결과가 기록에 남을 수 있다. -->
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
