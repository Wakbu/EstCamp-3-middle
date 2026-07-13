<?php
declare(strict_types=1);
require __DIR__ . '/../api/db.php';

$challengeId = 'mission-report';
$conn = db();
$flag = get_flag($conn, $challengeId) ?? 'FLAG{missing_flag}';

$reports = [
    '1001' => [
        'title' => '1분대 외곽 정찰 보고',
        'team' => '1분대',
        'classification' => '공개',
        'body' => '북측 외곽 통신선 점검 완료. 특이 징후는 발견되지 않았습니다.',
    ],
    '1002' => [
        'title' => '2분대 보급 현황 보고',
        'team' => '2분대',
        'classification' => '공개',
        'body' => '야전 보급품 수량과 장비 반납 상태를 확인했습니다.',
    ],
    '1003' => [
        'title' => '3분대 야간 순찰 보고',
        'team' => '3분대',
        'classification' => '공개',
        'body' => '야간 순찰 구역 이상 없음. 다음 교대조에 인계했습니다.',
    ],
    '1004' => [
        'title' => '관리관 전용 비문 보고',
        'team' => '관리관',
        'classification' => '비문',
        'body' => '이 보고서는 일반 분대에 노출되면 안 됩니다. 인증 표식: ' . $flag,
    ],
];

$visibleReports = ['1001', '1002', '1003'];
$requestedReport = preg_replace('/[^0-9]/', '', $_GET['report'] ?? '1001');
$report = $reports[$requestedReport] ?? null;
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>작전 보고서 열람소 | EST 전술보안 인트라넷</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">작전 과제 목록</a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>작전 보고서 열람소</span>
          <small>IDOR / 중급</small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">325 전과 / 접근 제어 작전</p>
          <h1>보고서 번호 뒤에 숨은 비문을 찾아라</h1>
          <p>분대 보고서 열람소는 선택한 보고서 번호를 기준으로 문서를 보여줍니다. 화면에 보이는 보고서만 접근 가능한지, 서버가 보고서 번호를 어떻게 신뢰하는지 확인하십시오.</p>

          <div class="hint-box visible">
            <strong>공개 보고서 목록</strong>
            <ul>
              <?php foreach ($visibleReports as $id): ?>
                <li><a href="?report=<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">보고서 #<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>

          <form class="tool-form" method="get">
            <label for="report">보고서 번호</label>
            <div class="submit-row compact-row">
              <input id="report" name="report" value="<?php echo htmlspecialchars($requestedReport, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off" />
              <button class="primary-button" type="submit">열람</button>
            </div>
          </form>

          <?php if ($report): ?>
            <article class="hint-box visible success-box">
              <strong><?php echo htmlspecialchars($report['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
              <p>작성 분대: <?php echo htmlspecialchars($report['team'], ENT_QUOTES, 'UTF-8'); ?> / 등급: <?php echo htmlspecialchars($report['classification'], ENT_QUOTES, 'UTF-8'); ?></p>
              <pre class="terminal"><?php echo htmlspecialchars($report['body'], ENT_QUOTES, 'UTF-8'); ?></pre>
            </article>
          <?php else: ?>
            <div class="hint-box visible">
              <p>해당 번호의 보고서를 찾을 수 없습니다.</p>
            </div>
          <?php endif; ?>
          <!-- 소스 힌트: report 파라미터 값이 열람할 보고서 번호를 결정한다. -->
          <!-- 소스 힌트: 공개 목록은 1001~1003만 보여주지만, 서버는 요청한 번호가 공개 목록에 있는지 확인하지 않는다. -->
          <!-- 소스 힌트: 공개 목록에 없는 가까운 번호를 직접 입력해 비문 보고서를 찾아라. -->

          <form class="submit-row" data-flag-form data-challenge-id="<?php echo $challengeId; ?>">
            <input name="flag" placeholder="FLAG{...}" autocomplete="off" />
            <button class="primary-button" type="submit">보고</button>
          </form>
        </div>
      </section>
    </main>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
    <script src="/app.js?v=reset-cleanup-1"></script>
  </body>
</html>
