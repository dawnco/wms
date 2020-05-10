<?php
/**
 * @author Dawnc
 * @date   2020-05-08
 */

namespace wms\fw;


class Conf
{
    private static $data = [];

    public static function get($key, $default = null)
    {

        if (strpos($key, '.') === false) {
            return self::$data[$key] ?? $default;
        }

        $conf = self::$data;
        foreach (explode(".", $key) as $n) {
            if (isset($conf[$n])) {
                $conf = $conf[$n];
            } else {
                return $default;
            }
        }
        return $conf;
    }

    public static function set($key, $value)
    {
        $keys = is_array($key) ? $key : [$key => $value];
        foreach ($keys as $key => $value) {
            self::$data[$key] = $value;
        }
    }

}
