<?php
require "include/config.php";
require 'vendor/autoload.php';

use Acme\URLShortener;

try {
    $pdo = new PDO(DB_PDODRIVER . ':host=' . DB_HOST . ';dbname=' . DB_DATABASE .
        '', DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    exit;
}

$urlShortener = new URLShortener($pdo);
$urlShortener->shortToLongUrl("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

