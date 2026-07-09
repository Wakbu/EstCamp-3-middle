from pathlib import Path
root = Path(r'C:\Users\최준용\Documents\[이스트캠프] 워게임 중간 대회')

def write(name, text):
    (root / 'challenges' / name).write_text(text, encoding='utf-8')

write('session-shadow.php', r'''<?php
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
    <title>Session Shadow | EST Wargame Lab</title>
    <link rel="stylesheet" href="/styles.css" />
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">&#x2190; &#xBB38;&#xC81C; &#xBAA9;&#xB85D;</a>
      <section class="challenge-detail">
        <div class="section-heading">
          <span>Session Shadow</span>
          <small>Cookie</small>
        </div>
        <div class="challenge-body">
          <p class="eyebrow">150 pts / &#xC911;&#xD558;</p>
          <h1>&#xBCF4;&#xC774;&#xC9C0; &#xC54A;&#xB294; &#xC5ED;&#xD560;</h1>
          <p>&#xD398;&#xC774;&#xC9C0;&#xB294; &#xC0AC;&#xC6A9;&#xC790;&#xC758; &#xC0C1;&#xD0DC;&#xB97C; &#xC5B4;&#xB514;&#xC5D0;&#xC120;&#xAC00; &#xAE30;&#xC5B5;&#xD558;&#xACE0; &#xC788;&#xC2B5;&#xB2C8;&#xB2E4;. &#xD604;&#xC7AC; &#xC5ED;&#xD560;&#xC774; &#xC5B4;&#xB514;&#xC11C; &#xC624;&#xB294;&#xC9C0; &#xAD00;&#xCC30;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p>
          <div class="terminal">
            <span>current role</span>
            <code><?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?></code>
            <span>required role</span>
            <code>admin</code>
          </div>
          <?php if ($flag): ?>
            <div class="hint-box visible success-box">
              <strong>FLAG</strong>
              <code><?php echo htmlspecialchars($flag, ENT_QUOTES, 'UTF-8'); ?></code>
            </div>
          <?php else: ?>
            <div class="hint-box visible staged-hints">
              <strong>&#xD78C;&#xD2B8;</strong>
              <p>&#xD654;&#xBA74;&#xC5D0; &#xBCF4;&#xC774;&#xB294; &#xAC12;&#xB9CC; &#xBCF4;&#xC9C0; &#xB9D0;&#xACE0;, &#xBE0C;&#xB77C;&#xC6B0;&#xC800;&#xAC00; &#xD568;&#xAED8; &#xBCF4;&#xB0B4;&#xB294; &#xC815;&#xBCF4;&#xB97C; &#xD655;&#xC778;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p>
            </div>
          <?php endif; ?>
          <form class="submit-row" data-flag-form data-challenge-id="<?php echo $challengeId; ?>">
            <input name="flag" placeholder="EST{...}" autocomplete="off" />
            <button class="primary-button" type="submit">&#xC81C;&#xCD9C;</button>
          </form>
        </div>
      </section>
    </main>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
    <script src="/app.js"></script>
  </body>
</html>
''')

