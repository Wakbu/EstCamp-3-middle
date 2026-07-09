from pathlib import Path
root = Path(r'C:\Users\최준용\Documents\[이스트캠프] 워게임 중간 대회')

# index: modal visible by default, reset button in status panel
p = root / 'index.html'
s = p.read_text(encoding='utf-8')
s = s.replace('<div class="modal-overlay" id="tutorial-modal" aria-hidden="true">', '<div class="modal-overlay visible" id="tutorial-modal" aria-hidden="false">')
old = '''          <form class="team-form" id="team-form">
            <label for="team-name">&#xD300;&#xBA85;</label>
            <div>
              <input id="team-name" name="team" maxlength="64" autocomplete="off" />
              <button class="ghost-button" type="submit">&#xC801;&#xC6A9;</button>
            </div>
          </form>'''
new = old + '''
          <button class="ghost-button reset-button" id="reset-progress" type="button">&#xC644;&#xB8CC; &#xAE30;&#xB85D; &#xCD08;&#xAE30;&#xD654;</button>'''
if 'reset-progress' not in s:
    s = s.replace(old, new)
p.write_text(s, encoding='utf-8')

# app: modal hides when seen, reset progress
p = root / 'app.js'
s = p.read_text(encoding='utf-8')
s = s.replace('''  if (localStorage.getItem("tutorialSeen") !== "1") {
    modal.classList.add("visible");
    modal.setAttribute("aria-hidden", "false");
  }''', '''  if (localStorage.getItem("tutorialSeen") === "1") {
    modal.classList.remove("visible");
    modal.setAttribute("aria-hidden", "true");
  } else {
    modal.classList.add("visible");
    modal.setAttribute("aria-hidden", "false");
  }''')
insert = '''
async function resetProgress() {
  if (!window.confirm("현재 팀의 완료 기록을 초기화할까요?")) return;
  try {
    const result = await fetchJson("/api/reset.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ team: teamName() }),
    });
    if (result.ok) {
      solved = new Set();
      showToast("&#xC644;&#xB8CC; &#xAE30;&#xB85D;&#xC744; &#xCD08;&#xAE30;&#xD654;&#xD588;&#xC2B5;&#xB2C8;&#xB2E4;.");
      await loadScoreboard();
    }
  } catch (error) {
    showToast("&#xCD08;&#xAE30;&#xD654; &#xC911; &#xC624;&#xB958;&#xAC00; &#xBC1C;&#xC0DD;&#xD588;&#xC2B5;&#xB2C8;&#xB2E4;.");
  }
}
'''
if 'async function resetProgress' not in s:
    s = s.replace('document.querySelector("#refresh-board")?.addEventListener', insert + '\ndocument.querySelector("#refresh-board")?.addEventListener')
    s = s.replace('''document.querySelector("#refresh-board")?.addEventListener("click", async () => {
  await loadScoreboard();
  showToast(text.boardRefreshed);
});''', '''document.querySelector("#refresh-board")?.addEventListener("click", async () => {
  await loadScoreboard();
  showToast(text.boardRefreshed);
});

document.querySelector("#reset-progress")?.addEventListener("click", resetProgress);''')
p.write_text(s, encoding='utf-8')

# CSS: reset button and stricter modal positioning
p = root / 'styles.css'
s = p.read_text(encoding='utf-8')
extra = '''
.reset-button {
  width: 100%;
  margin-top: 0.75rem;
}

body:has(.modal-overlay.visible) {
  overflow: hidden;
}
'''
if '.reset-button' not in s:
    s = s.rstrip() + '\n' + extra
# ensure modal is fixed and visible above all
s = s.replace('z-index: 20;\n  display: none;', 'z-index: 1000;\n  display: none;')
p.write_text(s, encoding='utf-8')

# api reset endpoint
p = root / 'api' / 'reset.php'
p.write_text(r'''<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

function normalize_team_name(string $name): string {
    $name = trim($name);
    if ($name === '') {
        return 'you';
    }
    return substr($name, 0, 64);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$teamName = normalize_team_name($_POST['team'] ?? 'you');
$conn = db();

$stmt = $conn->prepare('SELECT id FROM teams WHERE name = ? LIMIT 1');
$stmt->bind_param('s', $teamName);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(['ok' => true, 'team' => $teamName], JSON_UNESCAPED_UNICODE);
    exit;
}

$teamId = (int) $row['id'];
$stmt = $conn->prepare('DELETE FROM solves WHERE team_id = ?');
$stmt->bind_param('i', $teamId);
$stmt->execute();

$stmt = $conn->prepare('DELETE FROM submissions WHERE team_id = ?');
$stmt->bind_param('i', $teamId);
$stmt->execute();

echo json_encode(['ok' => true, 'team' => $teamName], JSON_UNESCAPED_UNICODE);
''', encoding='utf-8')

