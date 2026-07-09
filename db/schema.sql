CREATE DATABASE IF NOT EXISTS wargame_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'wargame_app'@'localhost' IDENTIFIED BY 'CHANGE_ME_DB_PASSWORD';
GRANT SELECT, INSERT, UPDATE, DELETE ON wargame_lab.* TO 'wargame_app'@'localhost';
FLUSH PRIVILEGES;

USE wargame_lab;

CREATE TABLE IF NOT EXISTS challenges (
  challenge_id VARCHAR(64) PRIMARY KEY,
  title VARCHAR(120) NOT NULL,
  category VARCHAR(64) NOT NULL,
  difficulty VARCHAR(32) NOT NULL,
  points INT NOT NULL,
  path VARCHAR(255) NOT NULL,
  summary VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS challenge_flags (
  challenge_id VARCHAR(64) PRIMARY KEY,
  flag VARCHAR(255) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL,
  password VARCHAR(255) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS teams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(64) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  team_id INT NOT NULL,
  challenge_id VARCHAR(64) NOT NULL,
  submitted_flag VARCHAR(255) NOT NULL,
  is_correct TINYINT(1) NOT NULL,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_submissions_team (team_id),
  INDEX idx_submissions_challenge (challenge_id),
  CONSTRAINT fk_submissions_team FOREIGN KEY (team_id) REFERENCES teams(id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS solves (
  team_id INT NOT NULL,
  challenge_id VARCHAR(64) NOT NULL,
  solved_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (team_id, challenge_id),
  CONSTRAINT fk_solves_team FOREIGN KEY (team_id) REFERENCES teams(id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

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
DELETE FROM users WHERE username IN ('admin', 'guest');
INSERT INTO users (username, password) VALUES
  ('admin', 'not_the_real_password'),
  ('guest', 'guest');
