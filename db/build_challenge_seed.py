from pathlib import Path


def hx(s):
    return "CONVERT(0x" + s.encode("utf-8").hex().upper() + " USING utf8mb4)"


challenges = [
    ('session-shadow', 'Session Shadow', '출입증', '초급', 150, '/challenges/session-shadow.php', '교육망 출입증에 기록된 보직 값을 확인하고 상위 권한으로 통제소를 통과하십시오.', 1, 10),
    ('blind-notice', 'Blind Notice', '공지 검색', '중급', 250, '/challenges/blind-notice.php', '공개 게시판의 존재 여부 신호만으로 비공개 작전 메모의 인증 표식을 추적하십시오.', 1, 20),
    ('image-vault', 'Image Vault', '보급창', '상급 전초', 300, '/challenges/image-vault.php', '영상 보급창의 파일명 검문 규칙을 우회해 내부 보관 자료를 열람하십시오.', 1, 30),
    ('admin-memo-chain', 'Admin Memo Chain', '검열관', '고급', 400, '/challenges/admin-memo-chain.php', '검열관 단말에서 다시 열리는 야전 메모를 이용해 내부 전용 호출까지 연결하십시오.', 1, 40),
]
flags = [
    ('session-shadow', 'FLAG{cookie_role_admin_shadow}'),
    ('blind-notice', 'FLAG{boolean_blind_notice_42c7}'),
    ('image-vault', 'FLAG{image_vault_path_filter_bypass}'),
    ('admin-memo-chain', 'FLAG{stored_xss_admin_memo_chain}'),
]
lines = []
lines.append('USE wargame_lab;')
lines.append('')
lines.append('CREATE TABLE IF NOT EXISTS blind_notices (')
lines.append('  id INT AUTO_INCREMENT PRIMARY KEY,')
lines.append('  title VARCHAR(160) NOT NULL,')
lines.append('  body TEXT NOT NULL,')
lines.append('  is_public TINYINT(1) NOT NULL DEFAULT 1')
lines.append(') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;')
lines.append('')
lines.append('CREATE TABLE IF NOT EXISTS admin_memos (')
lines.append('  id INT AUTO_INCREMENT PRIMARY KEY,')
lines.append('  body TEXT NOT NULL,')
lines.append('  review_result TEXT NULL,')
lines.append('  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP')
lines.append(') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;')
lines.append('')
lines.append('INSERT INTO challenges (challenge_id, title, category, difficulty, points, path, summary, is_active, sort_order) VALUES')
rows = []
for c in challenges:
    cid, title, cat, diff, pts, path, summary, active, order = c
    rows.append(f"  ('{cid}', '{title}', '{cat}', {hx(diff)}, {pts}, '{path}', {hx(summary)}, {active}, {order})")
lines.append(',\n'.join(rows) + '\nON DUPLICATE KEY UPDATE')
for field in ['title','category','difficulty','points','path','summary','is_active','sort_order']:
    suffix = ',' if field != 'sort_order' else ';'
    lines.append(f'  {field} = VALUES({field}){suffix}')
lines.append('')
lines.append('INSERT INTO challenge_flags (challenge_id, flag) VALUES')
lines.append(',\n'.join([f"  ('{cid}', '{flag}')" for cid, flag in flags]) + '\nON DUPLICATE KEY UPDATE flag = VALUES(flag);')
lines.append('')
lines.append('DELETE FROM blind_notices;')
lines.append('INSERT INTO blind_notices (title, body, is_public) VALUES')
lines.append("  ('전입 안내', '교육망 공개 게시판을 개방합니다. 모든 분대는 정찰 규정을 준수하십시오.', 1),")
lines.append("  ('상황판 점검', '훈련 중 전과 현황 갱신이 잠시 지연될 수 있습니다.', 1),")
lines.append("  ('비문 인증 메모', '이 행은 공개 검색 화면에 노출하지 말 것.', 0);")
Path(__file__).with_name('challenge_seed.sql').write_text('\n'.join(lines) + '\n', encoding='utf-8', newline='\n')