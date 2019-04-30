<?php
namespace pietras;
class RestApp {
	private $debug = false;
	private $url1, $url2;
	private $page, $pagesize;
	private $requestMethod;

	public function __construct() {
		$this->setDebug(false);
		$router = new Router();
		$this->url1 = StringMethods::smallPlToLatin($router->getPathElement(1));
		$this->url2 = StringMethods::smallPlToLatin($router->getPathElement(2));
		$this->page = $this->calculatePage($router);
		$this->pagesize = $this->calculatePagesize($router);
		$this->requestMethod = $_SERVER["REQUEST_METHOD"];
	}

		private function calculatePage($router) {
			$urlParams = $router->getParams();
			$page = array_key_exists("page", $urlParams)? intval($urlParams["page"]): 1;
			if ($page < 1) $page = 1;
			return $page;
		}

		private function calculatePagesize($router) {
			$urlParams = $router->getParams();
			$pagesize = array_key_exists("pagesize", $urlParams)? intval($urlParams["pagesize"]): 10;
			if ($pagesize < 10) $pagesize = 10;
			return $pagesize;
		}

	public function setDebug($value) { 
		$this->debug = $value;
		if ($this->debug) error_reporting(E_ALL);
		else error_reporting(0); 
		return $this; 
	}
	public function getDebug() { return $this->debug; }

	public function getUrl1() { return $this->url1; }
	public function getUrl2() { return $this->url2; }

	public function getPage() { return $this->page; }
	public function getPageSize() { return $this->pagesize; }

	public function getRequestMethod() { return $this->requestMethod; }

	public function send200forGET($json) {
		header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
		header("Cache-Control: public, max-age=86400");
		header('Content-Type: application/json');
		echo json_encode($json);
	}

	public function send200forNonGET() {
		header($_SERVER["SERVER_PROTOCOL"]." 200 OK.");
		header("Cache-Control: no-cache, no-store, must-revalidate");
	}

	public function send201() {
		header($_SERVER["SERVER_PROTOCOL"]." 201 OK.");
		header("Cache-Control: no-cache, no-store, must-revalidate");
	}

	public function send400() {
		header($_SERVER["SERVER_PROTOCOL"]." 400 Data error.");
		header("Cache-Control: no-cache, no-store, must-revalidate");
	}
}
