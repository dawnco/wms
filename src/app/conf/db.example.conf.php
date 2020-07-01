<?php

/**
 * @author  Dawnc
 * @date    2015-09-01
 */

return [
    "default" => array(
        "driver"   => \wms\database\Mysqli::class,
        "hostname" => "127.0.0.1",
        "username" => "root",
        "password" => "root",
        "database" => "laravel_admin",
        "port"     => 3306,
        "charset"  => "UTF8",
    ),
    "redis"   => [
        "hostname" => "127.0.0.1",
        "port"     => 6379,
        "password" => "a111111"
    ],
];

