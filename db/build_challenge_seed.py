from pathlib import Path

challenges = [
    ('session-shadow', '세션 그림자', '출입증', '초급', 150, '/challenges/session-shadow.php', '교육망 출입증에 기록된 보직 값을 확인하고 상위 권한으로 통제소를 통과하십시오.', 1, 10),
    ('blind-notice', '비문 공지 검색소', '공지 검색', '중급', 250, '/challenges/blind-notice.php', '검색어가 조회문에 들어가는 방식을 관찰해 비공개 작전 메모의 인증 표식을 추적하십시오.', 1, 20),
    ('reflected-board', '반사 게시판 검색', 'DOM XSS', '중급 입문', 225, '/challenges/reflected-board.php', '검색 결과 렌더링 흐름을 악용해 브라우저 alert 호출을 발생시키십시오.', 1, 25),
    ('image-vault', '영상 보급창', '보급창', '상급 입문', 300, '/challenges/image-vault.php', '영상 보급창의 파일명 검문 규칙을 우회해 내부 보관 자료를 열람하십시오.', 1, 30),
    ('idle-empire', '코인 제국 보급 작전', '보급 자산', '중급', 300, '/challenges/idle-empire.php', '브라우저 저장소와 최종 보고 요청을 분석해 클라이언트 신뢰 취약점을 확인하십시오.', 1, 35),
    ('net-diagnostics', '네트워크 진단', '명령어 주입', '중상급', 350, '/challenges/net-diagnostics.php', 'ping 명령 뒤에 붙는 사용자 입력을 이용해 추가 명령을 실행하십시오.', 1, 37),
    ('admin-memo-chain', '관리관 메모 연쇄', '검문소', '고급', 400, '/challenges/admin-memo-chain.php', '관리관 검토 화면에 저장되는 메모를 이용해 전용 API 호출까지 연결하십시오.', 1, 40),
    ('internal-supply', '내부 보급망 점검기', '내부망 요청', '중상급', 375, '/challenges/internal-supply.php', '작전 서버의 대리 조회 기능으로 내부 전용 인증 페이지에 접근하십시오.', 1, 45),
    ('upload-dispatch', '전송 파일 업로드', '파일 업로드', '고급', 450, '/challenges/upload-dispatch.php', '허술한 이미지 파일명 검사를 우회하고 업로드한 첨부 파일을 실행하십시오.', 1, 50),
]

flags = [
    ('session-shadow', 'FLAG{cookie_role_admin_shadow}'),
    ('blind-notice', 'FLAG{boolean_blind_notice_42c7}'),
    ('reflected-board', 'FLAG{dom_xss_reflected_search_alert}'),
    ('image-vault', 'FLAG{image_vault_path_filter_bypass}'),
    ('idle-empire', 'FLAG{idle_empire_client_trust_bypass}'),
    ('net-diagnostics', 'FLAG{ops_ping_command_injection}'),
    ('admin-memo-chain', 'FLAG{stored_xss_admin_memo_chain}'),
    ('internal-supply', 'FLAG{ssrf_internal_supply_route}'),
    ('upload-dispatch', 'FLAG{dispatch_upload_php_execution}'),
]

lines = ['USE wargame_lab;', '']
lines += [
    'CREATE TABLE IF NOT EXISTS blind_notices (',
    '  id INT AUTO_INCREMENT PRIMARY KEY,',
    '  title VARCHAR(160) NOT NULL,',
    '  body TEXT NOT NULL,',
    '  is_public TINYINT(1) NOT NULL DEFAULT 1',
    ') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;',
    '',
    'CREATE TABLE IF NOT EXISTS admin_memos (',
    '  id INT AUTO_INCREMENT PRIMARY KEY,',
    '  body TEXT NOT NULL,',
    '  review_result TEXT NULL,',
    '  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
    ') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;',
    '',
    'INSERT INTO challenges (challenge_id, title, category, difficulty, points, path, summary, is_active, sort_order) VALUES',
]
rows = []
for c in challenges:
    cid, title, cat, diff, pts, path, summary, active, order = c
    rows.append(f"  ('{cid}', '{title}', '{cat}', '{diff}', {pts}, '{path}', '{summary}', {active}, {order})")
lines.append(',\n'.join(rows) + '\nON DUPLICATE KEY UPDATE')
for field in ['title', 'category', 'difficulty', 'points', 'path', 'summary', 'is_active', 'sort_order']:
    lines.append(f"  {field} = VALUES({field}){',' if field != 'sort_order' else ';'}")
lines += ['', 'INSERT INTO challenge_flags (challenge_id, flag) VALUES']
lines.append(',\n'.join([f"  ('{cid}', '{flag}')" for cid, flag in flags]) + '\nON DUPLICATE KEY UPDATE flag = VALUES(flag);')
lines += [
    '',
    'DELETE FROM blind_notices;',
    'INSERT INTO blind_notices (title, body, is_public) VALUES',
    "  ('전입 안내', '교육망 공개 게시판을 개방합니다. 모든 분대는 보안 규정을 준수하십시오.', 1),",
    "  ('상황판 점검', '훈련 중 전과 현황 갱신이 일시 지연될 수 있습니다.', 1),",
    "  ('비문 인증 메모', '이 문서는 공개 검색 화면에 노출되지 말 것', 0);",
]
Path(__file__).with_name('challenge_seed.sql').write_text('\n'.join(lines) + '\n', encoding='utf-8', newline='\n')