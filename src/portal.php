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
    define('IS_CLI', false);
}

date_default_timezone_set('PRC');

include WMS_PATH . "/fw/fn.php";
include APP_PATH . "/fn.php";
include WMS_PATH . "/fw/Fw.php";

\wms\fw\Fw::instance()->run();
