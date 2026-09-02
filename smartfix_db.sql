-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 02, 2026 at 08:34 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartfix_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `achievement_id` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `achievement_name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`achievement_id`, `scenario_id`, `achievement_name`, `description`) VALUES
(1, 1, 'Memory Doctor', 'Solve Case #1'),
(2, 2, 'Thermal Guardian', 'Solve Case #2'),
(3, 3, 'Network Rescuer', 'Solve Case #3'),
(4, 4, 'Boot Master', 'Solve Case #4'),
(6, 5, 'Startup Optimizer', 'Resolved the slow-computer scenario by disabling unnecessary startup applications.'),
(7, 6, 'Blue Screen Resolver', 'Resolved the blue-screen scenario by rolling back the faulty display driver.'),
(8, 7, 'USB Troubleshooter', 'Restored the undetected USB device by reinstalling its driver.'),
(9, 8, 'Audio Restorer', 'Restored sound by selecting the speakers as the default output device.');

-- --------------------------------------------------------

--
-- Table structure for table `attempts`
--

CREATE TABLE `attempts` (
  `attempt_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `selected_fix` varchar(50) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `time_taken_seconds` int(11) NOT NULL DEFAULT 0,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attempts`
--

INSERT INTO `attempts` (`attempt_id`, `user_id`, `scenario_id`, `selected_fix`, `is_correct`, `time_taken_seconds`, `attempted_at`) VALUES
(1, 1, 1, 'ram', 1, 96, '2026-08-28 04:03:16'),
(2, 1, 1, 'paste', 0, 6, '2026-08-28 04:03:48'),
(3, 1, 1, 'bios', 0, 26, '2026-08-28 04:04:21'),
(4, 1, 1, 'ram', 1, 38, '2026-08-28 04:04:33'),
(5, 1, 1, 'ram', 1, 38, '2026-08-30 13:40:09'),
(6, 1, 4, 'bios', 1, 13, '2026-08-30 13:41:55'),
(7, 1, 1, 'ram', 1, 18, '2026-08-30 13:43:04'),
(8, 1, 2, 'paste', 1, 41, '2026-08-30 14:16:06'),
(9, 1, 3, 'ip', 1, 10, '2026-08-30 14:16:20'),
(10, 2, 1, 'ram', 1, 69, '2026-08-30 16:52:55'),
(11, 2, 2, 'paste', 1, 7, '2026-08-30 16:54:17'),
(12, 2, 3, 'paste', 0, 17, '2026-08-30 16:54:37'),
(13, 2, 3, 'ip', 1, 21, '2026-08-30 16:54:42'),
(14, 2, 4, 'bios', 1, 10, '2026-08-30 16:54:56'),
(15, 2, 1, 'ram', 1, 29, '2026-09-01 05:27:14'),
(16, 2, 1, 'ram', 1, 28, '2026-09-02 02:39:12'),
(18, 2, 5, 'ram', 0, 60, '2026-09-02 03:28:17'),
(19, 2, 5, 'ram', 0, 71, '2026-09-02 03:28:28'),
(20, 2, 5, 'startup', 1, 18, '2026-09-02 03:30:39'),
(21, 2, 6, 'driver', 1, 82, '2026-09-02 03:52:31'),
(22, 2, 1, 'ram', 1, 5, '2026-09-02 03:54:13'),
(23, 2, 6, 'driver', 1, 9, '2026-09-02 04:04:25'),
(24, 2, 5, 'startup', 1, 17, '2026-09-02 04:16:50'),
(25, 2, 7, 'usb', 1, 10, '2026-09-02 04:17:11'),
(26, 2, 8, 'audio', 1, 16, '2026-09-02 04:17:39'),
(27, 3, 1, 'ram', 1, 24, '2026-09-02 04:25:35'),
(28, 3, 6, 'startup', 0, 5, '2026-09-02 04:43:36'),
(29, 3, 6, 'driver', 1, 12, '2026-09-02 04:43:42'),
(30, 3, 1, 'ram', 1, 57, '2026-09-02 04:53:31');

-- --------------------------------------------------------

--
-- Table structure for table `diagnostics`
--

CREATE TABLE `diagnostics` (
  `diagnostic_id` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `diagnostic_type` enum('beep','thermal','network','boot') NOT NULL,
  `diagnostic_result` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diagnostics`
--

