<?php
namespace pietras;

class Router {
	private $url;
	private $pathElements;
	private $params;

	function __construct() {
		$this->refresh();
	}

	function refresh() {
		$this->url = $_SERVER["REQUEST_URI"];
		$this->params = $this->setParams();
		$dividedUrl = explode("?", $this->url);
		$this->pathElements = explode("/", $dividedUrl[0]);
		foreach ($this->pathElements as $key => $value) $this->pathElements[$key] = urldecode($value);
	}

		private function setParams() {
			$ret = [];
			$querystring = substr($this->url, strpos($this->url, "?")+1);
			$params = explode("&", $querystring);
			foreach ($params as $value) {
				$equalPos = strpos($value, "=");
				if ($equalPos !== false) {
					$key = substr($value, 0, strpos($value, "="));
					$paramValue = substr($value, strpos($value, "=")+1);
				} else {
					$key = $value;
					$paramValue = null;
				}
				$ret[$key] = $paramValue;
			}
			return $ret;
		}

	public function getPathElement($id) {
		if (isset($this->pathElements[$id])) return $this->pathElements[$id];
		else return false;
	}

	public function getParams() {
		return $this->params;
	}
}

?>
