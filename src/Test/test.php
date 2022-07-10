<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-05-28
 */

require dirname(__DIR__, 2) . "/vendor/autoload.php";

use Wms\Fw\Conf;
use Wms\Fw\Fw;

Conf::set('app', include __DIR__ . "/app/Conf/app.conf.php");
$fw = new Fw();
$fw->shell($argv);
