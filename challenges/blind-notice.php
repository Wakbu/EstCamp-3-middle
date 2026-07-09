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
        $error = 'Search failed.';
    }
}
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blind Notice | EST 전술보안 인트라넷</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">← 작전 과제 목록</a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>Blind Notice</span>
          <small>비공개 공지 정찰</small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">250 전과 / 중급 작전</p>
          <h1>비문 공지 검색소</h1>
          <p>부대 게시판 검색소는 공개 공지만 보여주지만, 질의문에 따라 반응이 미세하게 달라집니다. 내용보다 존재 여부 신호를 관찰해 비공개 행의 좌표를 추적하십시오.</p>
          <form class="tool-form" method="get">
            <label for="q">검색 질의</label>
            <div class="submit-row compact-row">
              <input id="q" name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off" />
              <button class="primary-button" type="submit">조회</button>
            </div>
          </form>
          <?php if ($searched): ?>
            <div class="hint-box visible <?php echo $match ? 'success-box' : ''; ?>">
              <strong>조회 &#xACB0;&#xACFC;</strong>
              <p><?php echo $error ? '&#xC870;&#xD68C; &#xC624;&#xB958;' : ($match ? '&#xC870;&#xAC74;&#xC5D0; &#xB9DE;&#xB294; &#xACF5;&#xC9C0;&#xAC00; &#xC874;&#xC7AC;&#xD569;&#xB2C8;&#xB2E4;.' : '조회&#xB41C; &#xACF5;&#xC9C0;&#xAC00; &#xC5C6;&#xC2B5;&#xB2C8;&#xB2E4;.'); ?></p>
            </div>
          <?php endif; ?>
          <div class="hint-box visible staged-hints">
            <strong>작전 메모</strong>
            <p>막히면 아래 순서대로 정찰하십시오.</p>
            <details><summary>1단계</summary><p>&#xC815;&#xC0C1; 검색 질의&#xC640; &#xD2B9;&#xC218;&#xBB38;&#xC790;&#xAC00; &#xD3EC;&#xD568;&#xB41C; 검색 질의&#xB97C; &#xBE44;&#xAD50;&#xD574;&#xBCF4;&#xC138;&#xC694;. &#xACB0;&#xACFC; &#xBB38;&#xAD6C;&#xAC00; &#xBC14;&#xB00C;&#xB294; &#xC870;&#xAC74;&#xC774; &#xC788;&#xC2B5;&#xB2C8;&#xB2E4;.</p></details>
            <details><summary>2단계</summary><p>&#xC5B4;&#xB5A4; &#xC870;&#xAC74;&#xC740; 조회 &#xACB0;&#xACFC;&#xB97C; &#xC874;&#xC7AC;&#xD558;&#xAC8C; &#xB9CC;&#xB4E4;&#xACE0;, &#xC5B4;&#xB5A4; &#xC870;&#xAC74;&#xC740; &#xC5C6;&#xAC8C; &#xB9CC;&#xB4ED;&#xB2C8;&#xB2E4;. &#xC774; &#xCC28;&#xC774;&#xB97C; &#xCC38/&#xAC70;&#xC9D3; &#xC2E0;&#xD638;&#xB85C; &#xC0AC;&#xC6A9;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p></details>
            <details><summary>3단계</summary><p>&#xD544;&#xC694;&#xD55C; &#xAC12;&#xC744; &#xD55C; &#xBC88;&#xC5D0; &#xBCF4;&#xAE30;&#xBCF4;&#xB2E4;, &#xD55C; &#xAE00;&#xC790;&#xC529; &#xBE44;&#xAD50;&#xD558;&#xB294; &#xBC29;&#xC2DD;&#xC744; &#xACE0;&#xB824;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p></details>
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
