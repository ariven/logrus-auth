
--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `email_confirmed` tinyint(1) NOT NULL,
  `hash` varchar(256) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `display_name` varchar(128) NOT NULL,
  `login_authority` varchar(128) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `failed_logins`
--

CREATE TABLE IF NOT EXISTS `failed_logins` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `member_id` int(9) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fl_member_id_fkey` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `member_groups`
--

CREATE TABLE IF NOT EXISTS `member_groups` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `group_id` int(9) NOT NULL,
  `member_id` int(9) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mg_member_id_fkey` (`member_id`),
  KEY `mg_group_id_fkey` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `tag` varchar(100) NOT NULL,
  `description` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lgs`
--

CREATE TABLE IF NOT EXISTS `lgs` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `what` text NOT NULL,
  `created_at` datetime NOT NULL,
  `who` varchar(128) NOT NULL,
  `category` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `member_id` int(9) NOT NULL,
  `username` varchar(128) NOT NULL,
  `reset_code` varchar(128) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pr_member_id` (`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `member_id` int(9) NOT NULL,
  `profile_picture` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_member_user_id_fkey` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_auth`
--

CREATE TABLE IF NOT EXISTS `session_auth` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `member_id` int(9) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `ip_address` varchar(128) NOT NULL,
  `last_login` datetime NOT NULL,
  `logged_in` tinyint(1) NOT NULL,
  `login_authority` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `session_member_id_fkey` (`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `failed_logins`
--
ALTER TABLE `failed_logins`
  ADD CONSTRAINT `fl_member_id_fkey` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `member_groups`
--
ALTER TABLE `member_groups`
  ADD CONSTRAINT `mg_member_id_fkey` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mg_group_id_fkey` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `pr_member_id` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profile_member_user_id_fkey` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `session_auth`
--
ALTER TABLE `session_auth`
  ADD CONSTRAINT `session_member_id_fkey` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;