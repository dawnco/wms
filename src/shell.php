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

include WMS_PATH . "/fw/fn.php";
include WMS_PATH . "/fw/Fw.php";

$fw = new \wms\fw\Fw();
$fw->shell();
