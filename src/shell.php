<?php
/**
 * @author Dawnc
 * @date   2020-05-10
 */

if (!defined('APP_NAME')) {
    define('APP_NAME', 'app');
}

if (!defined('APP_PATH')) {
    define('APP_PATH', __DIR__ . '/app');
}

if (!defined('WMS_PATH')) {
    define('WMS_PATH', __DIR__);
}

if (!defined('IS_CLI')) {
    define('IS_CLI', true);
}
date_default_timezone_set('PRC');
include WMS_PATH . "/fw/fn.php";
include APP_PATH . "/fn.php";
include WMS_PATH . "/fw/Fw.php";

\wms\fw\Fw::instance()->shell();


function out()
{
    echo date("Y-m-d H:i:s");
    $args = func_get_args();
    echo " \033[31;40m";
    echo array_shift($args);
    echo "\033[0m ";
    foreach ($args as $v) {
        echo $v;
        echo " ";
    }
    echo "\n";
}


$name = $argv[1];

include __DIR__ . "/app/shell/" . $name . ".shell.php";



