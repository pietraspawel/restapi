<?php

/**
 * Main controller.
 */

namespace pietras;

$file = null;

if ($url1 == "products" and $url2 == "") {
    if ($requestMethod == "GET") {
        $file = C . "get_products.php";
    }
    if ($requestMethod == "POST") {
        $file = C . "post_product.php";
    }
}
if ($url1 == "products" and $url2 != "") {
    if ($requestMethod == "GET") {
        $file = C . "get_single_product.php";
    }
    if ($requestMethod == "PUT") {
        $file = C . "put_product.php";
    }
    if ($requestMethod == "DELETE") {
        $file = C . "delete_product.php";
    }
}
if ($url1 == "help") {
    $file = C . "help.php";
}
if ($file !== null) {
    include "$file";
} else {
    $rest->send404();
    die();
}
