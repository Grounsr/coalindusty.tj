-- =====================================================================
-- Coal Industry Forum Tajikistan — Database Schema
-- Trilingual CMS: English (default) / Russian / Tajik
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Administrators
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(60) NOT NULL UNIQUE,
  `email` VARCHAR(180) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(180) NOT NULL,
  `role` ENUM('superadmin','editor') NOT NULL DEFAULT 'editor',
  `last_login_at` DATETIME NULL,
  `last_login_ip` VARCHAR(45) NULL,
  `failed_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Site settings (general — non-translatable)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `key_name` VARCHAR(80) NOT NULL,
  `value` TEXT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Translatable settings (per language)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings_i18n` (
  `key_name` VARCHAR(80) NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`key_name`, `lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Pages — slugs map to public URLs; content is per-language
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(80) NOT NULL UNIQUE,
  `system` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `page_translations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_id` INT UNSIGNED NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` VARCHAR(500) NULL,
  `content` LONGTEXT NULL,
  `meta_title` VARCHAR(255) NULL,
  `meta_description` VARCHAR(500) NULL,
  `meta_keywords` VARCHAR(500) NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_page_lang` (`page_id`,`lang`),
  CONSTRAINT `fk_pt_page` FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Free-form content blocks (homepage, about, investors, etc.)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `content_blocks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `block_key` VARCHAR(120) NOT NULL UNIQUE,
  `block_type` ENUM('text','html','image','number','url') NOT NULL DEFAULT 'text',
  `description` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `content_block_values` (
  `block_id` INT UNSIGNED NOT NULL,
  `lang` ENUM('en','ru','tj','_') NOT NULL DEFAULT '_',
  `value` LONGTEXT NULL,
  PRIMARY KEY (`block_id`,`lang`),
  CONSTRAINT `fk_cbv_block` FOREIGN KEY (`block_id`) REFERENCES `content_blocks`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Forum editions (year-by-year structure)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `forum_years` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `year` SMALLINT UNSIGNED NOT NULL UNIQUE,
  `event_date` DATE NULL,
  `is_current` TINYINT(1) NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `cover_image` VARCHAR(255) NULL,
  `participants_count` INT UNSIGNED NULL,
  `countries_count` INT UNSIGNED NULL,
  `speakers_count` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_year_translations` (
  `forum_year_id` INT UNSIGNED NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `title` VARCHAR(255) NULL,
  `tagline` VARCHAR(500) NULL,
  `description` LONGTEXT NULL,
  `venue` VARCHAR(255) NULL,
  PRIMARY KEY (`forum_year_id`,`lang`),
  CONSTRAINT `fk_fyt_year` FOREIGN KEY (`forum_year_id`) REFERENCES `forum_years`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Year gallery (photos & videos per forum year)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `year_media` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `forum_year_id` INT UNSIGNED NOT NULL,
  `media_type` ENUM('photo','video') NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `thumbnail` VARCHAR(500) NULL,
  `caption_en` VARCHAR(255) NULL,
  `caption_ru` VARCHAR(255) NULL,
  `caption_tj` VARCHAR(255) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_year` (`forum_year_id`),
  CONSTRAINT `fk_ym_year` FOREIGN KEY (`forum_year_id`) REFERENCES `forum_years`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- News
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `news` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `cover_image` VARCHAR(500) NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  `views` INT UNSIGNED NOT NULL DEFAULT 0,
  `forum_year_id` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_published` (`is_published`,`published_at`),
  CONSTRAINT `fk_news_year` FOREIGN KEY (`forum_year_id`) REFERENCES `forum_years`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `news_translations` (
  `news_id` INT UNSIGNED NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `excerpt` VARCHAR(500) NULL,
  `body` LONGTEXT NULL,
  `meta_title` VARCHAR(255) NULL,
  `meta_description` VARCHAR(500) NULL,
  PRIMARY KEY (`news_id`,`lang`),
  CONSTRAINT `fk_nt_news` FOREIGN KEY (`news_id`) REFERENCES `news`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Leadership cards (PM, Minister, etc.) shown on About page
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `leadership` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `photo` VARCHAR(500) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `leadership_translations` (
  `leadership_id` INT UNSIGNED NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `position` VARCHAR(500) NOT NULL,
  `quote` TEXT NULL,
  PRIMARY KEY (`leadership_id`,`lang`),
  CONSTRAINT `fk_lt_l` FOREIGN KEY (`leadership_id`) REFERENCES `leadership`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Speakers
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `speakers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `forum_year_id` INT UNSIGNED NOT NULL,
  `photo` VARCHAR(500) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_sp_year` FOREIGN KEY (`forum_year_id`) REFERENCES `forum_years`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `speaker_translations` (
  `speaker_id` INT UNSIGNED NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `position` VARCHAR(500) NULL,
  `organization` VARCHAR(255) NULL,
  `country` VARCHAR(120) NULL,
  `bio` TEXT NULL,
  PRIMARY KEY (`speaker_id`,`lang`),
  CONSTRAINT `fk_st_sp` FOREIGN KEY (`speaker_id`) REFERENCES `speakers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Program items per forum year
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `program_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `forum_year_id` INT UNSIGNED NOT NULL,
  `day_number` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `time_start` VARCHAR(20) NULL,
  `time_end` VARCHAR(20) NULL,
  `block_label` VARCHAR(20) NULL,
  `hall` VARCHAR(120) NULL,
  `tag` VARCHAR(80) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pi_year` FOREIGN KEY (`forum_year_id`) REFERENCES `forum_years`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `program_item_translations` (
  `program_item_id` INT UNSIGNED NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `title` VARCHAR(500) NOT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`program_item_id`,`lang`),
  CONSTRAINT `fk_pit_pi` FOREIGN KEY (`program_item_id`) REFERENCES `program_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Topics (key forum themes)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `topics` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `forum_year_id` INT UNSIGNED NOT NULL,
  `icon` VARCHAR(80) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_topic_year` FOREIGN KEY (`forum_year_id`) REFERENCES `forum_years`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `topic_translations` (
  `topic_id` INT UNSIGNED NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `title` VARCHAR(500) NOT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`topic_id`,`lang`),
  CONSTRAINT `fk_tt_t` FOREIGN KEY (`topic_id`) REFERENCES `topics`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Partners & Sponsors
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `partners` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `forum_year_id` INT UNSIGNED NOT NULL,
  `logo` VARCHAR(500) NULL,
  `url` VARCHAR(500) NULL,
  `tier` ENUM('strategic','general','media','partner') NOT NULL DEFAULT 'partner',
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_p_year` FOREIGN KEY (`forum_year_id`) REFERENCES `forum_years`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `partner_translations` (
  `partner_id` INT UNSIGNED NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` VARCHAR(500) NULL,
  PRIMARY KEY (`partner_id`,`lang`),
  CONSTRAINT `fk_pt_p` FOREIGN KEY (`partner_id`) REFERENCES `partners`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Investor packages (without sums shown publicly)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `investor_packages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `icon` VARCHAR(80) NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `investor_package_translations` (
  `package_id` INT UNSIGNED NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `name` VARCHAR(180) NOT NULL,
  `tagline` VARCHAR(500) NULL,
  `benefits` TEXT NULL,
  PRIMARY KEY (`package_id`,`lang`),
  CONSTRAINT `fk_ipt_ip` FOREIGN KEY (`package_id`) REFERENCES `investor_packages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Downloads (program / concept note PDFs per language per year)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `downloads` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `forum_year_id` INT UNSIGNED NULL,
  `doc_type` ENUM('program','concept','other') NOT NULL,
  `lang` ENUM('en','ru','tj') NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `original_name` VARCHAR(255) NULL,
  `file_size` INT UNSIGNED NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dl` (`forum_year_id`,`doc_type`,`lang`),
  CONSTRAINT `fk_dl_year` FOREIGN KEY (`forum_year_id`) REFERENCES `forum_years`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Participant registrations (forum delegates)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `forum_year_id` INT UNSIGNED NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `phone` VARCHAR(60) NULL,
  `country` VARCHAR(120) NULL,
  `city` VARCHAR(120) NULL,
  `organization` VARCHAR(255) NULL,
  `position` VARCHAR(255) NULL,
  `participation_type` ENUM('delegate','speaker','press','investor','sponsor','observer') NOT NULL DEFAULT 'delegate',
  `interests` VARCHAR(500) NULL,
  `dietary` VARCHAR(255) NULL,
  `comments` TEXT NULL,
  `photo_path` VARCHAR(500) NULL,
  `passport_path` VARCHAR(500) NULL,
  `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `verification_code` VARCHAR(10) NULL,
  `verification_expires` DATETIME NULL,
  `status` ENUM('pending','verified','confirmed','rejected','attended') NOT NULL DEFAULT 'pending',
  `lang` ENUM('en','ru','tj') NOT NULL DEFAULT 'ru',
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `verified_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_reg_year` FOREIGN KEY (`forum_year_id`) REFERENCES `forum_years`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Investor inquiries
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `investor_inquiries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `phone` VARCHAR(60) NULL,
  `company` VARCHAR(255) NULL,
  `position` VARCHAR(255) NULL,
  `country` VARCHAR(120) NULL,
  `interest_level` VARCHAR(120) NULL,
  `message` TEXT NULL,
  `status` ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Contact form messages
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `phone` VARCHAR(60) NULL,
  `subject` VARCHAR(255) NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('new','read','replied') NOT NULL DEFAULT 'new',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Media library (reusable assets)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `media_library` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_path` VARCHAR(500) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(120) NULL,
  `file_size` INT UNSIGNED NULL,
  `width` INT NULL,
  `height` INT NULL,
  `alt_text` VARCHAR(255) NULL,
  `category` VARCHAR(80) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Activity log (admin actions audit trail)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT UNSIGNED NULL,
  `action` VARCHAR(120) NOT NULL,
  `entity_type` VARCHAR(80) NULL,
  `entity_id` INT UNSIGNED NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin` (`admin_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Page views (basic analytics)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `page_views` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` VARCHAR(500) NOT NULL,
  `referrer` VARCHAR(500) NULL,
  `lang` VARCHAR(8) NULL,
  `ip_hash` VARCHAR(64) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
