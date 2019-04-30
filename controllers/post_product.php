<?php
namespace pietras;

if (!$auth->isWritingPermitted($user, $password)) $auth->send401();
$data = json_decode(file_get_contents('php://input'), true);
if (gettype($data) != "array") {
	$rest->send400();
	die();
}
$res = $database->insert($data);
if ($res) {
	$rest->send201();
	die();
} else {
	$rest->send400();
	die();
}
