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

INSERT INTO `admin_permission` (`id`, `user_id`, `presenter`) VALUES
(8,	1,	'Admin:Main'),
(9,	1,	'Admin:Users'),
(10,	1,	'Admin:Admins'),
(11,	1,	'Admin:Game'),
(12,	1,	'Admin:Antimulti');

DROP TABLE IF EXISTS `ban`;
CREATE TABLE `ban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `range_start` int(11) UNSIGNED NOT NULL,
  `range_end` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_62FED0E5C06EBDF6` (`range_start`),
  KEY `IDX_62FED0E5D7645AE8` (`range_end`),
  KEY `IDX_62FED0E52F4A8AA7` (`ends_at`)
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
  CONSTRAINT `FK_996BCC5A9AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `forum` (`id`),
  CONSTRAINT `FK_996BCC5AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cookie` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `error` int(11) NOT NULL,
  `user_agent_id` int(11) NOT NULL,
  `fingerprint` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AA08CB10A76ED395` (`user_id`),
  KEY `IDX_AA08CB10D499950B` (`user_agent_id`),
  KEY `IDX_AA08CB10A5E3B32D` (`ip`),
  KEY `IDX_AA08CB108AE0BA66` (`cookie`),
  KEY `IDX_AA08CB10FC0B754A` (`fingerprint`),
  CONSTRAINT `FK_AA08CB10A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_AA08CB10D499950B` FOREIGN KEY (`user_agent_id`) REFERENCES `user_agent` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `login` (`id`, `user_id`, `login`, `ip`, `cookie`, `date`, `error`, `user_agent_id`, `fingerprint`) VALUES