# targeted replacements for other challenge pages
p = root / 'challenges' / 'blind-notice.php'
s = p.read_text(encoding='utf-8')
s = s.replace('''<p>&#xACF5;&#xAC1C; &#xACF5;&#xC9C0;&#xB294; &#xC81C;&#xBAA9; &#xC77C;&#xBD80;&#xB85C; &#xAC80;&#xC0C9;&#xD560; &#xC218; &#xC788;&#xC2B5;&#xB2C8;&#xB2E4;. &#xD14C;&#xC774;&#xBE14; &#xB0B4;&#xC6A9;&#xC740; &#xBCF4;&#xC774;&#xC9C0; &#xC54A;&#xACE0;, &#xACB0;&#xACFC; &#xC874;&#xC7AC; &#xC5EC;&#xBD80;&#xB9CC; &#xBC18;&#xD658;&#xB429;&#xB2C8;&#xB2E4;.</p>''', '''<p>&#xACF5;&#xC9C0; &#xAC80;&#xC0C9;&#xC740; &#xC785;&#xB825;&#xAC12;&#xC5D0; &#xB530;&#xB77C; &#xB2E4;&#xB978; &#xBC18;&#xC751;&#xC744; &#xBCF4;&#xC5EC;&#xC90D;&#xB2C8;&#xB2E4;. &#xACB0;&#xACFC;&#xC758; &#xB0B4;&#xC6A9;&#xBCF4;&#xB2E4; &#xBC18;&#xC751;&#xC774; &#xBC14;&#xB00C;&#xB294; &#xC870;&#xAC74;&#xC5D0; &#xC9D1;&#xC911;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p>''')
s = s.replace('''<div class="terminal">
            public notice columns: id, title, body, is_public
          </div>''', '''<div class="hint-box visible staged-hints">
            <strong>&#xB2E8;&#xACC4;&#xD615; &#xD78C;&#xD2B8;</strong>
            <p>&#xB108;&#xBB34; &#xC5B4;&#xB824;&#xC6B0;&#xBA74; &#xC544;&#xB798; &#xB2E8;&#xACC4;&#xB97C; &#xCC28;&#xB840;&#xB85C; &#xD655;&#xC778;&#xD558;&#xC138;&#xC694;.</p>
            <details><summary>1&#xB2E8;&#xACC4;</summary><p>&#xAC19;&#xC740; &#xC785;&#xB825;&#xCC3D;&#xC5D0;&#xC11C; &#xC5B4;&#xB5A4; &#xBB38;&#xC790;&#xB294; &#xC751;&#xB2F5;&#xC744; &#xBC14;&#xAFB8;&#xACE0;, &#xC5B4;&#xB5A4; &#xBB38;&#xC790;&#xB294; &#xBCC0;&#xD654;&#xAC00; &#xC5C6;&#xB294;&#xC9C0; &#xBE44;&#xAD50;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p></details>
            <details><summary>2&#xB2E8;&#xACC4;</summary><p>&#xD654;&#xBA74;&#xC5D0; &#xAC12;&#xC774; &#xC9C1;&#xC811; &#xCD9C;&#xB825;&#xB418;&#xC9C0; &#xC54A;&#xC544;&#xB3C4;, &#xCC38;&#xACFC; &#xAC70;&#xC9D3;&#xC744; &#xAD6C;&#xBD84;&#xD560; &#xC218; &#xC788;&#xC73C;&#xBA74; &#xC815;&#xBCF4;&#xB97C; &#xC870;&#xAE08;&#xC529; &#xC5BB;&#xC744; &#xC218; &#xC788;&#xC2B5;&#xB2C8;&#xB2E4;.</p></details>
            <details><summary>3&#xB2E8;&#xACC4;</summary><p>&#xAC80;&#xC0C9; &#xC870;&#xAC74;&#xC774; &#xB370;&#xC774;&#xD130; &#xC870;&#xD68C; &#xBB38;&#xB9E5;&#xC5D0; &#xC601;&#xD5A5;&#xC744; &#xC8FC;&#xB294;&#xC9C0; &#xC0DD;&#xAC01;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p></details>
          </div>''')
p.write_text(s, encoding='utf-8')

