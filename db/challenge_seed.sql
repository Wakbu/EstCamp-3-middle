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
  ('session-shadow', 'Session Shadow', '異쒖엯利?, '珥덇툒', 150, '/challenges/session-shadow.php', '援먯쑁留?異쒖엯利앹뿉 湲곕줉??蹂댁쭅 媛믪쓣 ?뺤씤?섍퀬 ?곸쐞 沅뚰븳?쇰줈 ?듭젣?뚮? ?듦낵?섏떗?쒖삤.', 1, 10),
  ('blind-notice', 'Blind Notice', '怨듭? 寃??, '以묎툒', 250, '/challenges/blind-notice.php', '怨듦컻 寃뚯떆?먯쓽 議댁옱 ?щ? ?좏샇留뚯쑝濡?鍮꾧났媛??묒쟾 硫붾え???몄쬆 ?쒖떇??異붿쟻?섏떗?쒖삤.', 1, 20),
  ('image-vault', 'Image Vault', '蹂닿툒李?, '?곴툒 ?꾩큹', 300, '/challenges/image-vault.php', '?곸긽 蹂닿툒李쎌쓽 ?뚯씪紐?寃臾?洹쒖튃???고쉶???대? 蹂닿? ?먮즺瑜??대엺?섏떗?쒖삤.', 1, 30),
  ('admin-memo-chain', 'Admin Memo Chain', '寃?닿?', '怨좉툒', 400, '/challenges/admin-memo-chain.php', '寃?닿? ?⑤쭚?먯꽌 ?ㅼ떆 ?대━???쇱쟾 硫붾え瑜??댁슜???대? ?꾩슜 ?몄텧源뚯? ?곌껐?섏떗?쒖삤.', 1, 40)
ON DUPLICATE KEY UPDATE
  title = VALUES(title),
  category = VALUES(category),
  difficulty = VALUES(difficulty),
  points = VALUES(points),
  path = VALUES(path),
  summary = VALUES(summary),
  is_active = VALUES(is_active),
  sort_order = VALUES(sort_order);

INSERT INTO challenge_flags (challenge_id, flag) VALUES
  ('session-shadow', 'EST{cookie_role_admin_shadow}'),
  ('blind-notice', 'EST{boolean_blind_notice_42c7}'),
  ('image-vault', 'EST{image_vault_path_filter_bypass}'),
  ('admin-memo-chain', 'EST{stored_xss_admin_memo_chain}')
ON DUPLICATE KEY UPDATE flag = VALUES(flag);

DELETE FROM blind_notices;
INSERT INTO blind_notices (title, body, is_public) VALUES
  ('?꾩엯 ?덈궡', '援먯쑁留?怨듦컻 寃뚯떆?먯쓣 媛쒕갑?⑸땲?? 紐⑤뱺 遺꾨????뺤같 洹쒖젙??以?섑븯??떆??', 1),
  ('?곹솴???먭?', '?덈젴 以??꾧낵 ?꾪솴 媛깆떊???좎떆 吏?곕맆 ???덉뒿?덈떎.', 1),
  ('鍮꾨Ц ?몄쬆 硫붾え', '???됱? 怨듦컻 寃???붾㈃???몄텧?섏? 留?寃?', 0);
INSERT INTO challenges (challenge_id, title, category, difficulty, points, path, summary, is_active, sort_order) VALUES
  ('idle-empire', 'Idle Empire', '蹂닿툒 ?먯궛', '以묎툒', 300, '/challenges/idle-empire.php', '釉뚮씪?곗? ??μ냼? 理쒖쥌 蹂닿퀬 ?붿껌??遺꾩꽍???대씪?댁뼵???좊ː 痍⑥빟?먯쓣 ?뺤씤?섏떗?쒖삤.', 1, 35)
ON DUPLICATE KEY UPDATE
  title = VALUES(title),
  category = VALUES(category),
  difficulty = VALUES(difficulty),
  points = VALUES(points),
  path = VALUES(path),
  summary = VALUES(summary),
  is_active = VALUES(is_active),
  sort_order = VALUES(sort_order);

INSERT INTO challenge_flags (challenge_id, flag) VALUES
  ('idle-empire', 'EST{idle_empire_client_trust_bypass}')
ON DUPLICATE KEY UPDATE flag = VALUES(flag);
