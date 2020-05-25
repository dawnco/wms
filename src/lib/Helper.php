<?php
/**
 * @author Dawnc
 * @date   2020-05-23
 */

namespace wms\lib;


class Helper
{
    public static function lineToHump($str)
    {
        return preg_replace_callback('/(_[a-z])/', function ($match) {
            return ucfirst(trim($match[0], '_'));
        }, $str);
    }

    public static function humpToLine($str)
    {
        return preg_replace_callback('/([A-Z])/', function ($match) {
            return '_' . lcfirst($match[0]);
        }, $str);
    }
}
