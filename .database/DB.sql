-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+jammy2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 12, 2024 at 06:17 PM
-- Server version: 8.0.36-0ubuntu0.22.04.1
-- PHP Version: 8.3.2-1+ubuntu22.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `final`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `answer_id` int UNSIGNED NOT NULL,
  `question_id` int UNSIGNED NOT NULL,
  `text` varchar(1024) COLLATE utf8mb4_general_ci NOT NULL,
  `count` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`answer_id`, `question_id`, `text`, `count`) VALUES
(2, 3, 'ano', 3),
(3, 3, 'nie', 1),
(4, 3, 'Primadona', 17);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int UNSIGNED NOT NULL,
  `template_question_id` int UNSIGNED NOT NULL,
  `closed` timestamp NULL DEFAULT NULL,
  `note` varchar(1024) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `template_question_id`, `closed`, `note`) VALUES
(3, 1, NULL, 'skuska');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int UNSIGNED NOT NULL,
  `subject_name` varchar(32) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`) VALUES
(2, 'VSA'),
(4, 'AS'),
(5, 'slovencina'),
(6, 'matematika');

-- --------------------------------------------------------

--
-- Table structure for table `template_questions`
--

CREATE TABLE `template_questions` (
  `template_question_id` int UNSIGNED NOT NULL,
  `subject_id` int UNSIGNED NOT NULL,
  `author_id` int UNSIGNED NOT NULL,
  `type` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `code` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `text` varchar(1024) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `template_questions`
--

INSERT INTO `template_questions` (`template_question_id`, `subject_id`, `author_id`, `type`, `active`, `created`, `code`, `text`) VALUES
(1, 4, 23, 1, 1, '2024-05-11 16:00:23', NULL, 'Je Olga Annanasiová??');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int UNSIGNED NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `auth_level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = basic, 2 = admin, -1 = blokovany',
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `surname`, `email`, `password`, `auth_level`, `valid`, `last_login`) VALUES
(4, 'Jan', 'akdj', 'aaa@aaa.com', '$argon2id$v=19$m=65536,t=4,p=1$M3VRU2ZIaXQ3R2QzNzR3Lg$XBYrEZG1XROUzRX4bmM8m+C2QWRGZS1ahhapjkJ4om0', 1, 0, NULL),
(5, 'asjkd', 'asdkjh', 'basista.matej@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$OHVwQ2paZk82NGwwY3Fsdg$wbMr/LHzdZzBmwJqrCVESFT8+7bpov1cUopVKntYTJA', 1, 0, NULL),
(6, 'daskl', 'adksjh', 'dasjkg@jgd.sk', '$argon2id$v=19$m=65536,t=4,p=1$eGl1ZnVYcDcybENnblE4Tg$tZepqrViux+ns2ysnYZJaQW4AH5iKYwcKUr26LGAsc0', 1, 0, NULL),
(7, 'asdkjh', 'kjashd', 'matej@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$Zk12MWtwYjBFSFQycGlWOA$tiyg1HvttHsXFB5YnyzLhJHMDR/QH/mfaFioDBwsGgw', 1, 0, NULL),
(8, 'Samo', 'dajaky', 'samo@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$UkMvNVlORnVXMmUxd0F2VA$G0R99MzLcXqu9YuOuwF2zg16icv9O2CmN1N+GLpK6PE', 1, 0, NULL),
(9, 'Samo', 'jaky', 'samoo@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$Q3FYMVRhV3VGdnppU3AxTQ$qrm+D0ZwJCbLkd340zhUXo+ZNJbGcGE60WKYfjDydPs', 1, 0, NULL),
(10, 'asdsad', 'fasdf', 'aaaa@aaa.com', '$argon2id$v=19$m=65536,t=4,p=1$alo1ZVhuYnZHMXVITHB4SQ$f0v9cvjn7zwz0LaJ34+PgMKDfH1nKaQ9ShWR3ssKOLM', 1, 0, NULL),
(11, 'AJHKsdg', 'hajsdgk', 'aaasa@aaa.com', '$argon2id$v=19$m=65536,t=4,p=1$ZVNoVzFOUjRCVGhEVE1GYw$M58j2TLyYSlI/WKEuZuhUSmoerqChhtaC7hUg6AKrZs', 1, 0, NULL),
(12, 'Fialka', 'fialova', 'fialka@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$SXlhYlM2MFNmanVTQVB5Qw$Fs1v6BrGYp9eCfWkrxyklLgLz5arS8jK8iDEQaR1yCI', 1, 1, NULL),
(13, 'ibrahum', 'maigi', 'magi@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$VTdrWUZRemlEWk5kOUdnZw$iz2WLZRPBAgPQLQKIvQK8VjQyE6L6GQ5gTgMYuxsDEg', 1, 0, NULL),
(14, 'Jan', 'Szeliké', 'basista.matej1@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$aWlDaW54Z1ZxcW1VWW1Mag$iQreSPFxXGGHZR54ElCoUe5MQ7dFcmq9l1lvOgnpe3Y', 1, 1, NULL),
(15, 'juro', 'kokot', 'kubala@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$T0lOSHlQcG9OcmtEZXExWg$JIxPVwyP8G4e/tk/uYBKYHr0wTmqFFWnuR6yGGkZvis', 1, 0, NULL),
(16, 'Jan', 'Szeliké', 'basista.matej1+130@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$MjdncVoucm1HQmt4VndBdg$SUI48vYWtiXjogU89GPBrdI2CdV+DYNqBkahIkFmVRs', 1, 1, NULL),
(17, 'Matej', 'askjdh', 'aaaaaaa@aaa.com', '$argon2id$v=19$m=65536,t=4,p=1$VVd3SmlrZVFQRjVHNnpqbQ$v9icnfcYKQ2oGHAEeIUnQpTSOEWVaMgIZgInfQrNtNc', 1, 0, NULL),
(18, 'Patrik', 'Pitka Kester', 'okolonassss@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$eU1uNWxpL3RTSllETzQ2dQ$cQlQLcbNs11C342N+qmiUBhvTd+Rd4EVRv2OaT474qE', 1, 0, NULL),
(19, 'skuska', 'skuska', 'roemello.axle@milkgitter.com', '$argon2id$v=19$m=65536,t=4,p=1$S0lWekZ2Z1BGakpzNUxGRw$eFdW5EKXTy3QQnXeG2pdxoNTmmaZNLFxNhwc+nDG8ec', 1, 0, NULL),
(20, 'skuska', 'skuska', 'melayna.xayla@milkgitter.com', '$argon2id$v=19$m=65536,t=4,p=1$RVNrSGp3S3BIQ0pjS3FjbA$xgjRxDhv14/+QVHWMu6aiG5lfmnMmJUCAksY7pxK0Pg', 1, 0, NULL),
(21, 'Patrik', 'Pitka Kester', 'melaynaaa.xayla@milkgitter.com', '$argon2id$v=19$m=65536,t=4,p=1$eFpob2h2QWJyU0RIMDM4Ng$IkrNkaevdhqkG4B0wOSk+qIBPBYJVLvONIXaR9FUNe0', 1, 0, NULL),
(22, 'Samuel', 'Kubala', 'kubalasamuel1a@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$ckh5eHB0cGNGdngvTDU2Qw$vk/Q3oRc59Gb+pfo1+0cy1wB2C3tFqhG1nthZm9pc0I', 1, 1, NULL),
(23, 'marcel', 'brandt', 'brandysss@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$WVBzdURlSXk2c2o0VmxJcQ$nLO7LbHTVGBg4MOEoowngY1g056dOU/K3hy956ru7SY', 1, 0, NULL),
(24, 'Janko', 'korhac', 'pykej13@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$RjYySTJubU9Qb0d0QlFzRQ$DltIkl/4sdsggbTaTWnBLw0GdI+E8uMmgSkRnWm/qH4', 1, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `template_question_id` (`template_question_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `template_questions`
--
ALTER TABLE `template_questions`
  ADD PRIMARY KEY (`template_question_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `answer_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `template_questions`
--
ALTER TABLE `template_questions`
  MODIFY `template_question_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`template_question_id`) REFERENCES `template_questions` (`template_question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `template_questions`
--
ALTER TABLE `template_questions`
  ADD CONSTRAINT `template_questions_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `template_questions_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
