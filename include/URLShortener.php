<?php
namespace Acme;

/**
 * Class URLShortener
 *
 * Class for convert URL to short code and back. Redirect to URL.
 *
 * @author Nikolay Nizruhin
 * @copyright 2015 Nikolay Nizruhin
 * @license http://www.php.net/license/3_01.txt PHP License 3.01
 * @package Acme
 */
class URLShortener
{
    /**
     * PDO object
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * URL string
     *
     * @var string
     */
    protected $longUrl;

    /**
     * Short code
     *
     * @var string
     */
    protected $shortUrl;

    /**
     * Time to live
     *
     * @var integer
     */
    protected $ttl;

    /**
     * Set PDO object
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Validate URL format
     *
     * @param string $url
     * @return mixed
     */
    protected function validateUrlFormat($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }

    /**
     * Create short code
     */
    protected function createShortUrl()
    {
        $this->shortUrl = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
        if ($this->shortUrlExistsInDb($this->shortUrl)) {
            $this->createShortUrl();
        }
    }

    /**
     * Verify short code exist in database
     *
     * @param string $shortUrl
     * @return bool
     */
    protected function shortUrlExistsInDb($shortUrl)
    {
        $stmt = $this->pdo->prepare("SELECT short_url FROM " . DB_TABLE .
            " WHERE short_url = :shortUrl LIMIT 1");
        $stmt->bindParam(':shortUrl', $shortUrl);
        $stmt->execute();
        $result = $stmt->fetch();
        return empty($result) ? false : true;
    }

    /**
     * Set time to live
     */
    protected function setTtl()
    {
        $this->ttl = 0;
        if (isset($_GET['ttl']) && !empty($_GET['ttl'])) {
            $this->ttl = $_GET['ttl'];
        }
    }

    /**
     * Insert URL to database
     *
     * @param string $url
     */
    protected function insertUrlToDB($url)
    {
        $date = date('Y-m-d');
        $stmt = $this->pdo->prepare("INSERT INTO " . DB_TABLE .
            " (long_url, short_url, date_created, ttl) VALUES (:longUrl, :shortUrl, :date, :ttl)");
        $stmt->bindParam(':longUrl', $url);
        $stmt->bindParam(':shortUrl', $this->shortUrl);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':ttl', $this->ttl);
        $stmt->execute();
    }

    /**
     * Verify URL exists in database
     *
     * @param string $url
     * @return bool
     */
    protected function urlExistsInDb($url)
    {
        $stmt = $this->pdo->prepare("SELECT short_url FROM " . DB_TABLE .
            " WHERE long_url = :longUrl LIMIT 1");
        $stmt->bindParam(':longUrl', $url);
        $stmt->execute();
        $result = $stmt->fetch();
        if (!empty($result)) {
            $this->updateTtl($url);
            return $result['short_url'];
        } else {
            return false;
        }
    }

    /**
     * Update time to live
     *
     * @param string $url
     */
    protected function updateTtl($url)
    {
        $stmt = $this->pdo->prepare("UPDATE " . DB_TABLE .
            " SET ttl = :ttl WHERE long_url = :longUrl");
        $stmt->bindParam(':ttl', $this->ttl);
        $stmt->bindParam(':longUrl', $url);
        $stmt->execute();
    }

    /**
     * Get short code from URL
     *
     * @param string $url
     * @return bool
     */
    protected function getShortUrl($url)
    {
        $url = explode('/', $url);
        if (isset($url[4]) && !empty($url[4])) {
            return preg_match('/^[0-9a-zA-Z]{6}$/', $url[4]) ? $url[4] : false;
        } else {
            return false;
        }
    }

    /**
     * Get URL from database
     *
     * @param string $shortUrl
     * @return bool
     */
    protected function getUrlFromDb($shortUrl)
    {
        $stmt = $this->pdo->prepare("SELECT long_url FROM " . DB_TABLE .
            " WHERE short_url = :shortUrl LIMIT 1");
        $stmt->bindParam(':shortUrl', $shortUrl);
        $stmt->execute();
        $result = $stmt->fetch();
        return empty($result) ? false : $result['long_url'];
    }

    /**
     * Validate time to live
     *
     * @param string $shortUrl
     * @return bool
     */
    protected function validateTtl($shortUrl)
    {
        $stmt = $this->pdo->prepare("SELECT date_created, ttl FROM " . DB_TABLE .
            " WHERE short_url = :shortUrl LIMIT 1");
        $stmt->bindParam(':shortUrl', $shortUrl);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result['ttl'] == 0) {
            return true;
        } else {
            $dStart = new \DateTime($result['date_created']);
            $dEnd  = new \DateTime(date('Y-m-d'));
            $dDiff = $dStart->diff($dEnd);
            return ($dDiff->days > $result['ttl']) ? false : true;
        }
    }

    /**
     * Delete URL form database
     *
     * @param string $shortUrl
     * @return int
     */
    protected function removeUrl($shortUrl)
    {
        return $this->pdo->exec("DELETE FROM " . DB_TABLE .
            " WHERE short_url = '" . $shortUrl . "'");
    }

    /**
     * Print json format
     *
     * @param array $arr
     */
    protected function jsonOutput(array $arr)
    {
        echo json_encode($arr);
    }

    /**
     * Convert short code to URL link
     *
     * @param string $url
     */
    public function shortToLongUrl($url)
    {
        $this->shortUrl = $this->getShortUrl($url);
        if (!$this->validateTtl($this->shortUrl)) {
            $this->removeUrl($this->shortUrl);
            return header("Location: " . DOMAIN_NAME . "404.php");
        }
        $this->longUrl = $this->getUrlFromDb($this->shortUrl);
        if ($this->longUrl) {
            header("Location: " . $this->longUrl);
            exit;
        } else {
            header("Location: " . DOMAIN_NAME . "404.php");
            exit;
        }
    }

    /**
     * Convert URL to short code
     *
     * @param string $url
     */
    public function urlToShortUrl($url)
    {
        if (!$this->validateUrlFormat($url)) {
            return $this->jsonOutput(array('error' => true));
        }
        $this->setTtl();
        $this->shortUrl = $this->urlExistsInDb($url);
        if (!$this->shortUrl) {
            $this->createShortUrl();
            $this->insertUrlToDB($url);
        }
        return $this->jsonOutput(array('url' => DOMAIN_NAME . $this->shortUrl));
    }
}
