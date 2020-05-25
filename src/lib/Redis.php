<?php
/**
 * @author Dawnc
 * @date   2020-05-24
 */

namespace wms\lib;


class Redis {

    private static $__instance = [];

    /**
     * @param null $conf
     * @return \Redis
     */
    public static function getInstance($conf = null) {
        $key = md5(serialize($conf));
        if (!isset(self::$__instance[$key])) {
            if ($conf == null) {
                $conf = Conf::get("db.redis");
            }
            self::$__instance[$key] = new \Redis();
            self::$__instance[$key]->connect($conf['host'], $conf['port']);
        }
        return self::$__instance[$key];
    }

}