INSERT INTO `diagnostics` (`diagnostic_id`, `scenario_id`, `diagnostic_type`, `diagnostic_result`) VALUES
(1, 1, 'beep', '1 Continuous Long Beep (Memory/RAM Fault)'),
(2, 1, 'thermal', 'CPU: 32°C (Normal)'),
(3, 1, 'network', 'Adapter Link: Off'),
(4, 1, 'boot', 'Primary Drive: NVMe SSD 500GB'),
(5, 2, 'beep', '1 Short Beep (Normal POST)'),
(6, 2, 'thermal', 'CPU: 98°C (CRITICAL OVERHEAT)'),
(7, 2, 'network', 'IP: 192.168.1.45 (Valid)'),
(8, 2, 'boot', 'Primary Drive: NVMe SSD 500GB'),
(9, 3, 'beep', '1 Short Beep (Normal POST)'),
(10, 3, 'thermal', 'CPU: 41°C (Normal)'),
(11, 3, 'network', 'IP: 169.254.120.14 (APIPA / No DHCP)'),
(12, 3, 'boot', 'Primary Drive: SATA SSD 256GB'),
(13, 4, 'beep', '1 Short Beep (Normal POST)'),
(14, 4, 'thermal', 'CPU: 35°C (Normal)'),
(15, 4, 'network', 'Adapter Link: Active'),
(16, 4, 'boot', 'Primary Order: [1. USB Drive (Empty) / 2. Disabled]'),
(20, 5, '', 'Startup impact: HIGH — 12 unnecessary applications launch automatically when Windows starts.'),
(21, 6, '', 'System log: display driver crash detected after a recent driver update.'),
(22, 7, '', 'Device Manager: USB device has a driver error (Code 28).'),
(23, 8, '', 'Audio test: speakers are connected, but the wrong output device is selected.');

-- --------------------------------------------------------

--
-- Table structure for table `scenarios`
--

CREATE TABLE `scenarios` (
  `scenario_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `issue_type` varchar(50) NOT NULL,
  `difficulty` tinyint(4) NOT NULL DEFAULT 1,
  `correct_fix` varchar(50) NOT NULL,
  `fix_message` text NOT NULL,
  `fail_message` text NOT NULL,
  `xp_reward` int(11) NOT NULL DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scenarios`
--

INSERT INTO `scenarios` (`scenario_id`, `title`, `description`, `issue_type`, `difficulty`, `correct_fix`, `fix_message`, `fail_message`, `xp_reward`) VALUES
(1, 'Scenario 1: Blank Screen & Continuous Beeping', 'User reports pressing power button turns on fans, but display remains black accompanied by 1 continuous long beep.', 'HARDWARE', 3, 'ram', 'SUCCESS: RAM re-seated into DIMM Slot A2. POST passed with 1 short beep. System boots to Windows normally.', 'FAILED: Fix applied did not resolve the memory POST error. System still fails to output display.', 100),
(2, 'Scenario 2: System Thermal Shutdown During Use', 'User states the PC boots up fine, but turns off abruptly after 5-10 minutes when opening multiple applications.', 'HARDWARE', 4, 'paste', 'SUCCESS: Thermal paste cleaned and re-applied. Heatsink remounted. Idle CPU temp dropped to 38°C.', 'FAILED: Overheating issue remains unresolved. CPU thermal limits are still being exceeded.', 100),
(3, 'Scenario 3: Unidentified Network / No Internet Access', 'PC connects to LAN via RJ45 cable, but browser shows No Internet Access and IP shows self-assigned 169.254.x.x.', 'NETWORK', 2, 'ip', 'SUCCESS: IP renewed via DHCP server. New IP assigned: 192.168.1.102. Internet connectivity restored.', 'FAILED: Invalid IP address persists. Machine cannot route packets to local gateway.', 100),
(4, 'Scenario 4: No Bootable Device Found', 'PC turns on normally, passes initial POST screen, but gets stuck displaying An operating system was not found.', 'BOOT', 3, 'bios', 'SUCCESS: Boot priority updated to set NVMe OS drive as primary boot target. Windows loaded successfully.', 'FAILED: System still attempts to boot from non-bootable target drive.', 100),
(5, 'Computer Running Extremely Slow', 'Applications take a long time to open and the computer frequently freezes.', 'SOFTWARE', 2, 'startup', '', '', 100),
(6, 'Blue Screen and Unexpected Restart', 'A blue error screen appears and the computer restarts unexpectedly.', 'SOFTWARE', 3, 'driver', '', '', 100),
(7, 'USB Device Not Detected', 'A USB flash drive is connected, but it does not appear in File Explorer.', 'PERIPHERAL', 2, 'usb', '', '', 100),
(8, 'No Sound from Speakers', 'Audio is playing, but no sound can be heard from the connected speakers.', 'PERIPHERAL', 2, 'audio', '', '', 100);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `total_xp` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `username`, `password_hash`, `total_xp`, `created_at`) VALUES
(1, 'MARIANA OTHMAN', 'guru1', '$2y$10$4uiPdee9n0tdzCcesuvey.0B7L0ZVoeiPImN5jc/M8fY1VmbaQr5e', 400, '2026-08-28 04:01:20'),
(2, 'ADAM ISKANDAR BIN ABDULLAH', 'pelajar1', '$2y$10$OlQVoG53yiibafayByMWv.rl0VO1.yrDoH/ZeaQv4NmOGgcfxjAVW', 900, '2026-08-30 16:48:28'),
(3, 'AFIF ZUHAIRI', 'P02', '$2y$10$Q1dwKk72j4dPPc6PGpAIP.m2Z9yo9SPvtY7EvpT.PqiP.BSdgA9nu', 200, '2026-09-02 04:20:12');

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

