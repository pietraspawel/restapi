<?php

/**
 * Define autoloader rules.
 */

namespace pietras;

/**
 * Simple autoload function.
 *
 * Load class from 'libs/' folder.
 *
 * @param $className Name of the class.
 */
function autoload($className)
{
    $className = substr($className, strlen(__NAMESPACE__) + 1);
    require_once "libs/" . $className . '.php';
}
