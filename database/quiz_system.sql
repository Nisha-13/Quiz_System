-- =====================================================================
--  QuizSphere — Quiz System Database
--  phpMyAdmin / MySQL / MariaDB installation script
--
--  HOW TO RUN:
--    1. Open phpMyAdmin (http://localhost/phpmyadmin)
--    2. Click "Import" in the top menu
--    3. Choose this file (quiz_system.sql) and press "Go"
--    4. Done! The database 'quiz_system' is created with demo data.
--    5. Run "php artisan migrate" once so Laravel records the schema
--       baseline (the migrations are idempotent, so nothing is overwritten).
--
--  PREFER ARTISAN? Skip this file entirely and run:
--    php artisan migrate --seed
--
--  ADMIN LOGIN:  admin  /  admin123
--  DEMO USER  :  demoUser@example.com  /  password123
--
--  NOTE: This project uses file-based sessions and no queue/cache
--        database tables, so only the app tables below are required.
-- =====================================================================

-- ---------------------------------------------------------------
-- Create the database
-- ---------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `quiz_system`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `quiz_system`;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------
-- Table: admin
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Table: users
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `active` TINYINT NOT NULL DEFAULT 1 COMMENT '1 = not verified, 2 = verified',
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Table: categories
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `creator` VARCHAR(100) NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Table: quizzes
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `quizzes`;
CREATE TABLE `quizzes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quizzes_category_id_foreign` (`category_id`),
  CONSTRAINT `quizzes_category_id_foreign`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Table: mcqs
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `mcqs`;
CREATE TABLE `mcqs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `question` TEXT NOT NULL,
  `option_a` VARCHAR(255) NOT NULL,
  `option_b` VARCHAR(255) NOT NULL,
  `option_c` VARCHAR(255) NOT NULL,
  `option_d` VARCHAR(255) NOT NULL,
  `correct_ans` ENUM('a','b','c','d') NOT NULL,
  `admin_id` BIGINT UNSIGNED NOT NULL DEFAULT 1,
  `quiz_id` BIGINT UNSIGNED NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mcqs_quiz_id_foreign` (`quiz_id`),
  KEY `mcqs_category_id_foreign` (`category_id`),
  KEY `mcqs_admin_id_foreign` (`admin_id`),
  CONSTRAINT `mcqs_quiz_id_foreign`
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mcqs_category_id_foreign`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Table: records (one row per quiz attempt)
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `records`;
CREATE TABLE `records` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '1 = in progress, 2 = completed',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `records_quiz_id_foreign` (`quiz_id`),
  KEY `records_user_id_foreign` (`user_id`),
  CONSTRAINT `records_quiz_id_foreign`
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `records_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Table: mcq_records (per-question answers of an attempt)
-- ---------------------------------------------------------------
DROP TABLE IF EXISTS `mcq_records`;
CREATE TABLE `mcq_records` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `record_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `mcq_id` BIGINT UNSIGNED NOT NULL,
  `select_answer` ENUM('a','b','c','d') DEFAULT NULL,
  `is_correct` TINYINT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mcq_records_record_id_foreign` (`record_id`),
  KEY `mcq_records_user_id_foreign` (`user_id`),
  KEY `mcq_records_mcq_id_foreign` (`mcq_id`),
  CONSTRAINT `mcq_records_record_id_foreign`
    FOREIGN KEY (`record_id`) REFERENCES `records` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mcq_records_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mcq_records_mcq_id_foreign`
    FOREIGN KEY (`mcq_id`) REFERENCES `mcqs` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- SEED DATA
-- =====================================================================

-- Default admin — username: admin  |  password: admin123 (bcrypt hashed)
INSERT INTO `admin` (`name`, `password`, `created_at`, `updated_at`) VALUES
('admin', '$2y$12$QVJWtbGJVlrv.8WwGpXU9O46stPBjFPrD6gNa2kcvZqd3TZCsmca2', NOW(), NOW());

-- Demo user — email: demoUser@example.com  |  password: password123 (bcrypt hashed)
INSERT INTO `users` (`name`, `email`, `password`, `active`, `created_at`, `updated_at`) VALUES
('Demo User', 'demoUser@example.com', '$2y$12$tiP7tiaVylNguHRXXSsqIOXN7k/CIOzfb59giKULCvRpsVNbzb.B6', 2, NOW(), NOW());

-- Categories
INSERT INTO `categories` (`name`, `creator`, `created_at`, `updated_at`) VALUES
('Web Development', 'admin', NOW(), NOW()),
('Programming',     'admin', NOW(), NOW()),
('General Knowledge', 'admin', NOW(), NOW()),
('Science',         'admin', NOW(), NOW());

-- Quizzes
INSERT INTO `quizzes` (`name`, `category_id`, `created_at`, `updated_at`) VALUES
('HTML-Basics',            1, NOW(), NOW()),
('CSS-Fundamentals',       1, NOW(), NOW()),
('PHP-Basics',             2, NOW(), NOW()),
('World-Capitals',         3, NOW(), NOW()),
('Human-Body',             4, NOW(), NOW());

-- MCQs — HTML Basics (Quiz 1)
INSERT INTO `mcqs` (`question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_ans`, `admin_id`, `quiz_id`, `category_id`, `created_at`, `updated_at`) VALUES
('What does HTML stand for?', 'Hyper Text Markup Language', 'Home Tool Markup Language', 'Hyperlinks and Text Markup Language', 'High Text Machine Language', 'a', 1, 1, 1, NOW(), NOW()),
('Which tag is used for the largest heading?', '<h6>', '<head>', '<h1>', '<heading>', 'c', 1, 1, 1, NOW(), NOW()),
('Which tag creates a hyperlink?', '<link>', '<a>', '<href>', '<url>', 'b', 1, 1, 1, NOW(), NOW()),
('Who maintains the HTML specification?', 'WHATWG / W3C', 'Microsoft', 'Google', 'IEEE', 'a', 1, 1, 1, NOW(), NOW()),
('Which attribute specifies images for screen readers?', 'title', 'alt', 'aria-img', 'desc', 'b', 1, 1, 1, NOW(), NOW());

-- MCQs — CSS Fundamentals (Quiz 2)
INSERT INTO `mcqs` (`question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_ans`, `admin_id`, `quiz_id`, `category_id`, `created_at`, `updated_at`) VALUES
('What does CSS stand for?', 'Creative Style Sheets', 'Cascading Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets', 'b', 1, 2, 1, NOW(), NOW()),
('Which property changes text color?', 'font-color', 'text-color', 'color', 'font-style', 'c', 1, 2, 1, NOW(), NOW()),
('How do you include an external stylesheet?', '<style src="...">', '<script src="...">', '<stylesheet>...</stylesheet>', '<link rel="stylesheet" href="...">', 'd', 1, 2, 1, NOW(), NOW()),
('Which symbol starts an ID selector?', 'A dot ( . )', 'A hash ( # )', 'An ampersand ( & )', 'An asterisk ( * )', 'b', 1, 2, 1, NOW(), NOW());

-- MCQs — PHP Basics (Quiz 3)
INSERT INTO `mcqs` (`question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_ans`, `admin_id`, `quiz_id`, `category_id`, `created_at`, `updated_at`) VALUES
('What does PHP stand for today?', 'Personal Home Page', 'PHP: Hypertext Preprocessor', 'Preprocessed Home Pages', 'Private Hosting Protocol', 'b', 1, 3, 2, NOW(), NOW()),
('Which symbol starts all PHP variables?', '$', '@', '#', '&', 'a', 1, 3, 2, NOW(), NOW()),
('Which function prints text in PHP?', 'write()', 'say()', 'echo', 'display()', 'c', 1, 3, 2, NOW(), NOW()),
('How does a PHP file end?', '.ph', '.php', '.html', '.php3', 'b', 1, 3, 2, NOW(), NOW());

-- MCQs — World Capitals (Quiz 4)
INSERT INTO `mcqs` (`question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_ans`, `admin_id`, `quiz_id`, `category_id`, `created_at`, `updated_at`) VALUES
('What is the capital of France?', 'Berlin', 'Madrid', 'Paris', 'Rome', 'c', 1, 4, 3, NOW(), NOW()),
('What is the capital of Japan?', 'Kyoto', 'Tokyo', 'Osaka', 'Nagoya', 'b', 1, 4, 3, NOW(), NOW()),
('What is the capital of Australia?', 'Sydney', 'Melbourne', 'Canberra', 'Perth', 'c', 1, 4, 3, NOW(), NOW()),
('What is the capital of Canada?', 'Toronto', 'Ottawa', 'Vancouver', 'Montreal', 'b', 1, 4, 3, NOW(), NOW());

-- MCQs — Human Body (Quiz 5)
INSERT INTO `mcqs` (`question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_ans`, `admin_id`, `quiz_id`, `category_id`, `created_at`, `updated_at`) VALUES
('Which organ pumps blood through the body?', 'Lungs', 'Heart', 'Liver', 'Kidneys', 'b', 1, 5, 4, NOW(), NOW()),
('How many bones does an adult human have?', '156', '206', '256', '306', 'b', 1, 5, 4, NOW(), NOW()),
('Which organ controls the nervous system?', 'Brain', 'Spine', 'Stomach', 'Eyes', 'a', 1, 5, 4, NOW(), NOW());

SELECT '✅ Database quiz_system created successfully!' AS result;
