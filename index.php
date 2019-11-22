<?php

/**
 * Index file.
 */

namespace pietras;

include "autoloader.php";
$config = json_decode(file_get_contents("config.ini"), true);
include "define.php";
include "init.php";
include "front_controller.php";
if ($rest->getDebug()) {
    include "vars_monitor.php";
}
