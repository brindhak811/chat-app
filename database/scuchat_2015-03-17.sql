# ************************************************************
# Sequel Pro SQL dump
# Version 4004
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.6.14)
# Database: scuchat
# Generation Time: 2015-03-17 23:11:36 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table scuchat_active_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `scuchat_active_users`;

CREATE TABLE `scuchat_active_users` (
  `user_id` mediumint(9) NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `scuchat_active_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `scuchat_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table scuchat_friends
# ------------------------------------------------------------

DROP TABLE IF EXISTS `scuchat_friends`;

CREATE TABLE `scuchat_friends` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `friend_user_id` mediumint(9) NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(100) DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`friend_user_id`),
  KEY `friend_user_id` (`friend_user_id`),
  CONSTRAINT `scuchat_friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `scuchat_user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `scuchat_friends_ibfk_2` FOREIGN KEY (`friend_user_id`) REFERENCES `scuchat_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `scuchat_friends` WRITE;
/*!40000 ALTER TABLE `scuchat_friends` DISABLE KEYS */;

INSERT INTO `scuchat_friends` (`id`, `user_id`, `friend_user_id`, `date_created`, `date_modified`, `status`)
VALUES
	(1,2,1,'2015-03-17 09:12:34','2015-03-17 09:12:34','Active'),
	(2,1,2,'2015-03-17 09:12:34','2015-03-17 09:12:34','Active'),
	(3,2,4,'2015-03-17 15:53:12','2015-03-17 15:53:12','Active'),
	(4,4,2,'2015-03-17 15:53:12','2015-03-17 15:53:12','Active');

/*!40000 ALTER TABLE `scuchat_friends` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table scuchat_messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `scuchat_messages`;

CREATE TABLE `scuchat_messages` (
  `msg_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `from_user_id` mediumint(9) NOT NULL,
  `to_user_id` mediumint(9) NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  PRIMARY KEY (`msg_id`),
  KEY `from_user_id` (`from_user_id`),
  KEY `to_user_id` (`to_user_id`),
  CONSTRAINT `scuchat_messages_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `scuchat_user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `scuchat_messages_ibfk_2` FOREIGN KEY (`to_user_id`) REFERENCES `scuchat_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `scuchat_messages` WRITE;
/*!40000 ALTER TABLE `scuchat_messages` DISABLE KEYS */;

INSERT INTO `scuchat_messages` (`msg_id`, `from_user_id`, `to_user_id`, `date_created`, `message`)
VALUES
	(1,2,1,'2015-03-17 09:12:56','Hello Lincoln. Long time since we met. Let\'s catch up sometime.\n'),
	(2,1,2,'2015-03-17 09:14:03','Sure, would love to. How about next week?'),
	(3,1,2,'2015-03-17 15:54:04','Are you there?\n'),
	(4,2,4,'2015-03-17 15:55:14','Hi Steffi'),
	(5,1,2,'2015-03-17 15:55:57','I just realized I\'m out for work next week\n'),
	(6,1,2,'2015-03-17 15:56:12','So how about the week after that?\n'),
	(7,1,2,'2015-03-17 16:07:42','Looks like you are offline\n'),
	(8,1,2,'2015-03-17 16:07:50','Will ping you later'),
	(9,2,1,'2015-03-17 16:08:23','Sorry I was out for a while\n'),
	(10,2,1,'2015-03-17 16:08:38','yes, week after next week works for me.\n'),
	(11,1,2,'2015-03-17 16:09:01','ok, then.\n'),
	(12,1,2,'2015-03-17 16:09:10','see you in 2 weeks\n');

/*!40000 ALTER TABLE `scuchat_messages` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table scuchat_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `scuchat_user`;

CREATE TABLE `scuchat_user` (
  `user_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `login_pwd` varchar(50) DEFAULT NULL,
  `email_address` varchar(100) NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(100) DEFAULT 'user',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email_address` (`email_address`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `scuchat_user` WRITE;
/*!40000 ALTER TABLE `scuchat_user` DISABLE KEYS */;

INSERT INTO `scuchat_user` (`user_id`, `first_name`, `last_name`, `login_pwd`, `email_address`, `date_created`, `date_modified`, `role`)
VALUES
	(1,'Abraham','Lincoln','hello123','lincoln@scuchat.net','2015-03-17 07:53:07','2015-03-17 07:53:07','user'),
	(2,'Brad','Pitt','hello123','brad@scuchat.net','2015-03-17 07:55:06','2015-03-17 07:55:06','user'),
	(3,'Brindha','Krishnamoorthy','hello123','brindha@scuchat.net','2015-03-17 07:55:31','2015-03-17 07:55:31','user'),
	(4,'Steffi','Graf','hello123','steffi@scuchat.net','2015-03-17 07:56:36','2015-03-17 07:56:36','user');

/*!40000 ALTER TABLE `scuchat_user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
