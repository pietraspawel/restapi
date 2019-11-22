<?php

/**
 * Update product.
 */

namespace pietras;

if (!$auth->isWritingPermitted($user, $password)) {
    $auth->send401();
}
$changes = json_decode(file_get_contents('php://input'), true);
if (gettype($changes) != "array") {
    $rest->send400();
    die();
}
$res = $database->update($url2, $changes);
if ($res) {
    $rest->send200forNonGET();
    die();
} else {
    $rest->send404();
    die();
}
