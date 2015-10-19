<?php
// Set configuration for database
define("DB_PDODRIVER", "mysql");
define("DB_HOST", "localhost");
define("DB_DATABASE", "db");
define("DB_TABLE", "url_shortener");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "root");
define("DOMAIN_NAME", "http://" . $_SERVER['HTTP_HOST'] . "/");