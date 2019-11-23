<?php

namespace pietras;

/**
 * Provide url methods.
 */
class Url
{
    /**
     * @var string $url Stores url.
     */
    private $url;
    /**
     * @var array $pathElements Array of path elements (parts of url separated by slash).
     */
    private $pathElements;
    /**
     * @var array $params Array of parameters (parts of url after exclamation mark).
     */
    private $params;

    /**
     * Class constructor.
     *
     * Calculates properties.
     */
    public function __construct()
    {
        $this->refresh();
    }

    /**
     * Recalculates properties.
     */
    private function refresh()
    {
        $this->url = $_SERVER["REQUEST_URI"];
        $this->params = $this->setParams();
        $dividedUrl = explode("?", $this->url);
        $this->pathElements = explode("/", $dividedUrl[0]);
        foreach ($this->pathElements as $key => $value) {
            $this->pathElements[$key] = urldecode($value);
        }
    }

    /**
     * Extract parameters from url (part after '?').
     *
     * @return array Array of url parameters.
     */
    private function setParams()
    {
        $ret = [];
        $querystring = substr($this->url, strpos($this->url, "?") + 1);
        $params = explode("&", $querystring);
        foreach ($params as $value) {
            $equalPos = strpos($value, "=");
            if ($equalPos !== false) {
                $key = substr($value, 0, strpos($value, "="));
                $paramValue = substr($value, strpos($value, "=") + 1);
            } else {
                $key = $value;
                $paramValue = null;
            }
            $ret[$key] = $paramValue;
        }
        return $ret;
    }

    /**
     * @param  int $id Parameter index.
     * @return string|false
     */
    public function getPathElement(int $id)
    {
        if (isset($this->pathElements[$id])) {
            return $this->pathElements[$id];
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
