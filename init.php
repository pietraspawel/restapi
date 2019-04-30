<?php
namespace pietras;

$config = json_decode(file_get_contents("config.ini"), true);

define('DB_HOST', $config["DB_HOST"]);
define('DB_USER', $config["DB_USER"]);
define('DB_PASS', $config["DB_PASS"]);
define('DB_NAME', $config["DB_NAME"]);
$config["DEBUG"] = isset($config["DEBUG"])? $config["DEBUG"]: false;

$rest = new RestApp();
$rest->setDebug($config["DEBUG"]);

$database = new ProductDatabase(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$url1 = $rest->getUrl1();
$url2 = $rest->getUrl2();
$page = $rest->getPage();
$pagesize = $rest->getPageSize();
$requestMethod= $rest->getRequestMethod();

$auth = new AuthorizationDatabase(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$user = isset($_SERVER['PHP_AUTH_USER'])? $_SERVER['PHP_AUTH_USER']: null;
$password = isset($_SERVER['PHP_AUTH_PW'])? $_SERVER['PHP_AUTH_PW']: null;
