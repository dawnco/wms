<?php
/**
 * @author Dawnc
 * @date   2020-05-08
 */

namespace wms\fw;


class View
{

    private static $data = array();

    /**
     * 赋值模板
     * @param String|array $key
     * @param mixed        $val
     */
    public static function assign($key, $val = null)
    {
        if (is_array($key)) {
            self::$data = array_merge(self::$data, $key);
        } else {
            self::$data[$key] = $val;
        }
    }

    /**
     * 合并或者增加val 到 $key中
     * @param string  $key
     * @param mixed   $val    数组或者字符串
     * @param boolean $signal false  合并 $val 到 $key 中,  true 增加 val 到 $key中
     */
    public static function addValue($key, $val = '', $signal = false)
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
    public static function render($tpl = '', $data = array())
    {
        echo self::fetch($tpl, $data);
    }

    /**
     * 输出layout模板
     * @param string $tpl
     * @param array  $data
     * @param string $layout
     */
    public static function layout($tpl = "", $data = array(), $layout = "layout")
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
    public static function fetch($tpl = '', $data = array())
    {
        self::$data = array_merge(self::$data, $data);

        $_file = APP_PATH . "/view/$tpl.tpl.php";

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
