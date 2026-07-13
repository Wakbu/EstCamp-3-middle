<?php
session_start();
require __DIR__ . '/../api/db.php';

$adminPassword = getenv('WARGAME_ADMIN_PASSWORD') ?: '';
$message = '';
$error = '';

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function valid_challenge_id(string $value): bool {
    return (bool) preg_match('/^[a-z0-9][a-z0-9-]{1,62}$/', $value);
}

function challenge_template_path(string $challengeId): array {
    $relativePath = '/challenges/generated/' . $challengeId . '.php';
    $directory = __DIR__ . '/../challenges/generated';
    $filePath = $directory . '/' . $challengeId . '.php';
    return [$relativePath, $directory, $filePath];
}

function build_challenge_template(array $data): string {
    $challengeId = h($data['challenge_id']);
    $title = h($data['title']);
    $category = h($data['category']);
    $difficulty = h($data['difficulty']);
    $points = (int) $data['points'];
    $summary = h($data['summary']);
    $pageTitle = h($data['title'] . ' | EST 전술보안 인트라넷');

    return <<<HTML
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{$pageTitle}</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">작전 과제 목록</a>
      <section class="challenge-detail">
        <div class="section-heading"><span>{$title}</span><small>{$category} / {$difficulty}</small></div>
        <div class="challenge-body">
          <p class="eyebrow">{$points} 전과</p>
          <h1>{$title}</h1>
          <p>{$summary}</p>
          <!-- 소스 힌트: 이 자동 생성 템플릿에 문제별 단서를 주석으로 작성하십시오. -->
          <form class="submit-row" data-flag-form data-challenge-id="{$challengeId}">
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
HTML;
}

function generated_template_file_for(string $challengeId, string $path): ?string {
    [$expectedPath, $directory, $filePath] = challenge_template_path($challengeId);
    if ($path !== $expectedPath) return null;
    $base = realpath($directory);
    if ($base === false) return null;
    $target = realpath($filePath);
    if ($target === false || dirname($target) !== $base) return null;
    return $target;
}

function write_generated_template_content(string $challengeId, string $path, string $content): array {
    $templateFile = generated_template_file_for($challengeId, $path);
    if (!$templateFile) return [false, '자동 생성된 PHP 템플릿만 편집할 수 있습니다.'];
    if (file_put_contents($templateFile, $content, LOCK_EX) === false) return [false, 'PHP 파일 내용을 저장하지 못했습니다.'];
    return [true, ' PHP 파일 내용을 저장했습니다.'];
}

function write_challenge_template(array $data, bool $overwrite): array {
    [$relativePath, $directory, $filePath] = challenge_template_path($data['challenge_id']);
    if (!is_dir($directory) && !mkdir($directory, 0750, true)) return [false, '템플릿 폴더를 만들 수 없습니다.', $relativePath];
    $base = realpath($directory);
    if ($base === false) return [false, '템플릿 경로를 확인할 수 없습니다.', $relativePath];
    $targetDir = realpath(dirname($filePath));
    if ($targetDir === false || $targetDir !== $base) return [false, '허용되지 않은 경로입니다.', $relativePath];
    if (file_exists($filePath) && !$overwrite) return [false, '같은 템플릿 파일이 이미 있습니다.', $relativePath];
    if (file_put_contents($filePath, build_challenge_template($data), LOCK_EX) === false) return [false, '템플릿 파일을 저장하지 못했습니다.', $relativePath];
    return [true, '템플릿 파일을 생성했습니다: ' . h($relativePath), $relativePath];
}

