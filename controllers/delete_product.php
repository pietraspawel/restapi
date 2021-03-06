<?php

/**
 * Delete product.
 */

namespace pietras;

if (!$auth->isWritingPermitted($user, $password)) {
    $auth->send401();
}
$res = $database->delete($url2);
if ($res) {
    $rest->send200forNonGET();
    die();
} else {
    $rest->send404();
    die();
}
