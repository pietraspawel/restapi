<?php

/**
 * Application main constants.
 *
 * @global DB_HOST Database host.
 * @global DB_USER Database username.
 * @global DB_PASS User password.
 * @global DB_NAME Database name.
 * @global C Path to controllers folder.
 */

namespace pietras;

define('DB_HOST', $config["DB_HOST"]);
define('DB_USER', $config["DB_USER"]);
define('DB_PASS', $config["DB_PASS"]);
define('DB_NAME', $config["DB_NAME"]);
define("C", "controllers/");
