<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-10
 */
require dirname(__DIR__, 2) . "/vendor/autoload.php";

define("APP_PATH", dirname(__DIR__, 1) . "/Test/app");

use Wms\Fw\Conf;
use Wms\Fw\Fw;

Conf::set('app', include APP_PATH . "/Conf/app.conf.php");
Conf::set('route', include APP_PATH . "/Conf/route.conf.php");
$fw = new Fw();
$fw->shell($argv);
