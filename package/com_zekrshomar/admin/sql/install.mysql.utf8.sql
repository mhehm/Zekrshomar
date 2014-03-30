--
-- Table structure for table `#__zekrshomar_stats`
--

CREATE TABLE IF NOT EXISTS `#__zekrshomar_stats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `zekr_id` int(10) unsigned NOT NULL,
  `number` bigint(20) unsigned NOT NULL DEFAULT '0',
  `last` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE` (`user_id`,`zekr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__zekrshomar_zekrs`
--

CREATE TABLE IF NOT EXISTS `#__zekrshomar_zekrs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(3) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `mention` text COLLATE utf8_persian_ci NOT NULL,
  `image` text COLLATE utf8_persian_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_title` (`title`),
  KEY `idx_checked_out` (`checked_out`),
  KEY `idx_catid` (`catid`),
  KEY `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;