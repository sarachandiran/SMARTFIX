CREATE DATABASE IF NOT EXISTS smartfix_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartfix_db;

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  total_xp INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE scenarios (
  scenario_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  issue_type VARCHAR(50) NOT NULL,
  difficulty TINYINT NOT NULL DEFAULT 1,
  correct_fix VARCHAR(50) NOT NULL,
  fix_message TEXT NOT NULL,
  fail_message TEXT NOT NULL,
  xp_reward INT NOT NULL DEFAULT 100
) ENGINE=InnoDB;

CREATE TABLE diagnostics (
  diagnostic_id INT AUTO_INCREMENT PRIMARY KEY,
  scenario_id INT NOT NULL,
  diagnostic_type ENUM('beep','thermal','network','boot') NOT NULL,
  diagnostic_result VARCHAR(255) NOT NULL,
  UNIQUE KEY uq_scenario_diag (scenario_id, diagnostic_type),
  CONSTRAINT fk_diag_scenario FOREIGN KEY (scenario_id) REFERENCES scenarios(scenario_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE achievements (
  achievement_id INT AUTO_INCREMENT PRIMARY KEY,
  scenario_id INT NOT NULL UNIQUE,
  achievement_name VARCHAR(100) NOT NULL,
  description VARCHAR(255) NOT NULL,
  CONSTRAINT fk_achievement_scenario FOREIGN KEY (scenario_id) REFERENCES scenarios(scenario_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attempts (
  attempt_id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  scenario_id INT NOT NULL,
  selected_fix VARCHAR(50) NOT NULL,
  is_correct TINYINT(1) NOT NULL DEFAULT 0,
  time_taken_seconds INT NOT NULL DEFAULT 0,
  attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_attempt_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_attempt_scenario FOREIGN KEY (scenario_id) REFERENCES scenarios(scenario_id) ON DELETE CASCADE,
  INDEX idx_attempt_user (user_id),
  INDEX idx_attempt_scenario (scenario_id)
) ENGINE=InnoDB;

CREATE TABLE user_scenario_progress (
  progress_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  scenario_id INT NOT NULL,
  best_time_seconds INT NULL,
  completed_at TIMESTAMP NULL,
  UNIQUE KEY uq_user_scenario (user_id, scenario_id),
  CONSTRAINT fk_progress_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_progress_scenario FOREIGN KEY (scenario_id) REFERENCES scenarios(scenario_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_achievements (
  user_achievement_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  achievement_id INT NOT NULL,
  unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_achievement (user_id, achievement_id),
  CONSTRAINT fk_ua_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_ua_achievement FOREIGN KEY (achievement_id) REFERENCES achievements(achievement_id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO scenarios (scenario_id,title,description,issue_type,difficulty,correct_fix,fix_message,fail_message,xp_reward) VALUES
(1,'Scenario 1: Blank Screen & Continuous Beeping','User reports pressing power button turns on fans, but display remains black accompanied by 1 continuous long beep.','HARDWARE',3,'ram','SUCCESS: RAM re-seated into DIMM Slot A2. POST passed with 1 short beep. System boots to Windows normally.','FAILED: Fix applied did not resolve the memory POST error. System still fails to output display.',100),
(2,'Scenario 2: System Thermal Shutdown During Use','User states the PC boots up fine, but turns off abruptly after 5-10 minutes when opening multiple applications.','HARDWARE',4,'paste','SUCCESS: Thermal paste cleaned and re-applied. Heatsink remounted. Idle CPU temp dropped to 38°C.','FAILED: Overheating issue remains unresolved. CPU thermal limits are still being exceeded.',100),
(3,'Scenario 3: Unidentified Network / No Internet Access','PC connects to LAN via RJ45 cable, but browser shows No Internet Access and IP shows self-assigned 169.254.x.x.','NETWORK',2,'ip','SUCCESS: IP renewed via DHCP server. New IP assigned: 192.168.1.102. Internet connectivity restored.','FAILED: Invalid IP address persists. Machine cannot route packets to local gateway.',100),
(4,'Scenario 4: No Bootable Device Found','PC turns on normally, passes initial POST screen, but gets stuck displaying An operating system was not found.','BOOT',3,'bios','SUCCESS: Boot priority updated to set NVMe OS drive as primary boot target. Windows loaded successfully.','FAILED: System still attempts to boot from non-bootable target drive.',100);

INSERT INTO diagnostics (scenario_id,diagnostic_type,diagnostic_result) VALUES
(1,'beep','1 Continuous Long Beep (Memory/RAM Fault)'),(1,'thermal','CPU: 32°C (Normal)'),(1,'network','Adapter Link: Off'),(1,'boot','Primary Drive: NVMe SSD 500GB'),
(2,'beep','1 Short Beep (Normal POST)'),(2,'thermal','CPU: 98°C (CRITICAL OVERHEAT)'),(2,'network','IP: 192.168.1.45 (Valid)'),(2,'boot','Primary Drive: NVMe SSD 500GB'),
(3,'beep','1 Short Beep (Normal POST)'),(3,'thermal','CPU: 41°C (Normal)'),(3,'network','IP: 169.254.120.14 (APIPA / No DHCP)'),(3,'boot','Primary Drive: SATA SSD 256GB'),
(4,'beep','1 Short Beep (Normal POST)'),(4,'thermal','CPU: 35°C (Normal)'),(4,'network','Adapter Link: Active'),(4,'boot','Primary Order: [1. USB Drive (Empty) / 2. Disabled]');

INSERT INTO achievements (scenario_id,achievement_name,description) VALUES
(1,'Memory Doctor','Solve Case #1'),(2,'Thermal Guardian','Solve Case #2'),(3,'Network Rescuer','Solve Case #3'),(4,'Boot Master','Solve Case #4');
