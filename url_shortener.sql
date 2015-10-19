CREATE TABLE IF NOT EXISTS `url_shortener` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `long_url` varchar(255) NOT NULL,
  `short_url` varchar(6) NOT NULL,
  `date_created` date NOT NULL,
  `ttl` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
