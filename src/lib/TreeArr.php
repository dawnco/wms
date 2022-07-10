<?php
/**
 * @author Dawnc
 * @date   2020-05-12
 */

namespace Wms\Lib;


/**
 * 处理  二维数据  转换成 树
 * 例如
 * [
 *  [一级类名, 二级类名, 三级类名]
 *  [一级类名, 二级类名, 三级类名]
 *  [一级类名, 二级类名, 三级类名]
 * ],
 * 转为 id pid  二维数组格式
 * Class Arr2Tree
 */
class TreeArr
{
    protected $id = 100;

    /**
     * 处理后的数据 格式
     * a是b的父级 b是c的父级, id为 产生的数字
     * [
     * 'a'=> [name=> 名称, pid => 父id, id=> id] ,
     * 'a^b' => [name=> 名称, pid => 父id, id=> id],
     * 'a^b^c' => [name=> 名称, pid => 父id, id=> id]
     * ]
     */
    protected $data = [];
    // 待处理数据
    protected $srcData = [];

    public function __construct()
    {
    }

    public function id()
    {
        return $this->id++;
    }

    /**
     * @param array $arr 父路径的集合  [a, b, c] 层级关系是  c-> b-> a
     *                   a是b的父级 b是c的父级
     * @return array     生成的树二维结构
     */
    public function handle($arr = [])
    {

        $tmp = [];
        foreach ($arr as $v) {
            $this->genId($v);
        }

        return $this->data;

    }

    /**
     * 生成对应ID
     * @param array $arr [a,b,c]  a是b的父级
     */
    protected function genId($arr = [])
    {
        if (count($arr) == 0) {
            return 1;
        }
        $key = $this->wrap($arr);
        if (!isset($this->data[$key])) {
            $id =
            $this->data[$key] = [
                "name" => array_pop($arr),//名称
                "pid" => $this->genId($arr), //pid
                "id" => $this->id(), //id
            ];
        }

        return $this->data[$key]['id'];

    }


    protected function wrap($arr)
    {
        return implode("^", $arr);
    }

    public function demo()
    {

        $arr = [
            ["四川", "成都", "武侯区",],
            ["四川", "成都", "青羊区"],
            ["重庆", "渝北"],
            ["重庆", "渝北", "大竹林", "新山村"],
            ["重庆", "渝北", "木耳", "新山村"],
            ["重庆", "渝中"],
            ["广东", "东莞"],
            ["重庆", "渝北", "统景"],

        ];
        $data = $this->handle($arr);
        var_dump($data);
    }

}
