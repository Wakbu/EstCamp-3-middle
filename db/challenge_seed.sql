USE wargame_lab;

CREATE TABLE IF NOT EXISTS blind_notices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(160) NOT NULL,
  body TEXT NOT NULL,
  is_public TINYINT(1) NOT NULL DEFAULT 1
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_memos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  body TEXT NOT NULL,
  review_result TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO challenges (challenge_id, title, category, difficulty, points, path, summary, is_active, sort_order) VALUES
  ('session-shadow', '세션 그림자', '출입증', '초급', 150, '/challenges/session-shadow.php', '교육망 출입증에 기록된 보직 값을 확인하고 상위 권한으로 통제소를 통과하십시오.', 1, 10),
  ('blind-notice', '비문 공지 검색소', '공지 검색', '중급', 250, '/challenges/blind-notice.php', '공개 게시판의 응답 신호를 관찰해 비공개 작전 메모의 인증 표식을 추적하십시오.', 1, 20),
  ('image-vault', '영상 보급창', '보급창', '상급 입문', 300, '/challenges/image-vault.php', '영상 보급창의 파일명 검문 규칙을 우회해 내부 보관 자료를 열람하십시오.', 1, 30),
  ('idle-empire', '코인 제국 보급 작전', '보급 자산', '중급', 300, '/challenges/idle-empire.php', '브라우저 저장소와 최종 보고 요청을 분석해 클라이언트 신뢰 취약점을 확인하십시오.', 1, 35),
  ('admin-memo-chain', '관리관 메모 연쇄', '검문소', '고급', 400, '/challenges/admin-memo-chain.php', '관리관 메모 체계에서 저장된 작전 메모를 이용해 최종 인증 표식까지 연결하십시오.', 1, 40)
ON DUPLICATE KEY UPDATE
  title = VALUES(title), category = VALUES(category), difficulty = VALUES(difficulty), points = VALUES(points), path = VALUES(path), summary = VALUES(summary), is_active = VALUES(is_active), sort_order = VALUES(sort_order);

INSERT INTO challenge_flags (challenge_id, flag) VALUES
  ('session-shadow', 'EST{cookie_role_admin_shadow}'),
  ('blind-notice', 'EST{boolean_blind_notice_42c7}'),
  ('image-vault', 'EST{image_vault_path_filter_bypass}'),
  ('idle-empire', 'EST{idle_empire_client_trust_bypass}'),
  ('admin-memo-chain', 'EST{stored_xss_admin_memo_chain}')
ON DUPLICATE KEY UPDATE flag = VALUES(flag);

DELETE FROM blind_notices;
INSERT INTO blind_notices (title, body, is_public) VALUES
  ('전입 안내', '교육망 공개 게시판을 개방합니다. 모든 분대는 보안 규정을 준수하십시오.', 1),
  ('상황판 점검', '훈련 중 전과 현황 갱신이 일시 지연될 수 있습니다.', 1),
  ('비문 인증 메모', '이 문서는 공개 검색 화면에 노출되지 말 것', 0);
