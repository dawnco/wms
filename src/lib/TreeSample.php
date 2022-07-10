<?php
/**
 * @author Dawnc
 * @date   2020-06-03
 */

namespace Wms\Lib;


class TreeSample
{

    private $items = [];


    private $config = [
        // id名称
        'idName' => 'id',
        // 父ID名称
        'pidName' => 'parentId',
        //子节点名称
        'childName' => 'children'
    ];

    public function __construct($items, $config = [])
    {
        $this->processedItems = $this->items = $items;

        if ($config) {
            $this->config = $config;
        }
    }

    /**设置数据 数据格式
     * @param       $items [["id","pid",..] , ["id","pid",..] ..]
     * @param array $data
     */

    private function format()
    {
        $data = [];
        foreach ($this->items as $vo) {
            $data[$vo[$this->config['idName']]] = $vo;
        }

        $this->items = $data;

        return $this;
    }

    /**
     *  格式化树  转换成 key 为id的树
     * @param array $items
     * @return array
     */
    private function treeAssocTree($id)
    {
        $items = $this->items;
        foreach ($items as $item) {
            $items[$item[$this->config['pidName']]][$this->config['childName']][$item[$this->config['idName']]] = &
                $items[$item[$this->config['idName']]];
        }
        return $items[$id][$this->config['childName']] ?? [];
    }

    /**
     * 转换成 id关联的数
     * @param int $id
     * @return array
     */
    public function treeAssoc($id = 0)
    {
        return $this->format()->treeAssocTree($id);
    }

    /**
     * 转成id 非关联的树
     * @return array
     */
    public function tree()
    {
        return $this->list_to_tree($this->items, $this->config['idName'], $this->config['pidName'],
            $this->config['childName']);
    }

    /**
     * 转换成树 key自增
     * https://blog.csdn.net/qq_41654694/article/details/87877930
     */
    private function list_to_tree($list, $pk = 'zid', $pid = 'fid', $child = 'kid', $root = 0)
    {
        //创建Tree
        $tree = array();
        if (is_array($list)) {
            //创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                //判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }

        return $tree;
    }


}
