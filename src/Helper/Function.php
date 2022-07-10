<?php
/**
 * @author Dawnc
 * @date   2020-05-10
 */

/**
 * 获取数组中某个值可以点取值
 * @param array           $arr
 * @param int|string|null $index
 * @param mixed|null      $default
 * @return mixed
 */
function arr_get(array $arr, $index, $default = null)
{

    if (is_null($index)) {
        return $arr;
    }

    if (is_int($index)) {
        return $arr[$index] ?? $default;
    }

    $keys = explode('.', $index);
    $tmp = $arr;
    foreach ($keys as $k) {
        if (isset($tmp[$k])) {
            $tmp = $tmp[$k];
        } else {
            return $default;
        }
    }
    return $tmp;
}
