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
            $result = 'admin browser fetched private API: ' . $flag;
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
    <title>Admin Memo Chain | EST 전술보안 인트라넷</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">← 작전 과제 목록</a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>Admin Memo Chain</span>
          <small>검열관 브라우저 유도</small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">400 전과 / 고급 작전</p>
          <h1>검열관 메모 체계 침투</h1>
          <p>야전 메모는 저장 후 검열관 단말에서 다시 열람됩니다. 내가 남긴 문장이 검열관 권한의 브라우저에서 어떻게 해석되는지 확인하고, 내부 전용 호출까지 연결하십시오.</p>
          <?php if ($message): ?><div class="hint-box visible"><p><?php echo $message; ?></p></div><?php endif; ?>
          <form class="tool-form" method="post">
            <input type="hidden" name="action" value="write" />
            <label for="memo">작전 메모 내용</label>
            <textarea id="memo" name="body" rows="6" placeholder="&#xAC80;&#xD1A0;&#xB420; &#xBA54;&#xBAA8;&#xB97C; &#xC785;&#xB825;&#xD558;&#xC138;&#xC694;."></textarea>
            <button class="primary-button" type="submit">메모 등록</button>
          </form>
          <div class="hint-box visible staged-hints">
            <strong>작전 메모</strong>
            <p>막히면 아래 순서대로 정찰하십시오.</p>
            <details><summary>1단계</summary><p>&#xBA54;&#xBAA8;&#xC5D0; &#xB0A8;&#xAE34; &#xBB38;&#xC790;&#xAC00; &#xC800;&#xC7A5; &#xD6C4; &#xD398;&#xC774;&#xC9C0;&#xC5D0; &#xC5B4;&#xB5BB;&#xAC8C; &#xB3CC;&#xC544;&#xC624;&#xB294;&#xC9C0; &#xD655;&#xC778;&#xD558;&#xC138;&#xC694;. &#xADF8;&#xB300;&#xB85C; &#xBCF4;&#xC774;&#xB294;&#xC9C0;&#xAC00; &#xC911;&#xC694;&#xD569;&#xB2C8;&#xB2E4;.</p></details>
            <details><summary>2단계</summary><p>&#xD398;&#xC774;&#xC9C0;&#xAC00; &#xB0B4; &#xC785;&#xB825;&#xC744; &#xBB38;&#xC790;&#xB85C; &#xBCF4;&#xC874;&#xD558;&#xB294;&#xC9C0;, &#xD654;&#xBA74; &#xAD6C;&#xC131;&#xC694;&#xC18C;&#xB85C; &#xD574;&#xC11D;&#xD558;&#xB294;&#xC9C0; &#xBE44;&#xAD50;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p></details>
            <details><summary>3단계</summary><p>&#xAC80;&#xD1A0; &#xD750;&#xB984;&#xC5D0;&#xC11C;&#xB294; &#xB2E4;&#xB978; &#xAD8C;&#xD55C;&#xC758; &#xC790;&#xC6D0;&#xC744; &#xC694;&#xCCAD;&#xD560; &#xC218; &#xC788;&#xB294;&#xC9C0; &#xD655;&#xC778;&#xD574;&#xBCF4;&#xC138;&#xC694;. &#xC694;&#xCCAD; &#xACB0;&#xACFC;&#xAC00; &#xBA54;&#xBAA8; &#xAE30;&#xB85D;&#xC5D0; &#xB0A8;&#xB294;&#xC9C0;&#xB3C4; &#xBCF4;&#xC138;&#xC694;.</p></details>
          </div>
          <form method="post" class="inline-form">
            <input type="hidden" name="action" value="review" />
            <button class="ghost-button" type="submit">검열관 검토 요청</button>
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
