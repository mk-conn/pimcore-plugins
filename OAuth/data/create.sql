DROP TABLE IF EXISTS `plugin_oauth`;
CREATE TABLE `plugin_oauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumerSecret` varchar(125) DEFAULT NULL,
  `consumerKey` varchar(125) DEFAULT NULL,
  `accessToken` mediumtext,
  `className` varchar(125) NOT NULL,
  `title` varchar(75) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;