-- ============================================
-- Base de données : assala_events
-- Description : Base de données pour AL ASSALA EVENT
-- ============================================

-- Créer la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS `assala_events` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Utiliser la base de données
USE `assala_events`;

-- ============================================
-- Table : categories
-- Description : Catégories d'événements
-- ============================================

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table : reservations
-- Description : Réservations d'événements
-- ============================================

CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `number_of_guests` int(11) DEFAULT 0,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_event_type` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table : gallery
-- Description : Galerie d'images
-- ============================================

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(500) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_category_id` (`category_id`),
  CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insertion des catégories de base
-- ============================================

INSERT INTO `categories` (`name`) VALUES
  ('Mariage'),
  ('Fiançailles'),
  ('Khotoba'),
  ('Aqiqah'),
  ('Anniversaire')
ON DUPLICATE KEY UPDATE `name` = `name`;
