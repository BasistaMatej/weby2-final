-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+jammy2
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: localhost:3306
-- Čas generovania: St 10.Apr 2024, 17:29
-- Verzia serveru: 8.0.36-0ubuntu0.22.04.1
-- Verzia PHP: 8.3.2-1+ubuntu22.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `final`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `answers`
--

CREATE TABLE `answers` (
  `answer_id` int UNSIGNED NOT NULL,
  `question_id` int UNSIGNED NOT NULL,
  `text` varchar(1024) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `questions`
--

CREATE TABLE `questions` (
  `question_id` int UNSIGNED NOT NULL,
  `template_question_id` int UNSIGNED NOT NULL,
  `closed` timestamp NULL DEFAULT NULL,
  `note` varchar(1024) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int UNSIGNED NOT NULL,
  `subject_name` varchar(32) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `template_questions`
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

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `users`
--

CREATE TABLE `users` (
  `user_id` int UNSIGNED NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `auth_level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = basic, 2 = admin, -1 = blokovany',
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `answer_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `template_questions`
--
ALTER TABLE `template_questions`
  MODIFY `template_question_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

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
