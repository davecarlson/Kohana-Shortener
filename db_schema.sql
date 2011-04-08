SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Table structure for table `shortener`
--

CREATE TABLE IF NOT EXISTS `shortener` (
  `code` varchar(6) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`code`),
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;