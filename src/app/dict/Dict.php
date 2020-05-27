<?php
/**
 * @author Dawnc
 * @date   2020-05-15
 */

namespace app\dict;


abstract class Dict
{

    /**
     * 字典数据
     */
    protected static $data = [
        //id => name
        // 1 => 待确认
    ];

    /**
     * 字典数据 用户前台显示的 用于 同一值  前后台显示不一样的名称
     * 比如  1 后台表示带确认  用户需要显示成 带处理
     * @var array
     */
    protected static $dataFront = [
        //id => name
        // 2 => 待审核
    ];

    /**
     * 数据
     */
    public static function data()
    {
        //预初始
        return static::$data;
    }


    /**
     * 名称
     */
    public static function name($id)
    {
        return static::$data[$id] ?? false;
    }

    /**
     * 是否存在id
     * @param $id
     * @return bool
     */
    public static function has($id)
    {
        return isset(static::$data[$id]);
    }

    /**
     * 显示前台名称, 没有显示默认名称
     * @param $id
     * @return bool
     */
    public static function nameFront($id)
    {
        return static::$dataFront[$id] ?? static::name($id);
    }

}