CREATE TABLE `user_achievements` (
  `user_achievement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `achievement_id` int(11) NOT NULL,
  `unlocked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_achievements`
--

INSERT INTO `user_achievements` (`user_achievement_id`, `user_id`, `achievement_id`, `unlocked_at`) VALUES
(1, 1, 1, '2026-08-28 04:03:16'),
(4, 1, 4, '2026-08-30 13:41:55'),
(6, 1, 2, '2026-08-30 14:16:07'),
(7, 1, 3, '2026-08-30 14:16:20'),
(8, 2, 1, '2026-08-30 16:52:55'),
(9, 2, 2, '2026-08-30 16:54:17'),
(10, 2, 3, '2026-08-30 16:54:42'),
(11, 2, 4, '2026-08-30 16:54:56'),
(15, 2, 6, '2026-09-02 03:30:39'),
(16, 2, 7, '2026-09-02 03:52:31'),
(20, 2, 8, '2026-09-02 04:17:11'),
(21, 2, 9, '2026-09-02 04:17:39'),
(22, 3, 1, '2026-09-02 04:25:35'),
(23, 3, 7, '2026-09-02 04:43:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_scenario_progress`
--

CREATE TABLE `user_scenario_progress` (
  `progress_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `best_time_seconds` int(11) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_scenario_progress`
--

INSERT INTO `user_scenario_progress` (`progress_id`, `user_id`, `scenario_id`, `best_time_seconds`, `completed_at`) VALUES
(1, 1, 1, 18, '2026-08-28 04:03:16'),
(2, 1, 4, 13, '2026-08-30 13:41:55'),
(3, 1, 2, 41, '2026-08-30 14:16:06'),
(4, 1, 3, 10, '2026-08-30 14:16:20'),
(5, 2, 1, 5, '2026-08-30 16:52:55'),
(6, 2, 2, 7, '2026-08-30 16:54:17'),
(7, 2, 3, 21, '2026-08-30 16:54:42'),
(8, 2, 4, 10, '2026-08-30 16:54:56'),
(10, 2, 5, 17, '2026-09-02 03:30:39'),
(11, 2, 6, 9, '2026-09-02 03:52:31'),
(12, 2, 7, 10, '2026-09-02 04:17:11'),
(13, 2, 8, 16, '2026-09-02 04:17:39'),
(14, 3, 1, 24, '2026-09-02 04:25:35'),
(15, 3, 6, 12, '2026-09-02 04:43:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`achievement_id`),
  ADD UNIQUE KEY `scenario_id` (`scenario_id`);

--
-- Indexes for table `attempts`
--
ALTER TABLE `attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `idx_attempt_user` (`user_id`),
  ADD KEY `idx_attempt_scenario` (`scenario_id`);

--
-- Indexes for table `diagnostics`
--
ALTER TABLE `diagnostics`
  ADD PRIMARY KEY (`diagnostic_id`),
  ADD UNIQUE KEY `uq_scenario_diag` (`scenario_id`,`diagnostic_type`);

--
-- Indexes for table `scenarios`
--
ALTER TABLE `scenarios`
  ADD PRIMARY KEY (`scenario_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD PRIMARY KEY (`user_achievement_id`),
  ADD UNIQUE KEY `uq_user_achievement` (`user_id`,`achievement_id`),
  ADD KEY `fk_ua_achievement` (`achievement_id`);

--
-- Indexes for table `user_scenario_progress`
--
ALTER TABLE `user_scenario_progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD UNIQUE KEY `uq_user_scenario` (`user_id`,`scenario_id`),
  ADD KEY `fk_progress_scenario` (`scenario_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `achievement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attempts`
--
ALTER TABLE `attempts`
  MODIFY `attempt_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `diagnostics`
--
ALTER TABLE `diagnostics`
  MODIFY `diagnostic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `scenarios`
--
ALTER TABLE `scenarios`
  MODIFY `scenario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_achievements`
--
ALTER TABLE `user_achievements`
  MODIFY `user_achievement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_scenario_progress`
--
ALTER TABLE `user_scenario_progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `achievements`
--
ALTER TABLE `achievements`
  ADD CONSTRAINT `fk_achievement_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`scenario_id`) ON DELETE CASCADE;

--
-- Constraints for table `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `fk_attempt_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`scenario_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attempt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `diagnostics`
--
ALTER TABLE `diagnostics`
  ADD CONSTRAINT `fk_diag_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`scenario_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD CONSTRAINT `fk_ua_achievement` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`achievement_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ua_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_scenario_progress`
--
ALTER TABLE `user_scenario_progress`
  ADD CONSTRAINT `fk_progress_scenario` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`scenario_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
