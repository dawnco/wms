<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-03
 */

return [
    "/" => ['c' => \Wms\Test\App\Control\PortalControl::class, 'm' => "index", 'p' => '1', 'n' => ''],
    "x2" => ['c' => \Wms\Test\App\Control\PortalControl::class, 'm' => "index", 'p' => '2', 'n' => ''],
    "x3/(\d+)" => ['c' => \Wms\Test\App\Control\PortalControl::class, 'm' => "index", 'p' => '3', 'n' => ''],
];
