<?php

namespace pietras;

/**
 * Stores applications states and provide respond methods.
 */
class RestApp
{
    /**
     * @var boolean $debug Tells if debug mode is on or not.
     */
    private $debug = false;
    /**
     * @var string $url1 Store first element of URL.
     */
    private $url1;
    /**
     * @var string $url1 Store second element of URL.
     */
    private $url2;
    /**
     * @var int $page Store page number argument from URL.
     */
    private $page;
    /**
     * @var int $pagesize Store size of page agument from URL.
     */
    private $pagesize;
    /**
     * @var $requestMethod Store value from $_SERVER["REQUEST_METHOD"].
     */
    private $requestMethod;

    /**
     * Class constructor.
     *
     * Has no parameters. Set properties from cofig file and url.
     */
    public function __construct()
    {
        $this->setDebug(false);
        $url = new Url();
        $this->url1 = StringMethods::smallPlToLatin($url->getPathElement(1));
        $this->url2 = StringMethods::smallPlToLatin($url->getPathElement(2));
        $this->page = $this->calculatePage($url);
        $this->pagesize = $this->calculatePagesize($url);
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
    }

    /**
     * Calculate page number.
     *
     * Extract from url. Default is 1. Should be 1 or more.
     *
     * @param  Url $url
     * @return int
     */
    private function calculatePage(Url $url): int
    {
        $urlParams = $url->getParams();
        $page = array_key_exists("page", $urlParams) ? intval($urlParams["page"]) : 1;
        if ($page < 1) {
            $page = 1;
        }
        return $page;
    }

    /**
     * Calculate page size.
     *
     * Extract from ur. Default is 10. Should be 10 or more.
     *
     * @param  Url $url
     * @return int
     */
    private function calculatePagesize(Url $url): int
    {
        $urlParams = $url->getParams();
        $pagesize = array_key_exists("pagesize", $urlParams) ? intval($urlParams["pagesize"]) : 10;
        if ($pagesize < 10) {
            $pagesize = 10;
        }
        return $pagesize;
    }

    /**
     * Set error reporting depending $value.
     *
     * @param bool $value
     */
    public function setDebug(bool $value): self
    {
        $this->debug = $value;
        if ($this->debug) {
            error_reporting(E_ALL);
        } else {
            error_reporting(0);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function getDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @return string
     */
    public function getUrl1(): string
    {
        return $this->url1;
    }

    /**
     * @return string
     */
    public function getUrl2(): string
    {
        return $this->url2;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pagesize;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * Send http respond.
     *
     * @param  mixed $json Returned string.
     * @return string
     */
    public function send200forGET($json)
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Cache-Control: public, max-age=86400");
        header('Content-Type: application/json');
        echo json_encode($json);
    }

    /**
     * Send http respond.
     */
    public function send200forNonGET()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK.");
        header("Cache-Control: no-cache, no-store, must-revalidate");
    }

    /**
     * Send http respond.
     */
    public function send201()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 201 OK.");
        header("Cache-Control: no-cache, no-store, must-revalidate");
    }

    /**
     * Send http respond.
     */
    public function send400()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Data error.");
        header("Cache-Control: no-cache, no-store, must-revalidate");
    }

    /**
     * Send http respond.
     */
    public function send404()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Source not found.");
        header("Cache-Control: no-cache, no-store, must-revalidate");
    }
}
