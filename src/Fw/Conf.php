<?php
/**
 * @author Dawnc
 * @date   2020-05-08
 */

namespace Wms\Fw;


class Conf
{
    private static array $data = [];

    /**
     * 获取配置值
     * @param int|string|null $key
     * @param mixed|null      $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {

        if (is_null($key)) {
            return self::$data;
        }

        if (is_int($key)) {
            return self::$data[$key] ?? $default;
        }

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

    /**
     * 设置值
     * @param string|int $key 可以点分割 比如  app.redis
     * @param mixed      $value
     * @return void
     */
    public static function set($key, $value): void
    {


        if (is_int($key)) {
            self::$data[$key] = $value;
            return;
        }

        if (strpos($key, ".") === false) {
            self::$data[$key] = $value;
            return;
        } else {
            $keys = explode(".", $key);
        }

        $array = &self::$data;
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;

    }

}
