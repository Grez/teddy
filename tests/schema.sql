-- Adminer 4.2.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `admin_permission`;
CREATE TABLE `admin_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `presenter` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2877342FA76ED395` (`user_id`),
  CONSTRAINT `FK_2877342FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `ban`;
CREATE TABLE `ban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `range_start` int(10) unsigned NOT NULL,
  `range_end` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `reason` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_62FED0E5C06EBDF6` (`range_start`),
  KEY `IDX_62FED0E5D7645AE8` (`range_end`),
  KEY `IDX_62FED0E52F4A8AA7` (`ends_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `chat_message`;
CREATE TABLE `chat_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `posted_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FAB3FC16A76ED395` (`user_id`),
  CONSTRAINT `FK_FAB3FC16A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `coin_sack`;
CREATE TABLE `coin_sack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `coins` int(11) NOT NULL,
  `remaining` int(11) NOT NULL,
  `added_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B8900A0FA76ED395` (`user_id`),
  CONSTRAINT `FK_B8900A0FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `forum`;
CREATE TABLE `forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `forum` (`id`) VALUES
  (1),
  (2),
  (3),
  (4),
  (5),
  (6),
  (7),
  (8);

DROP TABLE IF EXISTS `forum_last_visit`;
CREATE TABLE `forum_last_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `forum_id` int(11) DEFAULT NULL,
  `last_visit_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A3036267A76ED395` (`user_id`),
  KEY `IDX_A303626729CCBAD0` (`forum_id`),
  CONSTRAINT `FK_A303626729CCBAD0` FOREIGN KEY (`forum_id`) REFERENCES `forum` (`id`),
  CONSTRAINT `FK_A3036267A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `forum_post`;
CREATE TABLE `forum_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` longtext COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `type` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_996BCC5A29CCBAD0` (`forum_id`),
  KEY `IDX_996BCC5AA76ED395` (`user_id`),
  KEY `IDX_996BCC5A9AC0396` (`conversation_id`),
  KEY `IDX_996BCC5A1F6FA0AF` (`deleted_by`),
  KEY `IDX_996BCC5A4AF38FD1` (`deleted_at`),
  CONSTRAINT `FK_996BCC5A1F6FA0AF` FOREIGN KEY (`deleted_by`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_996BCC5A29CCBAD0` FOREIGN KEY (`forum_id`) REFERENCES `forum` (`id`),
  CONSTRAINT `FK_996BCC5A9AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `forum_post` (`id`),
  CONSTRAINT `FK_996BCC5AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_agent_id` int(11) NOT NULL,
  `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cookie` int(11) DEFAULT NULL,
  `fingerprint` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  `error` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AA08CB10A76ED395` (`user_id`),
  KEY `IDX_AA08CB10D499950B` (`user_agent_id`),
  KEY `IDX_AA08CB10A5E3B32D` (`ip`),
  KEY `IDX_AA08CB108AE0BA66` (`cookie`),
  KEY `IDX_AA08CB10FC0B754A` (`fingerprint`),
  CONSTRAINT `FK_AA08CB10A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_AA08CB10D499950B` FOREIGN KEY (`user_agent_id`) REFERENCES `user_agent` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `map`;
CREATE TABLE `map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `radius` int(11) NOT NULL,
  `seed` int(11) NOT NULL,
  `octaves` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `positions_last_modified_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_user_id` int(11) DEFAULT NULL,
  `from_user_id` int(11) DEFAULT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` longtext COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `unread` tinyint(1) NOT NULL,
  `deleted_by_sender` tinyint(1) NOT NULL,
  `deleted_by_recipient` tinyint(1) NOT NULL,
  `sent_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B6BD307F29F6EE60` (`to_user_id`),
  KEY `IDX_B6BD307F2130303A` (`from_user_id`),
  KEY `IDX_B6BD307F9AC0396` (`conversation_id`),
  KEY `IDX_B6BD307F1DD4B870` (`deleted_by_sender`),
  KEY `IDX_B6BD307F132D7D4` (`deleted_by_recipient`),
  CONSTRAINT `FK_B6BD307F2130303A` FOREIGN KEY (`from_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_B6BD307F29F6EE60` FOREIGN KEY (`to_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_B6BD307F9AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `message` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `position`;
CREATE TABLE `position` (
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `height` double NOT NULL,
  `map_id` int(11) DEFAULT NULL,
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_462CE4F553C55F64` (`map_id`),
  CONSTRAINT `FK_462CE4F553C55F64` FOREIGN KEY (`map_id`) REFERENCES `map` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `stat_daily`;
CREATE TABLE `stat_daily` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `players_total` int(11) NOT NULL,
  `players_active` int(11) NOT NULL,
  `players_online` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E2BA767CAA9E377A` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `stat_detailed`;
CREATE TABLE `stat_detailed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `players_total` int(11) NOT NULL,
  `players_active` int(11) NOT NULL,
  `players_online` int(11) NOT NULL,
  `load1` double DEFAULT NULL,
  `load5` double DEFAULT NULL,
  `load15` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6D62B9AA9E377A6F949845` (`date`,`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `system_log`;
CREATE TABLE `system_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `script` int(11) NOT NULL,
  `action` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_verified` tinyint(1) NOT NULL,
  `verification_code` int(11) NOT NULL,
  `age` smallint(6) NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gender` smallint(6) NOT NULL,
  `last_login_at` datetime NOT NULL,
  `last_activity_at` datetime NOT NULL,
  `registered_at` datetime NOT NULL,
  `fb_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL,
  `affiliate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL,
  `admin` tinyint(1) NOT NULL,
  `admin_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `api_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649290B2F37` (`nick`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `user_agent`;
CREATE TABLE `user_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C44967C5C44967C5` (`user_agent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `user_log`;
CREATE TABLE `user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6429094EA76ED395` (`user_id`),
  KEY `IDX_6429094E47CC8C92` (`action`),
  KEY `IDX_6429094E8CDE5729` (`type`),
  KEY `IDX_6429094EAA9E377A` (`date`),
  CONSTRAINT `FK_6429094EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- 2016-05-05 12:23:27
