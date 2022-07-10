<?php
/**
 * @author Dawnc
 * @date   2020-05-08
 */

namespace Wms\Fw;


class View
{

    private static array $data = [];

    /**
     * 赋值模板
     * @param array|String $key
     * @param mixed        $val
     */
    public static function assign(array|string $key, mixed $val = null): void
    {
        if (is_array($key)) {
            self::$data = array_merge(self::$data, $key);
        } else {
            self::$data[$key] = $val;
        }
    }

    /**
     * 合并或者增加val 到 $key中
     * @param string       $key
     * @param array|string $val    数组或者字符串
     * @param boolean      $signal false  合并 $val 到 $key 中,  true 增加 val 到 $key中
     */
    public static function addValue(string $key, array|string $val = [], bool $signal = false): void
    {
        if (is_string($val) || $signal) {
            self::$data[$key][] = $val;
        } elseif (is_array($val)) {
            self::$data[$key] = array_merge(isset(self::$data[$key]) ? self::$data[$key] : array(), $val);
        }
    }

    public static function getData()
    {
        return self::$data;
    }

    /**
     * 输出模板
     * @param string $tpl
     * @param array  $data
     */
    public static function render(string $tpl = '', array $data = [])
    {
        echo self::fetch($tpl, $data);
    }

    /**
     * 输出layout模板
     * @param string $tpl
     * @param array  $data
     * @param string $layout
     */
    public static function layout(string $tpl = "", array $data = [], string $layout = "layout"): void
    {
        $data['tpl'] = $tpl;
        self::render($layout, $data);
    }

    /**
     * 渲染模板
     * @param string $tpl  模板文件
     * @param array  $data 数据
     * @return string
     */
    public static function fetch(string $tpl = '', array $data = array()): string
    {
        self::$data = array_merge(self::$data, $data);

        $_file = APP_PATH . "/View/$tpl.tpl.php";

        if (self::$data) {
            extract(self::$data);
        }
        ob_start();
        include $_file;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

}
