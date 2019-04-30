<?php
namespace pietras;

if (!$auth->isReadingPermitted($user, $password)) $auth->send401();
$products = $database->fetch($url2);
$rest->send200forGET($products);
die();
