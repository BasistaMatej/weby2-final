-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: mysql
-- Čas generovania: Po 20.Máj 2024, 19:26
-- Verzia serveru: 8.0.32
-- Verzia PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `WEBY2_final`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `answers`
--

CREATE TABLE `answers` (
  `answer_id` int UNSIGNED NOT NULL,
  `question_id` int UNSIGNED NOT NULL,
  `answer_text` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `count` int UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `answers`
--

INSERT INTO `answers` (`answer_id`, `question_id`, `answer_text`, `count`) VALUES
(31, 24, 'java persistance unit', 3),
(32, 24, 'java persistence unit', 5),
(33, 24, 'Neviem', 2),
(35, 26, 'Áno', 30),
(36, 26, 'Nie', 9),
(37, 26, 'Neviem', 3),
(38, 26, 'Sem-tam', 15),
(39, 28, 'Áno', 20),
(40, 28, 'Nie', 17),
(41, 28, 'Neviem', 5),
(42, 28, 'Sem-tam', 10);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `questions`
--

CREATE TABLE `questions` (
  `question_id` int UNSIGNED NOT NULL,
  `template_question_id` int UNSIGNED NOT NULL,
  `closed` timestamp NULL DEFAULT NULL,
  `note` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `questions`
--

INSERT INTO `questions` (`question_id`, `template_question_id`, `closed`, `note`) VALUES
(24, 25, '2024-05-14 17:06:26', NULL),
(26, 27, '2024-05-19 17:06:43', NULL),
(28, 27, '2024-05-19 20:09:53', NULL);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int UNSIGNED NOT NULL,
  `subject_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`) VALUES
(2, 'VSA'),
(4, 'Telesna'),
(5, 'slovencina'),
(7, 'ruština a japonština'),
(8, 'ruština a nemcina'),
(9, 'ruština a a nemcina');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `template_questions`
--

CREATE TABLE `template_questions` (
  `template_question_id` int UNSIGNED NOT NULL,
  `subject_id` int UNSIGNED NOT NULL,
  `author_id` int UNSIGNED NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '0=bez answers; 1=s answers',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `template_question_text` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `template_questions`
--

INSERT INTO `template_questions` (`template_question_id`, `subject_id`, `author_id`, `type`, `active`, `created`, `code`, `template_question_text`) VALUES
(25, 2, 14, 0, 0, '2024-05-20 14:54:47', NULL, 'Čo je JPA ?'),
(27, 4, 22, 1, 0, '2024-05-20 14:57:34', NULL, 'Máš rád futbal ? ');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `users`
--

CREATE TABLE `users` (
  `user_id` int UNSIGNED NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `auth_level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = basic, 2 = admin, -1 = blokovany',
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `users`
--

INSERT INTO `users` (`user_id`, `name`, `surname`, `email`, `password`, `auth_level`, `valid`, `last_login`) VALUES
(5, 'Robert', 'Novák', 'robo.novak44@gmail.com', '$2y$10$ZMVtxR981HvI71zQ8F0R2eipdckdNZqdHnOY4wXo/u/qFEQ58BkOO', 1, 0, '2024-05-18 17:44:22'),
(7, 'František', 'Šoltes', 'soltesfranta22@gmail.com', '$2y$10$WTCYpVEj1J5pq53pQfM2R.Vy.AKw8w1aRsyRo3QJaVocBeH2ulxpm', 1, 0, '2024-05-16 17:45:51'),
(14, 'Jan', 'Szeliké', 'basista.matej1@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$aWlDaW54Z1ZxcW1VWW1Mag$iQreSPFxXGGHZR54ElCoUe5MQ7dFcmq9l1lvOgnpe3Y', 2, 1, '2024-05-20 15:42:44'),
(17, 'Matej', 'Bašista', 'bmatej12@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$VVd3SmlrZVFQRjVHNnpqbQ$v9icnfcYKQ2oGHAEeIUnQpTSOEWVaMgIZgInfQrNtNc', 1, 0, '2024-05-19 17:46:52'),
(18, 'Patrik', 'Pitka Kester', 'okolonassss@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$eU1uNWxpL3RTSllETzQ2dQ$cQlQLcbNs11C342N+qmiUBhvTd+Rd4EVRv2OaT474qE', 1, 0, '2024-05-20 17:47:17'),
(21, 'Patrik', 'Pitka Kester', 'melaynaaa.xayla@milkgitter.com', '$argon2id$v=19$m=65536,t=4,p=1$eFpob2h2QWJyU0RIMDM4Ng$IkrNkaevdhqkG4B0wOSk+qIBPBYJVLvONIXaR9FUNe0', 1, 0, '2024-05-18 17:47:28'),
(22, 'Samuel', 'Kubala', 'kubalasamuel1a@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$ckh5eHB0cGNGdngvTDU2Qw$vk/Q3oRc59Gb+pfo1+0cy1wB2C3tFqhG1nthZm9pc0I', 2, 1, '2024-05-21 17:47:33'),
(23, 'Marcel', 'Brandt', 'brandysss@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$WVBzdURlSXk2c2o0VmxJcQ$nLO7LbHTVGBg4MOEoowngY1g056dOU/K3hy956ru7SY', 1, 0, '2024-05-09 17:47:39'),
(24, 'Patrik', 'Korhac', 'pykej13@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$a1ByL2lBcFViS1RHWHMvRw$9MnPWI3rO0006hdaVh8JH8FUiT5j97/Tbk9Wov4U3sU', 2, 1, '2024-05-16 17:47:43'),
(27, 'Janko', 'korhac', 'jankokorhac@centrum.sk', '$argon2id$v=19$m=65536,t=4,p=1$RFl0VnJKVE1oRFBpbm1yQw$6bKxNjK+KD4YF2L2fWpYnAlTFeKqZnF2+SLuGXoBI6w', 1, 0, '2024-05-14 17:47:48'),
(35, 'ibi', 'maiga', 'ibi@gmail.com', '$2y$10$mxcxM.boJGd5THhzD9IO6umVkOokEo.qijaAWFe1RmJZm6vGWkEaK', 1, 0, NULL);

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexy pre tabuľku `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `template_question_id` (`template_question_id`);

--
-- Indexy pre tabuľku `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexy pre tabuľku `template_questions`
--
ALTER TABLE `template_questions`
  ADD PRIMARY KEY (`template_question_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexy pre tabuľku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `answers`
--
ALTER TABLE `answers`
  MODIFY `answer_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pre tabuľku `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pre tabuľku `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pre tabuľku `template_questions`
--
ALTER TABLE `template_questions`
  MODIFY `template_question_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pre tabuľku `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`template_question_id`) REFERENCES `template_questions` (`template_question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `template_questions`
--
ALTER TABLE `template_questions`
  ADD CONSTRAINT `template_questions_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `template_questions_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
