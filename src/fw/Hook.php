<?php
/**
 * @author Dawnc
 * @date   2020-05-10
 */

namespace wms\fw;


class Hook
{

    private $callbacks = [];

    /**
     * @param type $name 名称
     * @param type $callback
     * @param type $seq  按升序
     * @param type $parameter
     */
    public function add($name, $callback, $seq = 10, $parameter = [])
    {

        if (!isset($this->callbacks[$name])) {
            $this->callbacks[$name] = [];
        }

        $this->callbacks[$name][] = [
            "callback"  => $callback,
            "seq"       => $seq,
            "parameter" => $parameter,
        ];

        usort($this->callbacks[$name], [$this, "sort"]);


    }

    /**
     * 执行action
     * @param type $name
     * @param type $parameter
     */
    public function handle($name, $parameter = [])
    {
        $callbacks = $this->callbacks[$name] ?? [];

        foreach ($callbacks as $k => $c) {
            //执行
            call_user_func_array($c['callback'], array_merge($c['parameter'], $parameter));
        }
    }

    /**
     * 按升序排列
     * @param type $a
     * @param type $b
     * @return int
     */
    public function sort($a, $b)
    {
        if ($a['seq'] == $b['seq']) {
            return 0;
        }
        return $a['seq'] > $b['seq'] ? 1 : -1; // 按升序排列
    }
}