function form_data_from_post(): array {
    return [
        'challenge_id' => trim($_POST['challenge_id'] ?? ''),
        'title' => trim($_POST['title'] ?? ''),
        'category' => trim($_POST['category'] ?? 'Web'),
        'difficulty' => trim($_POST['difficulty'] ?? '초급'),
        'points' => max(0, (int) ($_POST['points'] ?? 0)),
        'path' => trim($_POST['path'] ?? ''),
        'summary' => trim($_POST['summary'] ?? ''),
        'flag' => trim($_POST['flag'] ?? ''),
        'sort_order' => (int) ($_POST['sort_order'] ?? 100),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
}

function validate_challenge_data(array $data): string {
    if (!valid_challenge_id($data['challenge_id'])) return '문제 ID는 영문 소문자, 숫자, 하이픈만 사용할 수 있습니다.';
    if ($data['title'] === '' || $data['path'] === '' || $data['summary'] === '' || $data['flag'] === '') return '제목, 경로, 설명, 플래그는 필수입니다.';
    return '';
}

function challenge_exists(mysqli $conn, string $challengeId): bool {
    $stmt = $conn->prepare('SELECT challenge_id FROM challenges WHERE challenge_id = ? LIMIT 1');
    $stmt->bind_param('s', $challengeId);
    $stmt->execute();
    return (bool) $stmt->get_result()->fetch_assoc();
}

function insert_challenge(mysqli $conn, array $data): void {
    $stmt = $conn->prepare('INSERT INTO challenges (challenge_id, title, category, difficulty, points, path, summary, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('ssssissii', $data['challenge_id'], $data['title'], $data['category'], $data['difficulty'], $data['points'], $data['path'], $data['summary'], $data['is_active'], $data['sort_order']);
    $stmt->execute();
    $stmt = $conn->prepare('INSERT INTO challenge_flags (challenge_id, flag) VALUES (?, ?)');
    $stmt->bind_param('ss', $data['challenge_id'], $data['flag']);
    $stmt->execute();
}

function update_challenge(mysqli $conn, string $originalId, array $data): string {
    if (!valid_challenge_id($originalId)) return '수정할 원본 문제 ID가 올바르지 않습니다.';
    if (!challenge_exists($conn, $originalId)) return '수정할 문제를 찾을 수 없습니다.';
    if ($originalId !== $data['challenge_id'] && challenge_exists($conn, $data['challenge_id'])) return '변경하려는 문제 ID가 이미 존재합니다.';
    $conn->begin_transaction();
    try {
        if ($originalId !== $data['challenge_id']) {
            foreach (['challenges', 'challenge_flags', 'submissions', 'solves'] as $table) {
                $stmt = $conn->prepare("UPDATE {$table} SET challenge_id = ? WHERE challenge_id = ?");
                $stmt->bind_param('ss', $data['challenge_id'], $originalId);
                $stmt->execute();
            }
        }
        $stmt = $conn->prepare('UPDATE challenges SET title = ?, category = ?, difficulty = ?, points = ?, path = ?, summary = ?, is_active = ?, sort_order = ? WHERE challenge_id = ?');
        $stmt->bind_param('sssissiis', $data['title'], $data['category'], $data['difficulty'], $data['points'], $data['path'], $data['summary'], $data['is_active'], $data['sort_order'], $data['challenge_id']);
        $stmt->execute();
        $stmt = $conn->prepare('INSERT INTO challenge_flags (challenge_id, flag) VALUES (?, ?) ON DUPLICATE KEY UPDATE flag = VALUES(flag)');
        $stmt->bind_param('ss', $data['challenge_id'], $data['flag']);
        $stmt->execute();
        $conn->commit();
    } catch (Throwable $e) {
        $conn->rollback();
        return '문제 수정 중 오류가 발생했습니다.';
    }
    return '';
}

function delete_challenge(mysqli $conn, string $challengeId, bool $deleteTemplate): array {
    if (!valid_challenge_id($challengeId)) return [false, '삭제할 문제 ID가 올바르지 않습니다.'];
    $stmt = $conn->prepare('SELECT path FROM challenges WHERE challenge_id = ?');
    $stmt->bind_param('s', $challengeId);
    $stmt->execute();
    $challenge = $stmt->get_result()->fetch_assoc();
    if (!$challenge) return [false, '삭제할 문제를 찾을 수 없습니다.'];
    $templateMessage = '';
    if ($deleteTemplate) {
        $templateFile = generated_template_file_for($challengeId, $challenge['path']);
        if ($templateFile && file_exists($templateFile)) $templateMessage = unlink($templateFile) ? ' 템플릿 파일도 삭제했습니다.' : ' 템플릿 파일은 삭제하지 못했습니다.';
    }
    foreach (['solves', 'submissions', 'challenge_flags', 'challenges'] as $table) {
        $stmt = $conn->prepare("DELETE FROM {$table} WHERE challenge_id = ?");
        $stmt->bind_param('s', $challengeId);
        $stmt->execute();
    }
    return [true, '문제와 관련 기록을 삭제했습니다.' . $templateMessage];
}

function delete_team(mysqli $conn, int $teamId): array {
    if ($teamId <= 0) return [false, '삭제할 팀원을 찾을 수 없습니다.'];
    $stmt = $conn->prepare('SELECT id, name FROM teams WHERE id = ?');
    $stmt->bind_param('i', $teamId);
    $stmt->execute();
    $team = $stmt->get_result()->fetch_assoc();
    if (!$team) return [false, '삭제할 팀원이 이미 존재하지 않습니다.'];
    $conn->begin_transaction();
    try {
        foreach (['submissions', 'solves'] as $table) {
            $stmt = $conn->prepare("DELETE FROM {$table} WHERE team_id = ?");
            $stmt->bind_param('i', $teamId);
            $stmt->execute();
        }
        $stmt = $conn->prepare('DELETE FROM teams WHERE id = ?');
        $stmt->bind_param('i', $teamId);
        $stmt->execute();
        $conn->commit();
    } catch (Throwable $e) {
        $conn->rollback();
        return [false, '팀원 삭제 중 오류가 발생했습니다.'];
    }
    return [true, '전과 현황에서 ' . h($team['name']) . ' 팀원을 삭제했습니다.'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    if (hash_equals($adminPassword, $_POST['password'] ?? '')) {
        $_SESSION['admin_ok'] = true;
        header('Location: /admin/');
        exit;
    }
    $error = '관리자 비밀번호가 맞지 않습니다.';
}
if (($_GET['logout'] ?? '') === '1') {
    $_SESSION = [];
    session_destroy();
    header('Location: /admin/');
    exit;
}

$isAuthed = $_SESSION['admin_ok'] ?? false;
$conn = null;
$challenges = [];
$teams = [];
$editingChallenge = null;
$editingTemplateContent = null;

if ($isAuthed) {
    $conn = db();
    $action = $_POST['action'] ?? '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
        $data = form_data_from_post();
        if ($data['path'] === '' && isset($_POST['create_template']) && valid_challenge_id($data['challenge_id'])) [$data['path']] = challenge_template_path($data['challenge_id']);
        $error = validate_challenge_data($data);
        if ($error === '' && challenge_exists($conn, $data['challenge_id'])) $error = '같은 문제 ID가 이미 존재합니다.';
        $templateMessage = '';
        if ($error === '' && isset($_POST['create_template'])) {
            [$ok, $templateMessage, $generatedPath] = write_challenge_template($data, isset($_POST['overwrite_template']));
            if (!$ok) $error = $templateMessage; else $data['path'] = $generatedPath;
        }
        if ($error === '') {
            insert_challenge($conn, $data);
            $message = '새 문제를 생성했습니다.' . ($templateMessage ? ' ' . $templateMessage : '');
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
        $originalId = trim($_POST['original_challenge_id'] ?? '');
        $data = form_data_from_post();
        $error = validate_challenge_data($data);
        if ($error === '') $error = update_challenge($conn, $originalId, $data);
        $contentMessage = '';
        if ($error === '' && isset($_POST['save_template_content']) && isset($_POST['template_content'])) {
            [$contentOk, $contentMessage] = write_generated_template_content($data['challenge_id'], $data['path'], $_POST['template_content']);
            if (!$contentOk) $error = $contentMessage;
        }
        if ($error === '') {
            header('Location: /admin/?edit=' . rawurlencode($data['challenge_id']) . '&saved=1');
            exit;
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
        [$ok, $deleteMessage] = delete_challenge($conn, trim($_POST['challenge_id'] ?? ''), isset($_POST['delete_template']));
        $ok ? $message = $deleteMessage : $error = $deleteMessage;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete_team') {
        [$ok, $teamMessage] = delete_team($conn, (int) ($_POST['team_id'] ?? 0));
        $ok ? $message = $teamMessage : $error = $teamMessage;
    }
    if (($_GET['saved'] ?? '') === '1') $message = '문제 정보를 수정했습니다.';

    $result = $conn->query('SELECT c.challenge_id, c.title, c.category, c.difficulty, c.points, c.path, c.summary, c.is_active, c.sort_order, f.flag FROM challenges c LEFT JOIN challenge_flags f ON f.challenge_id = c.challenge_id ORDER BY c.sort_order ASC, c.challenge_id ASC');
    if ($result) while ($row = $result->fetch_assoc()) $challenges[] = $row;
    $teamResult = $conn->query('SELECT t.id, t.name, t.created_at, COALESCE(s.solved_count, 0) AS solved_count, COALESCE(sb.submission_count, 0) AS submission_count, COALESCE(s.score, 0) AS score FROM teams t LEFT JOIN (SELECT s.team_id, COUNT(*) AS solved_count, COALESCE(SUM(c.points), 0) AS score FROM solves s LEFT JOIN challenges c ON c.challenge_id = s.challenge_id GROUP BY s.team_id) s ON s.team_id = t.id LEFT JOIN (SELECT team_id, COUNT(*) AS submission_count FROM submissions GROUP BY team_id) sb ON sb.team_id = t.id ORDER BY score DESC, solved_count DESC, t.created_at ASC');
    if ($teamResult) while ($row = $teamResult->fetch_assoc()) $teams[] = $row;
    $editId = trim($_GET['edit'] ?? '');
    if ($editId !== '' && valid_challenge_id($editId)) {
        $stmt = $conn->prepare('SELECT c.challenge_id, c.title, c.category, c.difficulty, c.points, c.path, c.summary, c.is_active, c.sort_order, f.flag FROM challenges c LEFT JOIN challenge_flags f ON f.challenge_id = c.challenge_id WHERE c.challenge_id = ?');
        $stmt->bind_param('s', $editId);
        $stmt->execute();
        $editingChallenge = $stmt->get_result()->fetch_assoc() ?: null;
        if ($editingChallenge) {
            $templateFile = generated_template_file_for($editingChallenge['challenge_id'], $editingChallenge['path']);
            if ($templateFile && is_readable($templateFile)) $editingTemplateContent = file_get_contents($templateFile);
        }
    }
}

function difficulty_options(string $current): string {
    $levels = ['초급', '중급', '중급 입문', '중상급', '상급 입문', '고급', 'Easy', 'Medium', 'Hard'];
    $html = '';
    foreach ($levels as $level) {
        $html .= '<option ' . ($current === $level ? 'selected' : '') . '>' . h($level) . '</option>';
    }
    return $html;
}
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin | EST 전술보안 인트라넷</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
  </head>
  <body>
    <header class="topbar">
      <a class="brand" href="/"><span class="brand-mark">작전</span><span><strong>EST 전술보안 인트라넷</strong><small>관리자 통제소</small></span></a>
      <nav class="nav"><a href="/">작전 과제</a><?php if ($isAuthed): ?><a href="/admin/?logout=1">로그아웃</a><?php endif; ?></nav>
    </header>
    <main class="admin-page">
      <?php if (!$isAuthed): ?>
        <section class="challenge-detail standalone admin-login-panel">
          <div class="detail-header"><div><h2>관리자 로그인</h2><div class="meta"><span class="pill">Problem Manager</span></div></div></div>
          <?php if ($error): ?><p class="notice error"><?= h($error) ?></p><?php endif; ?>
          <form class="admin-form" method="post"><input type="hidden" name="action" value="login" /><label>관리자 암호<input name="password" type="password" autocomplete="current-password" /></label><button class="primary-button" type="submit">로그인</button></form>
        </section>
      <?php else: ?>
        <?php if ($message): ?><p class="notice success admin-notice"><?= h($message) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="notice error admin-notice"><?= h($error) ?></p><?php endif; ?>
        <section class="admin-tabs" aria-label="admin actions"><a class="ghost-button <?= $editingChallenge ? '' : 'active-tab' ?>" href="/admin/">새 작전 과제</a><a class="ghost-button <?= $editingChallenge ? 'active-tab' : '' ?>" href="<?= $editingChallenge ? '/admin/?edit=' . h($editingChallenge['challenge_id']) : '#challenge-list' ?>">기존 과제 수정</a><a class="ghost-button" href="#challenge-list">과제 목록 / 삭제</a><a class="ghost-button" href="#team-list">전과 현황 팀원</a></section>
        <section class="admin-layout split-admin-layout">
          <article class="challenge-detail admin-editor">
            <?php if ($editingChallenge): ?>
              <div class="detail-header"><div><h2>기존 과제 수정</h2><div class="meta"><span class="pill"><?= h($editingChallenge['challenge_id']) ?></span><span class="pill">불러오기 완료</span></div></div></div>
              <form class="admin-form" method="post"><input type="hidden" name="action" value="update" /><input type="hidden" name="original_challenge_id" value="<?= h($editingChallenge['challenge_id']) ?>" />
                <label>과제 ID<input name="challenge_id" value="<?= h($editingChallenge['challenge_id']) ?>" pattern="[a-z0-9][a-z0-9-]{1,62}" required /></label><p class="form-help">ID를 바꾸면 플래그, 제출 기록, 해결 기록의 과제 ID도 함께 변경됩니다.</p>
                <label>제목<input name="title" value="<?= h($editingChallenge['title']) ?>" required /></label><div class="admin-form-row"><label>분류<input name="category" value="<?= h($editingChallenge['category']) ?>" required /></label><label>난이도<select name="difficulty"><?= difficulty_options($editingChallenge['difficulty']) ?></select></label></div>
                <div class="admin-form-row"><label>전과<input name="points" type="number" min="0" value="<?= h((string) $editingChallenge['points']) ?>" required /></label><label>정렬 순서<input name="sort_order" type="number" value="<?= h((string) $editingChallenge['sort_order']) ?>" required /></label></div>
                <label>페이지 경로<input name="path" value="<?= h($editingChallenge['path']) ?>" required /></label><label>브리핑<textarea name="summary" rows="3" required><?= h($editingChallenge['summary']) ?></textarea></label><label>인증 표식<input name="flag" value="<?= h($editingChallenge['flag'] ?? '') ?>" placeholder="FLAG{...}" required /></label><label class="check-row"><input name="is_active" type="checkbox" <?= ((int) $editingChallenge['is_active']) === 1 ? 'checked' : '' ?> /> 메인 상황판에 표시</label>
                <?php if ($editingTemplateContent !== null): ?><label>PHP 파일 내용<textarea class="code-editor" name="template_content" rows="18"><?= h($editingTemplateContent) ?></textarea></label><label class="check-row"><input name="save_template_content" type="checkbox" checked /> PHP 파일 내용 저장</label><?php endif; ?>
                <div class="admin-actions"><button class="primary-button" type="submit">수정 저장</button><a class="ghost-button" href="/admin/">생성 화면으로</a></div>
              </form>
            <?php else: ?>
              <div class="detail-header"><div><h2>새 작전 과제</h2><div class="meta"><span class="pill">Create</span><span class="pill">Template optional</span></div></div></div>
              <form class="admin-form" method="post"><input type="hidden" name="action" value="create" /><label>과제 ID<input name="challenge_id" placeholder="new-operation" pattern="[a-z0-9][a-z0-9-]{1,62}" required /></label><label>제목<input name="title" placeholder="New Operation" required /></label><div class="admin-form-row"><label>분류<input name="category" value="Web" required /></label><label>난이도<select name="difficulty"><?= difficulty_options('초급') ?></select></label></div><div class="admin-form-row"><label>전과<input name="points" type="number" min="0" value="200" required /></label><label>정렬 순서<input name="sort_order" type="number" value="100" required /></label></div><label>페이지 경로<input name="path" placeholder="/challenges/generated/new-operation.php" /></label><p class="form-help">템플릿 생성을 켜면 과제 ID 기준으로 경로가 자동 생성됩니다.</p><label>브리핑<textarea name="summary" rows="3" required></textarea></label><label>인증 표식<input name="flag" placeholder="FLAG{...}" required /></label><label class="check-row"><input name="create_template" type="checkbox" checked /> PHP 템플릿 생성</label><label class="check-row"><input name="overwrite_template" type="checkbox" /> 기존 파일 교체</label><label class="check-row"><input name="is_active" type="checkbox" checked /> 메인 상황판에 표시</label><div class="admin-actions"><button class="primary-button" type="submit">새 과제 생성</button></div></form>
            <?php endif; ?>
          </article>
          <aside class="challenge-detail admin-list" id="challenge-list"><div class="detail-header"><div><h2>과제 목록</h2><div class="meta"><span class="pill"><?= count($challenges) ?> items</span><span class="pill">클릭하면 수정</span></div></div></div><div class="admin-card-list"><?php foreach ($challenges as $challenge): ?><article class="admin-challenge-card <?= ($editingChallenge && $editingChallenge['challenge_id'] === $challenge['challenge_id']) ? 'selected' : '' ?>" onclick="location.href='/admin/?edit=<?= h($challenge['challenge_id']) ?>'"><div><strong><?= h($challenge['title']) ?></strong><code><?= h($challenge['challenge_id']) ?></code><small><?= h($challenge['path']) ?></small></div><div class="admin-card-meta"><span class="pill"><?= h($challenge['category']) ?></span><span class="pill"><?= h($challenge['difficulty']) ?></span><span class="pill"><?= (int) $challenge['points'] ?> 전과</span><span class="pill <?= ((int) $challenge['is_active']) === 1 ? 'solved' : '' ?>"><?= ((int) $challenge['is_active']) === 1 ? 'ON' : 'OFF' ?></span></div><form class="inline-delete" method="post" onclick="event.stopPropagation()" onsubmit="return confirm('이 과제를 삭제할까요? 제출 기록도 함께 삭제됩니다.');"><input type="hidden" name="action" value="delete" /><input type="hidden" name="challenge_id" value="<?= h($challenge['challenge_id']) ?>" /><label><input name="delete_template" type="checkbox" checked /> 파일도 삭제</label><button class="danger-button" type="submit">삭제</button></form></article><?php endforeach; ?></div></aside>
        </section>
        <section class="challenge-detail admin-list admin-team-list" id="team-list"><div class="detail-header"><div><h2>전과 현황 팀원 관리</h2><div class="meta"><span class="pill"><?= count($teams) ?> teams</span><span class="pill">삭제 시 전과 기록 제거</span></div></div></div><div class="admin-card-list"><?php if (!$teams): ?><p class="form-help">등록된 팀원이 없습니다.</p><?php endif; ?><?php foreach ($teams as $team): ?><article class="admin-challenge-card"><div><strong><?= h($team['name']) ?></strong><code>#<?= (int) $team['id'] ?></code><small>등록 <?= h($team['created_at']) ?></small></div><div class="admin-card-meta"><span class="pill solved"><?= (int) $team['score'] ?> 전과</span><span class="pill"><?= (int) $team['solved_count'] ?> 해결</span><span class="pill"><?= (int) $team['submission_count'] ?> 보고</span></div><form class="inline-delete" method="post" onsubmit="return confirm('이 팀원을 전과 현황에서 삭제할까요? 제출/해결 기록도 함께 삭제됩니다.');"><input type="hidden" name="action" value="delete_team" /><input type="hidden" name="team_id" value="<?= (int) $team['id'] ?>" /><button class="danger-button" type="submit">팀원 삭제</button></form></article><?php endforeach; ?></div></section>
      <?php endif; ?>
    </main>
  </body>
</html>