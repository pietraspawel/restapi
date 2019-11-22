<?php

/**
 * Initialize main variables.
 *
 * @param $rest         Object controls apllication.
 * @param $url1         Shortcut to $rest variable.
 * @param $url2         Shortcut to $rest variable.
 * @param $page         Shortcut to $rest variable.
 * @param $pagesize     Shortcut to $rest variable.
 * @param $reuestMethod Shortcut to $rest variable.
 *
 * @param $auth         Provide user authorization methods.
 * @param $user         Shortcut to $_SERVER['PHP_AUTH_USER'].
 * @param $password     Shortcut to $_SERVER['PHP_AUTH_PW'].
 *
 * @param $database     Provide CRUD methods.
 */

namespace pietras;

spl_autoload_register(__NAMESPACE__ . "\autoload");
$config["DEBUG"] = isset($config["DEBUG"]) ? $config["DEBUG"] : false;

$rest = new RestApp();
$rest->setDebug($config["DEBUG"]);
$url1 = $rest->getUrl1();
$url2 = $rest->getUrl2();
$page = $rest->getPage();
$pagesize = $rest->getPageSize();
$requestMethod = $rest->getRequestMethod();

$auth = new AuthorizationDatabase(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;

$database = new ProductDatabase(DB_HOST, DB_USER, DB_PASS, DB_NAME);