p = root / 'challenges' / 'image-vault.php'
s = p.read_text(encoding='utf-8')
s = s.replace('''<p>&#xC774;&#xBBF8;&#xC9C0; &#xBBF8;&#xB9AC;&#xBCF4;&#xAE30;&#xB294; &#xD30C;&#xC77C;&#xBA85;&#xC5D0; <code>.png</code>&#xAC00; &#xC788;&#xB294;&#xC9C0;&#xB9CC; &#xD655;&#xC778;&#xD569;&#xB2C8;&#xB2E4;. &#xBCF4;&#xAD00;&#xD568; &#xC548;&#xC758; &#xB2E4;&#xB978; &#xD30C;&#xC77C;&#xC744; &#xC77D;&#xC744; &#xBC29;&#xBC95;&#xC744; &#xCC3E;&#xC544;&#xBCF4;&#xC138;&#xC694;.</p>''', '''<p>&#xC774;&#xBBF8;&#xC9C0; &#xBBF8;&#xB9AC;&#xBCF4;&#xAE30;&#xB294; &#xC785;&#xB825;&#xB41C; &#xD30C;&#xC77C; &#xC774;&#xB984;&#xC744; &#xAE30;&#xBC18;&#xC73C;&#xB85C; &#xB3D9;&#xC791;&#xD569;&#xB2C8;&#xB2E4;. &#xC5B4;&#xB5A4; &#xC785;&#xB825;&#xC774; &#xD1B5;&#xACFC;&#xB418;&#xACE0; &#xC5B4;&#xB5A4; &#xC785;&#xB825;&#xC774; &#xAC70;&#xBD80;&#xB418;&#xB294;&#xC9C0; &#xAD00;&#xCC30;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p>''')
s = s.replace('''<div class="terminal">vault root: /assets/vault<br />default file: cover.png</div>''', '''<div class="hint-box visible staged-hints">
            <strong>&#xB2E8;&#xACC4;&#xD615; &#xD78C;&#xD2B8;</strong>
            <p>&#xB108;&#xBB34; &#xC5B4;&#xB824;&#xC6B0;&#xBA74; &#xC544;&#xB798; &#xB2E8;&#xACC4;&#xB97C; &#xCC28;&#xB840;&#xB85C; &#xD655;&#xC778;&#xD558;&#xC138;&#xC694;.</p>
            <details><summary>1&#xB2E8;&#xACC4;</summary><p>&#xD30C;&#xC77C;&#xBA85;&#xC758; &#xD615;&#xC2DD;&#xC744; &#xC870;&#xAE08;&#xC529; &#xBC14;&#xAFB8;&#xBA70; &#xD544;&#xD130;&#xAC00; &#xC5B4;&#xB5A4; &#xBD80;&#xBD84;&#xC744; &#xBCF4;&#xB294;&#xC9C0; &#xD655;&#xC778;&#xD558;&#xC138;&#xC694;.</p></details>
            <details><summary>2&#xB2E8;&#xACC4;</summary><p>&#xACBD;&#xB85C;&#xB294; &#xC0AC;&#xB78C;&#xC774; &#xBCF4;&#xB294; &#xBAA8;&#xC591;&#xACFC; &#xC11C;&#xBC84;&#xAC00; &#xD574;&#xC11D;&#xD55C; &#xACB0;&#xACFC;&#xAC00; &#xB2E4;&#xB97C; &#xC218; &#xC788;&#xC2B5;&#xB2C8;&#xB2E4;.</p></details>
            <details><summary>3&#xB2E8;&#xACC4;</summary><p>&#xD5C8;&#xC6A9;&#xB41C; &#xBB38;&#xC790;&#xC5F4;&#xC744; &#xC720;&#xC9C0;&#xD558;&#xBA74;&#xC11C; &#xCD5C;&#xC885; &#xD574;&#xC11D; &#xACBD;&#xB85C;&#xB97C; &#xB2EC;&#xB9AC; &#xB9CC;&#xB4E4; &#xC218; &#xC788;&#xB294;&#xC9C0; &#xBCF4;&#xC138;&#xC694;.</p></details>
          </div>''')
p.write_text(s, encoding='utf-8')

