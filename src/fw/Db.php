<?php
/**
 * @author Dawnc
 * @date   2020-05-08
 */

namespace wms\fw;


use wms\database\IDatabase;
use wms\database\Mysqli;

class Db
{

    private static $instance = [];

    /**
     * @param $conf
     * @return Mysqli
     */
    public static function instance($conf = 'default')
    {
        if (!isset(self::$instance[$conf])) {
            $option                = Conf::get("db.$conf");
            $type                  = isset($option['driver']) ? $option['driver'] : "\\wms\\database\\Mysqli";
            self::$instance[$conf] = new $type($option);
        }
        return self::$instance[$conf];
    }

}
