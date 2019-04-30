<?php
namespace pietras;
spl_autoload_register(__NAMESPACE__."\autoload");

function autoload($className) {
	$className = substr($className, strlen(__NAMESPACE__)+1);
	require_once "libs/".$className.'.php';
}
