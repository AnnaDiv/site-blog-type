-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Εξυπηρετητής: 127.0.0.1
-- Χρόνος δημιουργίας: 28 Αυγ 2025 στις 03:47:33
-- Έκδοση διακομιστή: 10.4.32-MariaDB
-- Έκδοση PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `loveitorthrowit`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `account_validation`
--

CREATE TABLE `account_validation` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `blocks`
--

CREATE TABLE `blocks` (
  `id` int(100) NOT NULL,
  `user_nickname` varchar(100) NOT NULL,
  `blockingUser` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `categories`
--

CREATE TABLE `categories` (
  `category_id` int(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `comments`
--

CREATE TABLE `comments` (
  `comments_id` int(100) NOT NULL,
  `posts_id` int(100) NOT NULL,
  `users_nickname` varchar(100) NOT NULL,
  `contentComment` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `deletedposts`
--

CREATE TABLE `deletedposts` (
  `post_id` int(100) NOT NULL,
  `user_nickname` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` varchar(255) NOT NULL,
  `image_folder` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `follows`
--

CREATE TABLE `follows` (
  `follow_id` int(100) NOT NULL,
  `user_nickname` varchar(100) NOT NULL,
  `follower` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `likes`
--

CREATE TABLE `likes` (
  `likes_id` int(11) NOT NULL,
  `like` tinyint(1) NOT NULL DEFAULT 0,
  `posts_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `lovedcategories`
--

CREATE TABLE `lovedcategories` (
  `user_id` int(100) NOT NULL,
  `category_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `lovedposts`
--

CREATE TABLE `lovedposts` (
  `post_id` int(100) NOT NULL,
  `users_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(100) NOT NULL,
  `users_nickname` varchar(100) NOT NULL,
  `senders_nickname` varchar(100) NOT NULL,
  `actions_id` int(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `used` tinyint(1) DEFAULT 0,
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `notification_actions`
--

CREATE TABLE `notification_actions` (
  `actions_id` int(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `content` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `percategory`
--

CREATE TABLE `percategory` (
  `category_title` varchar(100) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `posts`
--

CREATE TABLE `posts` (
  `posts_id` int(100) NOT NULL,
  `user_nickname` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` varchar(255) NOT NULL,
  `image_folder` varchar(255) NOT NULL,
  `likes` int(100) DEFAULT NULL,
  `comments` int(100) NOT NULL DEFAULT 0,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(10) NOT NULL DEFAULT 'public',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(10) NOT NULL DEFAULT 'post'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `throwcategories`
--

CREATE TABLE `throwcategories` (
  `user_id` int(100) NOT NULL,
  `category_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `throwposts`
--

CREATE TABLE `throwposts` (
  `post_id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `users`
--

CREATE TABLE `users` (
  `users_id` int(100) NOT NULL,
  `nickname` varchar(100) NOT NULL,
  `motto` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image_folder` varchar(255) DEFAULT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `account_validation`
--
ALTER TABLE `account_validation`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `blocks`
--
ALTER TABLE `blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_nickname` (`user_nickname`),
  ADD KEY `blockingUser` (`blockingUser`);

--
-- Ευρετήρια για πίνακα `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `title` (`title`);

--
-- Ευρετήρια για πίνακα `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comments_id`),
  ADD KEY `posts_id` (`posts_id`),
  ADD KEY `users_nickname` (`users_nickname`);

--
-- Ευρετήρια για πίνακα `deletedposts`
--
ALTER TABLE `deletedposts`
  ADD PRIMARY KEY (`post_id`);

--
-- Ευρετήρια για πίνακα `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follow_id`),
  ADD KEY `user_nickname` (`user_nickname`),
  ADD KEY `follower` (`follower`);

--
-- Ευρετήρια για πίνακα `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`likes_id`),
  ADD UNIQUE KEY `posts_id` (`posts_id`,`users_id`);

--
-- Ευρετήρια για πίνακα `lovedcategories`
--
ALTER TABLE `lovedcategories`
  ADD PRIMARY KEY (`user_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Ευρετήρια για πίνακα `lovedposts`
--
ALTER TABLE `lovedposts`
  ADD PRIMARY KEY (`post_id`,`users_id`),
  ADD KEY `users_id` (`users_id`);

--
-- Ευρετήρια για πίνακα `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `users_nickname` (`users_nickname`),
  ADD KEY `senders_nickname` (`senders_nickname`),
  ADD KEY `notifications_ibfk_3` (`actions_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Ευρετήρια για πίνακα `notification_actions`
--
ALTER TABLE `notification_actions`
  ADD PRIMARY KEY (`actions_id`);

--
-- Ευρετήρια για πίνακα `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Ευρετήρια για πίνακα `percategory`
--
ALTER TABLE `percategory`
  ADD PRIMARY KEY (`category_title`,`post_id`),
  ADD KEY `percategory_ibfk_2` (`post_id`);

--
-- Ευρετήρια για πίνακα `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`posts_id`),
  ADD UNIQUE KEY `posts_id` (`posts_id`),
  ADD KEY `user_nickname` (`user_nickname`);

--
-- Ευρετήρια για πίνακα `throwcategories`
--
ALTER TABLE `throwcategories`
  ADD PRIMARY KEY (`user_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Ευρετήρια για πίνακα `throwposts`
--
ALTER TABLE `throwposts`
  ADD PRIMARY KEY (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Ευρετήρια για πίνακα `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`),
  ADD UNIQUE KEY `nickname` (`nickname`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `account_validation`
--
ALTER TABLE `account_validation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `blocks`
--
ALTER TABLE `blocks`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `comments`
--
ALTER TABLE `comments`
  MODIFY `comments_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `follows`
--
ALTER TABLE `follows`
  MODIFY `follow_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `likes`
--
ALTER TABLE `likes`
  MODIFY `likes_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `notification_actions`
--
ALTER TABLE `notification_actions`
  MODIFY `actions_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `posts`
--
ALTER TABLE `posts`
  MODIFY `posts_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `blocks`
--
ALTER TABLE `blocks`
  ADD CONSTRAINT `blocks_ibfk_1` FOREIGN KEY (`user_nickname`) REFERENCES `users` (`nickname`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blocks_ibfk_2` FOREIGN KEY (`blockingUser`) REFERENCES `users` (`nickname`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`posts_id`) REFERENCES `posts` (`posts_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`users_nickname`) REFERENCES `users` (`nickname`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`user_nickname`) REFERENCES `users` (`nickname`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`follower`) REFERENCES `users` (`nickname`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `lovedcategories`
--
ALTER TABLE `lovedcategories`
  ADD CONSTRAINT `lovedcategories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lovedcategories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `lovedposts`
--
ALTER TABLE `lovedposts`
  ADD CONSTRAINT `lovedposts_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`posts_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lovedposts_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`users_nickname`) REFERENCES `users` (`nickname`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`senders_nickname`) REFERENCES `users` (`nickname`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`actions_id`) REFERENCES `notification_actions` (`actions_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `percategory`
--
ALTER TABLE `percategory`
  ADD CONSTRAINT `percategory_ibfk_1` FOREIGN KEY (`category_title`) REFERENCES `categories` (`title`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `percategory_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`posts_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_nickname`) REFERENCES `users` (`nickname`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `throwcategories`
--
ALTER TABLE `throwcategories`
  ADD CONSTRAINT `throwcategories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `throwcategories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `throwposts`
--
ALTER TABLE `throwposts`
  ADD CONSTRAINT `throwposts_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`posts_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `throwposts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