p = root / 'challenges' / 'admin-memo-chain.php'
s = p.read_text(encoding='utf-8')
s = s.replace('''<p>&#xBA54;&#xBAA8;&#xB294; &#xD544;&#xD130;&#xB9C1; &#xC5C6;&#xC774; &#xC800;&#xC7A5;&#xB418;&#xACE0;, &#xAD00;&#xB9AC;&#xC790; &#xAC80;&#xD1A0; &#xC2DC; &#xAD00;&#xB9AC;&#xC790; &#xCEE8;&#xD14D;&#xC2A4;&#xD2B8;&#xC5D0;&#xC11C; &#xC77D;&#xD78C; &#xAC83;&#xC73C;&#xB85C; &#xCC98;&#xB9AC;&#xB429;&#xB2C8;&#xB2E4;.</p>''', '''<p>&#xC800;&#xC7A5;&#xB41C; &#xBA54;&#xBAA8;&#xB294; &#xAC80;&#xD1A0; &#xD750;&#xB984;&#xC744; &#xAC70;&#xCE58;&#xBA70; &#xB2E4;&#xB978; &#xC2DC;&#xC120;&#xC5D0;&#xC11C; &#xB2E4;&#xC2DC; &#xC77D;&#xD799;&#xB2C8;&#xB2E4;. &#xB0B4;&#xAC00; &#xB0A8;&#xAE34; &#xB0B4;&#xC6A9;&#xC774; &#xC5B4;&#xB5A4; &#xD658;&#xACBD;&#xC5D0;&#xC11C; &#xD574;&#xC11D;&#xB420;&#xC9C0; &#xAD00;&#xCC30;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p>''')
s = s.replace('''<textarea id="memo" name="body" rows="6" placeholder="&lt;script&gt;...&lt;/script&gt;"></textarea>''', '''<textarea id="memo" name="body" rows="6" placeholder="&#xAC80;&#xD1A0;&#xB420; &#xBA54;&#xBAA8;&#xB97C; &#xC785;&#xB825;&#xD558;&#xC138;&#xC694;."></textarea>''')
s = s.replace('''<form method="post" class="inline-form">''', '''<div class="hint-box visible staged-hints">
            <strong>&#xB2E8;&#xACC4;&#xD615; &#xD78C;&#xD2B8;</strong>
            <p>&#xB108;&#xBB34; &#xC5B4;&#xB824;&#xC6B0;&#xBA74; &#xC544;&#xB798; &#xB2E8;&#xACC4;&#xB97C; &#xCC28;&#xB840;&#xB85C; &#xD655;&#xC778;&#xD558;&#xC138;&#xC694;.</p>
            <details><summary>1&#xB2E8;&#xACC4;</summary><p>&#xC785;&#xB825;&#xD55C; &#xB0B4;&#xC6A9;&#xC774; &#xC800;&#xC7A5; &#xD6C4; &#xD398;&#xC774;&#xC9C0;&#xC5D0; &#xC5B4;&#xB5A4; &#xBAA8;&#xC591;&#xC73C;&#xB85C; &#xB3CC;&#xC544;&#xC624;&#xB294;&#xC9C0; &#xBCF4;&#xC138;&#xC694;.</p></details>
            <details><summary>2&#xB2E8;&#xACC4;</summary><p>&#xAE00;&#xC790;&#xC640; &#xD0DC;&#xADF8;&#xAC00; &#xAC19;&#xC740; &#xBC29;&#xC2DD;&#xC73C;&#xB85C; &#xB2E4;&#xB8E8;&#xC5B4;&#xC9C0;&#xB294;&#xC9C0; &#xBE44;&#xAD50;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p></details>
            <details><summary>3&#xB2E8;&#xACC4;</summary><p>&#xAC80;&#xD1A0;&#xB77C;&#xB294; &#xD589;&#xB3D9;&#xC774; &#xB0B4; &#xBE0C;&#xB77C;&#xC6B0;&#xC800;&#xC640; &#xB2E4;&#xB978; &#xC870;&#xAC74;&#xC744; &#xAC00;&#xC9C8; &#xC218; &#xC788;&#xB294;&#xC9C0; &#xC0DD;&#xAC01;&#xD574;&#xBCF4;&#xC138;&#xC694;.</p></details>
          </div>
          <form method="post" class="inline-form">''')
p.write_text(s, encoding='utf-8')