<?php

/**
 * Get all products.
 */

namespace pietras;

if (!$auth->isReadingPermitted($user, $password)) {
    $auth->send401();
}
$products = $database->fetchAll($page, $pagesize);
$rest->send200forGET($products);
die();
