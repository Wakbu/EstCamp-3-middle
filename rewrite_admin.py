from pathlib import Path
root = Path(r'C:\Users\理쒖???Documents\[?댁뒪?몄틺?? ?뚭쾶??以묎컙 ???)
admin = root / 'admin' / 'index.php'
admin.write_text(r'''<?php
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
    $pageTitle = h($data['title'] . ' | EST Wargame Lab');

    return <<<HTML
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{$pageTitle}</title>
    <link rel="stylesheet" href="/styles.css" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">??臾몄젣 紐⑸줉</a>
      <section class="challenge-detail">
        <div class="section-heading"><span>{$title}</span><small>{$category} / {$difficulty}</small></div>
        <div class="challenge-body">
          <p class="eyebrow">{$points} pts</p>
          <h1>{$title}</h1>
          <p>{$summary}</p>
          <div class="hint-box visible staged-hints">
            <strong>?뚰듃</strong>
            <details><summary>1?④퀎</summary><p>泥?踰덉㎏ 愿李??ъ씤?몃? ?묒꽦?섏꽭??</p></details>
            <details><summary>2?④퀎</summary><p>留됲삍?????뺤씤????踰덉㎏ 諛⑺뼢???묒꽦?섏꽭??</p></details>
          </div>
          <form class="submit-row" data-flag-form data-challenge-id="{$challengeId}">
            <input name="flag" placeholder="EST{...}" autocomplete="off" />
            <button class="primary-button" type="submit">?쒖텧</button>
          </form>
        </div>
      </section>
    </main>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
    <script src="/app.js"></script>
  </body>
</html>
HTML;
}

function generated_template_file_for(string $challengeId, string $path): ?string {
    [$expectedPath, $directory, $filePath] = challenge_template_path($challengeId);
    if ($path !== $expectedPath) {
        return null;
    }
    $base = realpath($directory);
    if ($base === false) {
        return null;
    }
    $target = realpath($filePath);
    if ($target === false || dirname($target) !== $base) {
        return null;
    }
    return $target;
}

function write_generated_template_content(string $challengeId, string $path, string $content): array {
    $templateFile = generated_template_file_for($challengeId, $path);
    if (!$templateFile) {
        return [false, '?먮룞 ?앹꽦??PHP ?쒗뵆由용쭔 ?몄쭛?????덉뒿?덈떎.'];
    }
    if (file_put_contents($templateFile, $content, LOCK_EX) === false) {
        return [false, 'PHP ?뚯씪 ?댁슜????ν븯吏 紐삵뻽?듬땲??'];
    }
    return [true, ' PHP ?뚯씪 ?댁슜????ν뻽?듬땲??'];
}

function write_challenge_template(array $data, bool $overwrite): array {
    [$relativePath, $directory, $filePath] = challenge_template_path($data['challenge_id']);
    if (!is_dir($directory) && !mkdir($directory, 0750, true)) {
        return [false, '?쒗뵆由??대뜑瑜?留뚮뱾 ???놁뒿?덈떎.', $relativePath];
    }
    $base = realpath($directory);
    if ($base === false) {
        return [false, '?쒗뵆由?寃쎈줈瑜??뺤씤?????놁뒿?덈떎.', $relativePath];
    }
    $targetDir = realpath(dirname($filePath));
    if ($targetDir === false || $targetDir !== $base) {
        return [false, '?덉슜?섏? ?딆? 寃쎈줈?낅땲??', $relativePath];
    }
    if (file_exists($filePath) && !$overwrite) {
        return [false, '媛숈? ?쒗뵆由??뚯씪???대? ?덉뒿?덈떎.', $relativePath];
    }
    if (file_put_contents($filePath, build_challenge_template($data), LOCK_EX) === false) {
        return [false, '?쒗뵆由??뚯씪????ν븯吏 紐삵뻽?듬땲??', $relativePath];
    }
    return [true, '?쒗뵆由??뚯씪???앹꽦?덉뒿?덈떎: ' . h($relativePath), $relativePath];
}

function form_data_from_post(): array {
    return [
        'challenge_id' => trim($_POST['challenge_id'] ?? ''),
        'title' => trim($_POST['title'] ?? ''),
        'category' => trim($_POST['category'] ?? 'Web'),
        'difficulty' => trim($_POST['difficulty'] ?? 'Easy'),
        'points' => max(0, (int) ($_POST['points'] ?? 0)),
        'path' => trim($_POST['path'] ?? ''),
        'summary' => trim($_POST['summary'] ?? ''),
        'flag' => trim($_POST['flag'] ?? ''),
        'sort_order' => (int) ($_POST['sort_order'] ?? 100),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
}

function validate_challenge_data(array $data): string {
    if (!valid_challenge_id($data['challenge_id'])) {
        return '臾몄젣 ID???곷Ц ?뚮Ц?? ?レ옄, ?섏씠?덈쭔 ?ъ슜?????덉뒿?덈떎.';
    }
    if ($data['title'] === '' || $data['path'] === '' || $data['summary'] === '' || $data['flag'] === '') {
        return '?쒕ぉ, 寃쎈줈, ?ㅻ챸, ?뚮옒洹몃뒗 ?꾩닔?낅땲??';
    }
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
    if (!valid_challenge_id($originalId)) {
        return '?섏젙???먮낯 臾몄젣 ID媛 ?щ컮瑜댁? ?딆뒿?덈떎.';
    }
    if (!challenge_exists($conn, $originalId)) {
        return '?섏젙??臾몄젣瑜?李얠쓣 ???놁뒿?덈떎.';
    }
    if ($originalId !== $data['challenge_id'] && challenge_exists($conn, $data['challenge_id'])) {
        return '蹂寃쏀븯?ㅻ뒗 臾몄젣 ID媛 ?대? 議댁옱?⑸땲??';
    }

    $conn->begin_transaction();
    try {
        if ($originalId !== $data['challenge_id']) {
            $stmt = $conn->prepare('UPDATE challenges SET challenge_id = ? WHERE challenge_id = ?');
            $stmt->bind_param('ss', $data['challenge_id'], $originalId);
            $stmt->execute();

            $stmt = $conn->prepare('UPDATE challenge_flags SET challenge_id = ? WHERE challenge_id = ?');
            $stmt->bind_param('ss', $data['challenge_id'], $originalId);
            $stmt->execute();

            $stmt = $conn->prepare('UPDATE submissions SET challenge_id = ? WHERE challenge_id = ?');
            $stmt->bind_param('ss', $data['challenge_id'], $originalId);
            $stmt->execute();

            $stmt = $conn->prepare('UPDATE solves SET challenge_id = ? WHERE challenge_id = ?');
            $stmt->bind_param('ss', $data['challenge_id'], $originalId);
            $stmt->execute();
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
        return '臾몄젣 ?섏젙 以??ㅻ쪟媛 諛쒖깮?덉뒿?덈떎.';
    }
    return '';
}

function delete_challenge(mysqli $conn, string $challengeId, bool $deleteTemplate): array {
    if (!valid_challenge_id($challengeId)) {
        return [false, '??젣??臾몄젣 ID媛 ?щ컮瑜댁? ?딆뒿?덈떎.'];
    }
    $stmt = $conn->prepare('SELECT path FROM challenges WHERE challenge_id = ?');
    $stmt->bind_param('s', $challengeId);
    $stmt->execute();
    $challenge = $stmt->get_result()->fetch_assoc();
    if (!$challenge) {
        return [false, '??젣??臾몄젣瑜?李얠쓣 ???놁뒿?덈떎.'];
    }

    $templateMessage = '';
    if ($deleteTemplate) {
        $templateFile = generated_template_file_for($challengeId, $challenge['path']);
        if ($templateFile && file_exists($templateFile)) {
            $templateMessage = unlink($templateFile) ? ' ?쒗뵆由??뚯씪????젣?덉뒿?덈떎.' : ' ?쒗뵆由??뚯씪? ??젣?섏? 紐삵뻽?듬땲??';
        }
    }

    foreach (['solves', 'submissions', 'challenge_flags', 'challenges'] as $table) {
        $stmt = $conn->prepare("DELETE FROM {$table} WHERE challenge_id = ?");
        $stmt->bind_param('s', $challengeId);
        $stmt->execute();
    }
    return [true, '臾몄젣? 愿??湲곕줉????젣?덉뒿?덈떎.' . $templateMessage];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    if (hash_equals($adminPassword, $_POST['password'] ?? '')) {
        $_SESSION['admin_ok'] = true;
        header('Location: /admin/');
        exit;
    }
    $error = '愿由ъ옄 鍮꾨?踰덊샇媛 留욎? ?딆뒿?덈떎.';
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
$editingChallenge = null;
$editingTemplateContent = null;

if ($isAuthed) {
    $conn = db();
    $action = $_POST['action'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
        $data = form_data_from_post();
        if (($data['path'] === '') && isset($_POST['create_template']) && valid_challenge_id($data['challenge_id'])) {
            [$data['path']] = challenge_template_path($data['challenge_id']);
        }
        $error = validate_challenge_data($data);
        if ($error === '' && challenge_exists($conn, $data['challenge_id'])) {
            $error = '媛숈? 臾몄젣 ID媛 ?대? 議댁옱?⑸땲??';
        }
        $templateMessage = '';
        if ($error === '' && isset($_POST['create_template'])) {
            [$ok, $templateMessage, $generatedPath] = write_challenge_template($data, isset($_POST['overwrite_template']));
            if (!$ok) {
                $error = $templateMessage;
            } else {
                $data['path'] = $generatedPath;
            }
        }
        if ($error === '') {
            insert_challenge($conn, $data);
            $message = '??臾몄젣瑜??앹꽦?덉뒿?덈떎.' . ($templateMessage ? ' ' . $templateMessage : '');
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
        $originalId = trim($_POST['original_challenge_id'] ?? '');
        $data = form_data_from_post();
        $error = validate_challenge_data($data);
        if ($error === '') {
            $error = update_challenge($conn, $originalId, $data);
        }
        $contentMessage = '';
        if ($error === '' && isset($_POST['save_template_content']) && isset($_POST['template_content'])) {
            [$contentOk, $contentMessage] = write_generated_template_content($data['challenge_id'], $data['path'], $_POST['template_content']);
            if (!$contentOk) {
                $error = $contentMessage;
            }
        }
        if ($error === '') {
            $message = '臾몄젣 ?뺣낫瑜??섏젙?덉뒿?덈떎.' . $contentMessage;
            header('Location: /admin/?edit=' . rawurlencode($data['challenge_id']) . '&saved=1');
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
        [$ok, $deleteMessage] = delete_challenge($conn, trim($_POST['challenge_id'] ?? ''), isset($_POST['delete_template']));
        if ($ok) {
            $message = $deleteMessage;
        } else {
            $error = $deleteMessage;
        }
    }

    if (($_GET['saved'] ?? '') === '1') {
        $message = '臾몄젣 ?뺣낫瑜??섏젙?덉뒿?덈떎.';
    }

    $result = $conn->query(
        'SELECT c.challenge_id, c.title, c.category, c.difficulty, c.points, c.path, c.summary, c.is_active, c.sort_order, f.flag
         FROM challenges c
         LEFT JOIN challenge_flags f ON f.challenge_id = c.challenge_id
         ORDER BY c.sort_order ASC, c.challenge_id ASC'
    );
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $challenges[] = $row;
        }
    }

    $editId = trim($_GET['edit'] ?? '');
    if ($editId !== '' && valid_challenge_id($editId)) {
        $stmt = $conn->prepare(
            'SELECT c.challenge_id, c.title, c.category, c.difficulty, c.points, c.path, c.summary, c.is_active, c.sort_order, f.flag
             FROM challenges c
             LEFT JOIN challenge_flags f ON f.challenge_id = c.challenge_id
             WHERE c.challenge_id = ?'
        );
        $stmt->bind_param('s', $editId);
        $stmt->execute();
        $editingChallenge = $stmt->get_result()->fetch_assoc() ?: null;
        if ($editingChallenge) {
            $templateFile = generated_template_file_for($editingChallenge['challenge_id'], $editingChallenge['path']);
            if ($templateFile && is_readable($templateFile)) {
                $editingTemplateContent = file_get_contents($templateFile);
            }
        }
    }
}

function difficulty_options(string $current): string {
    $levels = ['Easy', 'Medium', 'Hard', '以묓븯', '以?, '以묒긽', '??];
    $html = '';
    foreach ($levels as $level) {
        $selected = $current === $level ? 'selected' : '';
        $html .= '<option ' . $selected . '>' . h($level) . '</option>';
    }
    return $html;
}
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin | EST Wargame Lab</title>
    <link rel="stylesheet" href="/styles.css" />
  </head>
  <body>
    <header class="topbar">
      <a class="brand" href="/"><span class="brand-mark">EST</span><span><strong>EST Wargame Lab</strong><small>愿由ъ옄 肄섏넄</small></span></a>
      <nav class="nav"><a href="/">臾몄젣 紐⑸줉</a><?php if ($isAuthed): ?><a href="/admin/?logout=1">濡쒓렇?꾩썐</a><?php endif; ?></nav>
    </header>

    <main class="admin-page">
      <?php if (!$isAuthed): ?>
        <section class="challenge-detail standalone admin-login-panel">
          <div class="detail-header"><div><h2>愿由ъ옄 濡쒓렇??/h2><div class="meta"><span class="pill">Problem Manager</span></div></div></div>
          <?php if ($error): ?><p class="notice error"><?= h($error) ?></p><?php endif; ?>
          <form class="admin-form" method="post">
            <input type="hidden" name="action" value="login" />
            <label>愿由ъ옄 鍮꾨?踰덊샇<input name="password" type="password" autocomplete="current-password" /></label>
            <button class="primary-button" type="submit">濡쒓렇??/button>
          </form>
        </section>
      <?php else: ?>
        <?php if ($message): ?><p class="notice success admin-notice"><?= h($message) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="notice error admin-notice"><?= h($error) ?></p><?php endif; ?>

        <section class="admin-tabs" aria-label="admin actions">
          <a class="ghost-button <?= $editingChallenge ? '' : 'active-tab' ?>" href="/admin/">??臾몄젣 ?앹꽦</a>
          <a class="ghost-button <?= $editingChallenge ? 'active-tab' : '' ?>" href="<?= $editingChallenge ? '/admin/?edit=' . h($editingChallenge['challenge_id']) : '#challenge-list' ?>">湲곗〈 臾몄젣 ?섏젙</a>
          <a class="ghost-button" href="#challenge-list">臾몄젣 紐⑸줉 / ??젣</a>
        </section>

        <section class="admin-layout split-admin-layout">
          <article class="challenge-detail admin-editor">
            <?php if ($editingChallenge): ?>
              <div class="detail-header"><div><h2>湲곗〈 臾몄젣 ?섏젙</h2><div class="meta"><span class="pill"><?= h($editingChallenge['challenge_id']) ?></span><span class="pill">?먮룞 遺덈윭?ㅺ린 ?꾨즺</span></div></div></div>
              <form class="admin-form" method="post">
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="original_challenge_id" value="<?= h($editingChallenge['challenge_id']) ?>" />
                <label>臾몄젣 ID<input name="challenge_id" value="<?= h($editingChallenge['challenge_id']) ?>" pattern="[a-z0-9][a-z0-9-]{1,62}" required /></label>
                <p class="form-help">ID瑜?諛붽씀硫??뚮옒洹? ?쒖텧 湲곕줉, ?닿껐 湲곕줉??臾몄젣 ID???④퍡 蹂寃쎈맗?덈떎.</p>
                <label>?쒕ぉ<input name="title" value="<?= h($editingChallenge['title']) ?>" required /></label>
                <div class="admin-form-row">
                  <label>移댄뀒怨좊━<input name="category" value="<?= h($editingChallenge['category']) ?>" required /></label>
                  <label>?쒖씠??select name="difficulty"><?= difficulty_options($editingChallenge['difficulty']) ?></select></label>
                </div>
                <div class="admin-form-row">
                  <label>?먯닔<input name="points" type="number" min="0" value="<?= h((string) $editingChallenge['points']) ?>" required /></label>
                  <label>?뺣젹 ?쒖꽌<input name="sort_order" type="number" value="<?= h((string) $editingChallenge['sort_order']) ?>" required /></label>
                </div>
                <label>?섏씠吏 寃쎈줈<input name="path" value="<?= h($editingChallenge['path']) ?>" required /></label>
                <label>?ㅻ챸<textarea name="summary" rows="3" required><?= h($editingChallenge['summary']) ?></textarea></label>
                <label>?뚮옒洹?input name="flag" value="<?= h($editingChallenge['flag'] ?? '') ?>" placeholder="EST{...}" required /></label>
                <label class="check-row"><input name="is_active" type="checkbox" <?= ((int) $editingChallenge['is_active']) === 1 ? 'checked' : '' ?> /> 硫붿씤 紐⑸줉???쒖떆</label>
                <?php if ($editingTemplateContent !== null): ?>
                  <label>PHP ?뚯씪 ?댁슜<textarea class="code-editor" name="template_content" rows="18"><?= h($editingTemplateContent) ?></textarea></label>
                  <label class="check-row"><input name="save_template_content" type="checkbox" checked /> PHP ?뚯씪 ?댁슜?????/label>
                <?php endif; ?>
                <div class="admin-actions"><button class="primary-button" type="submit">?섏젙 ???/button><a class="ghost-button" href="/admin/">?앹꽦 ?붾㈃?쇰줈</a></div>
              </form>
            <?php else: ?>
              <div class="detail-header"><div><h2>??臾몄젣 ?앹꽦</h2><div class="meta"><span class="pill">Create</span><span class="pill">Template optional</span></div></div></div>
              <form class="admin-form" method="post">
                <input type="hidden" name="action" value="create" />
                <label>臾몄젣 ID<input name="challenge_id" placeholder="new-problem" pattern="[a-z0-9][a-z0-9-]{1,62}" required /></label>
                <label>?쒕ぉ<input name="title" placeholder="New Problem" required /></label>
                <div class="admin-form-row">
                  <label>移댄뀒怨좊━<input name="category" value="Web" required /></label>
                  <label>?쒖씠??select name="difficulty"><?= difficulty_options('Easy') ?></select></label>
                </div>
                <div class="admin-form-row">
                  <label>?먯닔<input name="points" type="number" min="0" value="200" required /></label>
                  <label>?뺣젹 ?쒖꽌<input name="sort_order" type="number" value="100" required /></label>
                </div>
                <label>?섏씠吏 寃쎈줈<input name="path" placeholder="/challenges/generated/new-problem.php" /></label>
                <p class="form-help">?쒗뵆由??앹꽦??耳쒕㈃ 臾몄젣 ID 湲곗??쇰줈 寃쎈줈媛 ?먮룞 ?앹꽦?⑸땲??</p>
                <label>?ㅻ챸<textarea name="summary" rows="3" required></textarea></label>
                <label>?뚮옒洹?input name="flag" placeholder="EST{...}" required /></label>
                <label class="check-row"><input name="create_template" type="checkbox" checked /> PHP ?쒗뵆由??앹꽦</label>
                <label class="check-row"><input name="overwrite_template" type="checkbox" /> 湲곗〈 ?뚯씪 援먯껜</label>
                <label class="check-row"><input name="is_active" type="checkbox" checked /> 硫붿씤 紐⑸줉???쒖떆</label>
                <div class="admin-actions"><button class="primary-button" type="submit">??臾몄젣 ?앹꽦</button></div>
              </form>
            <?php endif; ?>
          </article>

          <aside class="challenge-detail admin-list" id="challenge-list">
            <div class="detail-header"><div><h2>臾몄젣 紐⑸줉</h2><div class="meta"><span class="pill"><?= count($challenges) ?> items</span><span class="pill">?대┃?섎㈃ ?섏젙</span></div></div></div>
            <div class="admin-card-list">
              <?php foreach ($challenges as $challenge): ?>
                <article class="admin-challenge-card <?= ($editingChallenge && $editingChallenge['challenge_id'] === $challenge['challenge_id']) ? 'selected' : '' ?>" onclick="location.href='/admin/?edit=<?= h($challenge['challenge_id']) ?>'">
                  <div>
                    <strong><?= h($challenge['title']) ?></strong>
                    <code><?= h($challenge['challenge_id']) ?></code>
                    <small><?= h($challenge['path']) ?></small>
                  </div>
                  <div class="admin-card-meta"><span class="pill"><?= h($challenge['category']) ?></span><span class="pill"><?= h($challenge['difficulty']) ?></span><span class="pill"><?= (int) $challenge['points'] ?> pts</span><span class="pill <?= ((int) $challenge['is_active']) === 1 ? 'solved' : '' ?>"><?= ((int) $challenge['is_active']) === 1 ? 'ON' : 'OFF' ?></span></div>
                  <form class="inline-delete" method="post" onclick="event.stopPropagation()" onsubmit="return confirm('??臾몄젣瑜???젣?좉퉴?? ?쒖텧 湲곕줉???④퍡 ??젣?⑸땲??');">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="challenge_id" value="<?= h($challenge['challenge_id']) ?>" />
                    <label><input name="delete_template" type="checkbox" checked /> ?뚯씪????젣</label>
                    <button class="danger-button" type="submit">??젣</button>
                  </form>
                </article>
              <?php endforeach; ?>
            </div>
          </aside>
        </section>
      <?php endif; ?>
    </main>
  </body>
</html>
''', encoding='utf-8')