-- Asosiy foydalanuvchilar jadvali
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `main_balance` decimal(15,2) DEFAULT '0.00',
  `date` date DEFAULT NULL,
  `action` enum('member','kicked') DEFAULT 'member',
  `status` enum('on','off') DEFAULT 'on',
  `admin` enum('0','1') DEFAULT '0',
  `referal` int(11) DEFAULT '0',
  `ref_id` bigint(20) DEFAULT NULL,
  `step` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kanalar jadvali
CREATE TABLE IF NOT EXISTS `channels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channelID` varchar(100) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `type` enum('lock','request','free') DEFAULT 'free',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- To'lovlar jadvali
CREATE TABLE IF NOT EXISTS `card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `number` varchar(50) DEFAULT NULL,
  `owner` varchar(255) DEFAULT NULL,
  `status` enum('on','off') DEFAULT 'on',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Referallar jadvali
CREATE TABLE IF NOT EXISTS `referals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