# challenge pages: hints and cookie role display
p = root / 'challenges' / 'session-shadow.php'
s = p.read_text(encoding='utf-8')
s = s.replace('''          <h1>&#xBCF4;&#xC774;&#xC9C0; &#xC54A;&#xB294; &#xC5ED;&#xD560;</h1>
          <p>&#xD398;&#xC774;&#xC9C0;&#xB294; &#xC0AC;&#xC6A9;&#xC790;&#xC758; &#xC0C1;&#xD0DC;&#xB97C; &#xC5B4;&#xB514;&#xC5D0;&#xC120;&#xAC00; &#xAE30;&#xC5B5;&#xD558;&#xACE0; &#xC788;&#xC2B5;&#xB2C8;&#xB2E4;. &#xD604;&#xC7AC; &#xC5ED;&#xD560;&#xC774; &#xC5B4;&#xB514;&#xC11C; &#xC624;&#xB294;&#xC9C0; &#xAD00;&#xCC30;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p>
          <div class="terminal">
            <span>current role</span>
            <code><?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?></code>
            <span>required role</span>
            <code>admin</code>
          </div>''', '''          <h1>&#xBCF4;&#xC774;&#xC9C0; &#xC54A;&#xB294; &#xC5ED;&#xD560;</h1>
          <p>&#xC811;&#xADFC; &#xACB0;&#xACFC;&#xAC00; &#xC0AC;&#xC6A9;&#xC790; &#xC0C1;&#xD0DC;&#xC5D0; &#xB530;&#xB77C; &#xB2EC;&#xB77C;&#xC9D1;&#xB2C8;&#xB2E4;. &#xD398;&#xC774;&#xC9C0;&#xAC00; &#xB0B4; &#xC0C1;&#xD0DC;&#xB97C; &#xC5B4;&#xB5BB;&#xAC8C; &#xAD6C;&#xBD84;&#xD558;&#xB294;&#xC9C0; &#xCC3E;&#xC544;&#xBCF4;&#xC138;&#xC694;.</p>
          <div class="terminal">
            <span>access status</span>
            <code><?php echo $role === 'admin' ? 'granted' : 'limited'; ?></code>
          </div>''')
s = s.replace('''<p>&#xD654;&#xBA74;&#xC5D0; &#xBCF4;&#xC774;&#xB294; &#xAC12;&#xB9CC; &#xBCF4;&#xC9C0; &#xB9D0;&#xACE0;, &#xBE0C;&#xB77C;&#xC6B0;&#xC800;&#xAC00; &#xD568;&#xAED8; &#xBCF4;&#xB0B4;&#xB294; &#xC815;&#xBCF4;&#xB97C; &#xD655;&#xC778;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p>''', '''<details open><summary>1&#xB2E8;&#xACC4;</summary><p>&#xAC1C;&#xBC1C;&#xC790; &#xB3C4;&#xAD6C;&#xC758; Application/Storage &#xC601;&#xC5ED;&#xC5D0;&#xC11C; &#xC774; &#xC0AC;&#xC774;&#xD2B8;&#xAC00; &#xC800;&#xC7A5;&#xD55C; &#xAC12;&#xC744; &#xD655;&#xC778;&#xD558;&#xC138;&#xC694;.</p></details>
              <details><summary>2&#xB2E8;&#xACC4;</summary><p>&#xC5ED;&#xD560;&#xC744; &#xB098;&#xD0C0;&#xB0B4;&#xB294; &#xAC12;&#xC774; &#xC788;&#xB2E4;&#xBA74;, &#xC811;&#xADFC; &#xC0C1;&#xD0DC;&#xAC00; &#xB2EC;&#xB77C;&#xC9C0;&#xB294;&#xC9C0; &#xC870;&#xC2EC;&#xC2A4;&#xB7FD;&#xAC8C; &#xBC14;&#xAFB8;&#xC5B4;&#xBCF4;&#xC138;&#xC694;.</p></details>''')
p.write_text(s, encoding='utf-8')