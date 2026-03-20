-- Dack's DND Tools schema bootstrap
-- Target: MySQL 8+ / MariaDB with JSON and JSON aggregation support

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS `dnd_tools`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `dnd_tools`;

-- ---------------------------------------------------------------------------
-- Auth / Site tables
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `dab_roles` (
  `id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dab_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `dab_account` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `display_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `user_role` INT UNSIGNED NOT NULL DEFAULT 2,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dab_account_email` (`email`),
  KEY `idx_dab_account_user_role` (`user_role`),
  CONSTRAINT `fk_dab_account_role`
    FOREIGN KEY (`user_role`) REFERENCES `dab_roles` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `dab_news` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(120) NOT NULL,
  `body` MEDIUMTEXT NOT NULL,
  `image_url` VARCHAR(1024) NULL,
  `publish_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dab_news_publish_date` (`publish_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Compendium base tables
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `equipment_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_equipment_categories_index` (`index`),
  KEY `idx_equipment_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ability_scores` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ability_scores_index` (`index`),
  KEY `idx_ability_scores_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `alignments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_alignments_index` (`index`),
  KEY `idx_alignments_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `conditions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_conditions_index` (`index`),
  KEY `idx_conditions_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `damage_types` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_damage_types_index` (`index`),
  KEY `idx_damage_types_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `languages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(80) NULL,
  `script` VARCHAR(80) NULL,
  `typical_speakers` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_languages_index` (`index`),
  KEY `idx_languages_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `proficiencies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(80) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_proficiencies_index` (`index`),
  KEY `idx_proficiencies_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `skills` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `ability_score_index` VARCHAR(120) NULL,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_skills_index` (`index`),
  KEY `idx_skills_name` (`name`),
  KEY `idx_skills_ability_score_index` (`ability_score_index`),
  CONSTRAINT `fk_skills_ability_score`
    FOREIGN KEY (`ability_score_index`) REFERENCES `ability_scores` (`index`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `races` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `size` VARCHAR(60) NULL,
  `speed` SMALLINT UNSIGNED NULL,
  `ability_bonuses` JSON NULL,
  `alignment` TEXT NULL,
  `age` TEXT NULL,
  `size_description` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_races_index` (`index`),
  KEY `idx_races_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subraces` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `race_index` VARCHAR(120) NOT NULL,
  `description` JSON NULL,
  `ability_bonuses` JSON NULL,
  `starting_proficiencies` JSON NULL,
  `languages` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_subraces_index` (`index`),
  KEY `idx_subraces_name` (`name`),
  KEY `idx_subraces_race_index` (`race_index`),
  CONSTRAINT `fk_subraces_race`
    FOREIGN KEY (`race_index`) REFERENCES `races` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `traits` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_traits_index` (`index`),
  KEY `idx_traits_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `classes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `hit_die` TINYINT UNSIGNED NULL,
  `spellcasting` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_classes_index` (`index`),
  KEY `idx_classes_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subclasses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `class_index` VARCHAR(120) NOT NULL,
  `subclass_flavor` TEXT NULL,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_subclasses_index` (`index`),
  KEY `idx_subclasses_name` (`name`),
  KEY `idx_subclasses_class_index` (`class_index`),
  CONSTRAINT `fk_subclasses_class`
    FOREIGN KEY (`class_index`) REFERENCES `classes` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `features` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `class_index` VARCHAR(120) NULL,
  `subclass_index` VARCHAR(120) NULL,
  `level` TINYINT UNSIGNED NULL,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_features_index` (`index`),
  KEY `idx_features_name` (`name`),
  KEY `idx_features_class_index` (`class_index`),
  KEY `idx_features_subclass_index` (`subclass_index`),
  CONSTRAINT `fk_features_class`
    FOREIGN KEY (`class_index`) REFERENCES `classes` (`index`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT `fk_features_subclass`
    FOREIGN KEY (`subclass_index`) REFERENCES `subclasses` (`index`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `equipment` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `equipment_category_index` VARCHAR(120) NULL,
  `cost` JSON NULL,
  `weight` DECIMAL(8,2) NULL,
  `damage` JSON NULL,
  `armor_class` JSON NULL,
  `properties` JSON NULL,
  `str_minimum` TINYINT UNSIGNED NULL,
  `stealth_disadvantage` TINYINT(1) NOT NULL DEFAULT 0,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_equipment_index` (`index`),
  KEY `idx_equipment_name` (`name`),
  KEY `idx_equipment_equipment_category_index` (`equipment_category_index`),
  CONSTRAINT `fk_equipment_category`
    FOREIGN KEY (`equipment_category_index`) REFERENCES `equipment_categories` (`index`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `magic_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `equipment_category_index` VARCHAR(120) NULL,
  `rarity_name` VARCHAR(80) NULL,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_magic_items_index` (`index`),
  KEY `idx_magic_items_name` (`name`),
  KEY `idx_magic_items_equipment_category_index` (`equipment_category_index`),
  CONSTRAINT `fk_magic_items_category`
    FOREIGN KEY (`equipment_category_index`) REFERENCES `equipment_categories` (`index`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `spells` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `spell_level` TINYINT UNSIGNED NULL,
  `school_index` VARCHAR(120) NULL,
  `casting_time` VARCHAR(120) NULL,
  `spell_range` VARCHAR(120) NULL,
  `components` JSON NULL,
  `duration` VARCHAR(120) NULL,
  `material` TEXT NULL,
  `description` JSON NULL,
  `higher_level` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_spells_index` (`index`),
  KEY `idx_spells_name` (`name`),
  KEY `idx_spells_school_index` (`school_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `monsters` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `size` VARCHAR(60) NULL,
  `type` VARCHAR(100) NULL,
  `alignment` VARCHAR(120) NULL,
  `armor_class` JSON NULL,
  `hit_points` INT UNSIGNED NULL,
  `hit_dice` VARCHAR(40) NULL,
  `speed` JSON NULL,
  `strength` TINYINT UNSIGNED NULL,
  `dexterity` TINYINT UNSIGNED NULL,
  `constitution` TINYINT UNSIGNED NULL,
  `intelligence` TINYINT UNSIGNED NULL,
  `wisdom` TINYINT UNSIGNED NULL,
  `charisma` TINYINT UNSIGNED NULL,
  `damage_vulnerabilities` JSON NULL,
  `damage_resistances` JSON NULL,
  `damage_immunities` JSON NULL,
  `senses` JSON NULL,
  `languages` VARCHAR(255) NULL,
  `challenge_rating` VARCHAR(20) NULL,
  `xp` INT UNSIGNED NULL,
  `special_abilities` JSON NULL,
  `actions` JSON NULL,
  `legendary_actions` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_monsters_index` (`index`),
  KEY `idx_monsters_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `backgrounds` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `feature_desc` JSON NULL,
  `personality_traits` JSON NULL,
  `ideals` JSON NULL,
  `bonds` JSON NULL,
  `flaws` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_backgrounds_index` (`index`),
  KEY `idx_backgrounds_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `feats` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `index` VARCHAR(120) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `prerequisites` JSON NULL,
  `description` JSON NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_feats_index` (`index`),
  KEY `idx_feats_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Compendium relationship tables
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `race_languages` (
  `race_index` VARCHAR(120) NOT NULL,
  `language_index` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`race_index`, `language_index`),
  KEY `idx_race_languages_language` (`language_index`),
  CONSTRAINT `fk_race_languages_race`
    FOREIGN KEY (`race_index`) REFERENCES `races` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_race_languages_language`
    FOREIGN KEY (`language_index`) REFERENCES `languages` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `race_proficiencies` (
  `race_index` VARCHAR(120) NOT NULL,
  `proficiency_index` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`race_index`, `proficiency_index`),
  KEY `idx_race_proficiencies_proficiency` (`proficiency_index`),
  CONSTRAINT `fk_race_proficiencies_race`
    FOREIGN KEY (`race_index`) REFERENCES `races` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_race_proficiencies_proficiency`
    FOREIGN KEY (`proficiency_index`) REFERENCES `proficiencies` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `race_traits` (
  `race_index` VARCHAR(120) NOT NULL,
  `trait_index` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`race_index`, `trait_index`),
  KEY `idx_race_traits_trait` (`trait_index`),
  CONSTRAINT `fk_race_traits_race`
    FOREIGN KEY (`race_index`) REFERENCES `races` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_race_traits_trait`
    FOREIGN KEY (`trait_index`) REFERENCES `traits` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `class_proficiencies` (
  `class_index` VARCHAR(120) NOT NULL,
  `proficiency_index` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`class_index`, `proficiency_index`),
  KEY `idx_class_proficiencies_proficiency` (`proficiency_index`),
  CONSTRAINT `fk_class_proficiencies_class`
    FOREIGN KEY (`class_index`) REFERENCES `classes` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_class_proficiencies_proficiency`
    FOREIGN KEY (`proficiency_index`) REFERENCES `proficiencies` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `class_saving_throws` (
  `class_index` VARCHAR(120) NOT NULL,
  `ability_score_index` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`class_index`, `ability_score_index`),
  KEY `idx_class_saving_throws_ability` (`ability_score_index`),
  CONSTRAINT `fk_class_saving_throws_class`
    FOREIGN KEY (`class_index`) REFERENCES `classes` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_class_saving_throws_ability`
    FOREIGN KEY (`ability_score_index`) REFERENCES `ability_scores` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `class_proficiency_choices` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_index` VARCHAR(120) NOT NULL,
  `description` TEXT NULL,
  `choose` TINYINT UNSIGNED NULL,
  `type` VARCHAR(80) NULL,
  `options` JSON NULL,
  PRIMARY KEY (`id`),
  KEY `idx_class_proficiency_choices_class` (`class_index`),
  CONSTRAINT `fk_class_proficiency_choices_class`
    FOREIGN KEY (`class_index`) REFERENCES `classes` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `class_starting_equipment` (
  `class_index` VARCHAR(120) NOT NULL,
  `equipment_index` VARCHAR(120) NOT NULL,
  `quantity` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`class_index`, `equipment_index`),
  KEY `idx_class_starting_equipment_equipment` (`equipment_index`),
  CONSTRAINT `fk_class_starting_equipment_class`
    FOREIGN KEY (`class_index`) REFERENCES `classes` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_class_starting_equipment_equipment`
    FOREIGN KEY (`equipment_index`) REFERENCES `equipment` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `class_starting_equipment_options` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_index` VARCHAR(120) NOT NULL,
  `choose` TINYINT UNSIGNED NULL,
  `description` TEXT NULL,
  `options` JSON NULL,
  PRIMARY KEY (`id`),
  KEY `idx_class_starting_equipment_options_class` (`class_index`),
  CONSTRAINT `fk_class_starting_equipment_options_class`
    FOREIGN KEY (`class_index`) REFERENCES `classes` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `monster_proficiencies` (
  `monster_index` VARCHAR(120) NOT NULL,
  `proficiency_index` VARCHAR(120) NOT NULL,
  `value` SMALLINT UNSIGNED NULL,
  PRIMARY KEY (`monster_index`, `proficiency_index`),
  KEY `idx_monster_proficiencies_proficiency` (`proficiency_index`),
  CONSTRAINT `fk_monster_proficiencies_monster`
    FOREIGN KEY (`monster_index`) REFERENCES `monsters` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_monster_proficiencies_proficiency`
    FOREIGN KEY (`proficiency_index`) REFERENCES `proficiencies` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `monster_condition_immunities` (
  `monster_index` VARCHAR(120) NOT NULL,
  `condition_index` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`monster_index`, `condition_index`),
  KEY `idx_monster_condition_immunities_condition` (`condition_index`),
  CONSTRAINT `fk_monster_condition_immunities_monster`
    FOREIGN KEY (`monster_index`) REFERENCES `monsters` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_monster_condition_immunities_condition`
    FOREIGN KEY (`condition_index`) REFERENCES `conditions` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `spell_classes` (
  `spell_index` VARCHAR(120) NOT NULL,
  `class_index` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`spell_index`, `class_index`),
  KEY `idx_spell_classes_class` (`class_index`),
  CONSTRAINT `fk_spell_classes_spell`
    FOREIGN KEY (`spell_index`) REFERENCES `spells` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_spell_classes_class`
    FOREIGN KEY (`class_index`) REFERENCES `classes` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `spell_subclasses` (
  `spell_index` VARCHAR(120) NOT NULL,
  `subclass_index` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`spell_index`, `subclass_index`),
  KEY `idx_spell_subclasses_subclass` (`subclass_index`),
  CONSTRAINT `fk_spell_subclasses_spell`
    FOREIGN KEY (`spell_index`) REFERENCES `spells` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_spell_subclasses_subclass`
    FOREIGN KEY (`subclass_index`) REFERENCES `subclasses` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `background_proficiencies` (
  `background_index` VARCHAR(120) NOT NULL,
  `proficiency_index` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`background_index`, `proficiency_index`),
  KEY `idx_background_proficiencies_proficiency` (`proficiency_index`),
  CONSTRAINT `fk_background_proficiencies_background`
    FOREIGN KEY (`background_index`) REFERENCES `backgrounds` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_background_proficiencies_proficiency`
    FOREIGN KEY (`proficiency_index`) REFERENCES `proficiencies` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `background_starting_equipment` (
  `background_index` VARCHAR(120) NOT NULL,
  `equipment_index` VARCHAR(120) NOT NULL,
  `quantity` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`background_index`, `equipment_index`),
  KEY `idx_background_starting_equipment_equipment` (`equipment_index`),
  CONSTRAINT `fk_background_starting_equipment_background`
    FOREIGN KEY (`background_index`) REFERENCES `backgrounds` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_background_starting_equipment_equipment`
    FOREIGN KEY (`equipment_index`) REFERENCES `equipment` (`index`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Required role seeds used by application logic
-- ---------------------------------------------------------------------------

INSERT INTO `dab_roles` (`id`, `name`) VALUES
  (2, 'user'),
  (3, 'webmaster')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Optional: bootstrap an admin account once roles exist.
-- Replace the hash with a password generated by PHP password_hash().
-- INSERT INTO `dab_account` (`first_name`, `last_name`, `display_name`, `email`, `password`, `user_role`)
-- VALUES ('Admin', 'User', 'admin', 'admin@example.com', '$2y$10$replace_me_with_password_hash', 3);