(5,	1,	'grez',	'127.0.0.1',	7701095,	'2015-11-01 21:28:55',	0,	2,	NULL),
(6,	1,	'grez',	'127.0.0.1',	7176986,	'2015-11-01 21:30:02',	0,	2,	'081cf85087f9a6575ce0516cca5fa2ea'),
(7,	1,	'grez',	'127.0.0.1',	2596238,	'2015-11-08 21:59:53',	0,	2,	'081cf85087f9a6575ce0516cca5fa2ea'),
(8,	1,	'grez',	'127.0.0.1',	2596238,	'2015-11-08 22:22:55',	0,	2,	'081cf85087f9a6575ce0516cca5fa2ea'),
(9,	1,	'grez',	'127.0.0.1',	2596238,	'2015-11-10 19:36:38',	0,	2,	'081cf85087f9a6575ce0516cca5fa2ea'),
(10,	1,	'grez',	'127.0.0.1',	2596238,	'2015-11-11 23:30:37',	0,	2,	'081cf85087f9a6575ce0516cca5fa2ea'),
(11,	1,	'grez',	'127.0.0.1',	9150066,	'2016-02-19 21:06:12',	0,	3,	'030d04f505950b3abe3b56b70fe3f3ea'),
(12,	1,	'grez.cz@gmail.com',	'127.0.0.1',	7856136,	'2016-02-21 03:27:12',	0,	3,	'030d04f505950b3abe3b56b70fe3f3ea'),
(13,	1,	'grez.cz@gmail.com',	'127.0.0.1',	4393593,	'2016-02-29 19:24:53',	0,	3,	'89d772dd27eb0b14716d9c626d551b8b'),
(14,	2,	'mario@luigi.it',	'127.0.0.1',	8320094,	'2016-02-29 20:00:50',	0,	3,	'89d772dd27eb0b14716d9c626d551b8b'),
(15,	1,	'grez.cz@gmail.com',	'127.0.0.1',	1368413,	'2016-02-29 20:01:05',	0,	3,	'89d772dd27eb0b14716d9c626d551b8b'),
(16,	NULL,	'grez',	'127.0.0.1',	7345009,	'2016-02-29 20:09:21',	0,	3,	'89d772dd27eb0b14716d9c626d551b8b'),
(17,	1,	'grez.cz@gmail.com',	'127.0.0.1',	7379283,	'2016-02-29 20:09:27',	0,	3,	'89d772dd27eb0b14716d9c626d551b8b'),
(18,	1,	'grez.cz@gmail.com',	'127.0.0.1',	2635594,	'2016-03-03 20:35:51',	0,	3,	'cf3a50a784ecf0aec7c82a78a89a5a80'),
(19,	1,	'grez.cz@gmail.com',	'127.0.0.1',	6133455,	'2016-03-09 19:36:11',	0,	3,	'a56416453707993a34de55acd8c72b20');

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_user_id` int(11) DEFAULT NULL,
  `from_user_id` int(11) DEFAULT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `sender_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` longtext COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `unread` tinyint(1) NOT NULL,
  `deleted` smallint(6) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B6BD307F29F6EE60` (`to_user_id`),
  KEY `IDX_B6BD307F2130303A` (`from_user_id`),
  KEY `IDX_B6BD307F9AC0396` (`conversation_id`),
  KEY `IDX_B6BD307FEB3B4E33` (`deleted`),
  CONSTRAINT `FK_B6BD307F2130303A` FOREIGN KEY (`from_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_B6BD307F29F6EE60` FOREIGN KEY (`to_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_B6BD307F9AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `message` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `message` (`id`, `to_user_id`, `from_user_id`, `conversation_id`, `sender_name`, `subject`, `text`, `type`, `unread`, `deleted`, `date`) VALUES
(1,	1,	1,	NULL,	'',	'test',	'bla',	1,	1,	0,	'2015-11-08 22:23:20');

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

INSERT INTO `stat_daily` (`id`, `date`, `players_total`, `players_active`, `players_online`) VALUES
(1,	'2015-10-20',	5000,	1000,	100),
(2,	'2015-10-21',	5020,	1010,	105);

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
  `fb_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `donate` int(11) NOT NULL,
  `activated` tinyint(1) NOT NULL,
  `affiliate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL,
  `admin` tinyint(1) NOT NULL,
  `admin_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login_at` datetime NOT NULL,
  `last_activity_at` datetime NOT NULL,
  `registered_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649290B2F37` (`nick`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `user` (`id`, `nick`, `email`, `password`, `name`, `email_verified`, `verification_code`, `age`, `location`, `gender`, `fb_id`, `avatar`, `donate`, `activated`, `affiliate`, `token`, `token_expires_at`, `admin`, `admin_description`, `last_login_at`, `last_activity_at`, `registered_at`) VALUES
(1,	'grez',	'grez.cz@gmail.com',	'$2y$10$gvSOuFb84RqjXYf2useL7uls4w66cr1CFgrjqudQDpnhX68iieWZe',	'Blabla',	0,	3977968,	22,	'Budlik',	1,	'',	'',	0,	0,	'4243607',	NULL,	NULL,	1,	'test',	'2016-03-09 19:36:11',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(2,	'mario',	'mario@luigi.it',	'$2y$10$M.XXWG90oMbADreyxalOP.Un5hZclX6qtJ01GcwN9aw/cSd/saX6O',	'',	0,	5634476,	0,	'',	0,	'',	'',	0,	0,	'9236778',	NULL,	NULL,	0,	'',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(3,	'karel',	'karel@barel.cz',	'$2y$10$RSf27D0WNnI5k.2bABoa6OkKCEhlw5WNledtGtXaRICHgCnmzDToG',	'',	0,	5723935,	0,	'',	0,	'',	'',	0,	0,	'7625205',	NULL,	NULL,	0,	'',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `user_agent`;
CREATE TABLE `user_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C44967C5C44967C5` (`user_agent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `user_agent` (`id`, `user_agent`) VALUES
(3,	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36'),
(2,	'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/45.0.2454.101 Chrome/45.0.2454.101 Safari/537.36');

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

INSERT INTO `user_log` (`id`, `user_id`, `action`, `type`, `data`, `date`) VALUES
(1,	1,	8,	1,	'a:3:{i:0;s:12:\"143.12.13.15\";i:1;s:3:\"∞\";i:2;s:12:\"Je to krokot\";}',	'2015-11-01 23:31:30'),
(2,	1,	9,	1,	'a:2:{i:0;s:15:\"127.255.255.255\";i:1;s:12:\"Je to krokot\";}',	'2015-11-01 23:32:27'),
(3,	1,	6,	1,	's:4:\"grez\";',	'2015-11-08 22:00:41'),
(4,	1,	8,	1,	'a:3:{i:0;s:12:\"142.123.12.8\";i:1;i:12;i:2;s:5:\"Karel\";}',	'2016-03-09 19:39:16'),
(5,	1,	8,	1,	'a:3:{i:0;s:9:\"127.0.0.*\";i:1;i:15;i:2;s:8:\"knedlík\";}',	'2016-03-09 20:01:51'),
(6,	1,	9,	1,	'a:2:{i:0;s:9:\"127.0.0.*\";i:1;s:8:\"knedlík\";}',	'2016-03-09 20:01:57'),
(7,	1,	8,	1,	'a:3:{i:0;s:11:\"143.12.0.12\";i:1;i:58;i:2;s:6:\"jkhkjh\";}',	'2016-03-09 20:02:16');

-- 2016-03-09 21:22:15
