<?php

/**
 * List variables values if $rest->getDebug() is true.
 */

namespace pietras;

echo '<pre>';
echo '$url1: ';
var_dump($url1);
echo '$url2: ';
var_dump($url2);
echo '$requestMethod: ';
var_dump($requestMethod);
echo '$file: ';
print_r($file);
echo '</pre>';
