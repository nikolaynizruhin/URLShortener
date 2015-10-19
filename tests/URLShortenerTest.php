<?php
require "include/config.php";
require 'vendor/autoload.php';

use Acme\URLShortener;

/**
 * Class URLShortenerTest
 *
 * PHPUnit test URLShortener class.
 *
 * @author Nikolay Nizruhin
 * @copyright 2015 Nikolay Nizruhin
 * @license http://www.php.net/license/3_01.txt PHP License 3.01
 */
class URLShortenerTest extends PHPUnit_Framework_TestCase
{
    public $urlShortener;

    public function setUp()
    {
        $pdo = new PDO(DB_PDODRIVER . ':host=' . DB_HOST . ';dbname=' . DB_DATABASE . '', DB_USERNAME, DB_PASSWORD);
        $this->urlShortener = new URLShortener($pdo);
    }

    public function testCorrectUrlFormat()
    {
        $result = $this->urlShortener->validateUrlFormat('http://google.com');
        $this->assertEquals('http://google.com', $result);
    }

    public function testIncorrectUrlFormat()
    {
        $this->assertFalse($this->urlShortener->validateUrlFormat('qwerty'));
    }

    public function testShortUrlCreating()
    {
        $this->urlShortener->createShortUrl();
        $this->assertNotEmpty($this->urlShortener->shortUrl);
    }

    public function testShortUrlExistsInDb()
    {
        $this->assertTrue($this->urlShortener->shortUrlExistsInDb('DPwji'));
    }

    public function testShortUrlNotExistsInDb()
    {
        $this->assertFalse($this->urlShortener->shortUrlExistsInDb('DPwj1'));
    }

    public function testTtlSet()
    {
        $this->urlShortener->setTtl();
        $this->assertGreaterThanOrEqual(0, $this->urlShortener->ttl);
    }

    public function testUrlExistsInDb()
    {
        $this->assertNotEmpty($this->urlShortener->urlExistsInDb('http://youtube.com'));
    }

    public function testUrlNotExistsInDb()
    {
        $this->assertFalse($this->urlShortener->urlExistsInDb('http://youtube.com/test'));
    }

    public function testShortUrlGet()
    {
        $this->assertNotEmpty($this->urlShortener->getShortUrl('http://localhost/URLShortener/Qwe12r'));
    }

    public function testShortUrlNotGet()
    {
        $this->assertFalse($this->urlShortener->getShortUrl('http://localhost/qwe'));
    }

    public function testUrlNotGetFromDb()
    {
        $this->assertFalse($this->urlShortener->getUrlFromDb('12345'));
    }

    public function testUrlGetFromDb()
    {
        $this->assertNotEmpty($this->urlShortener->getUrlFromDb('B5ODl6'));
    }

    public function testValidTtl()
    {
        $this->assertTrue($this->urlShortener->validateTtl('8gTmbG'));
    }

    public function testNotValidTtl()
    {
        $this->assertFalse($this->urlShortener->validateTtl('7pdqT'));
    }
}