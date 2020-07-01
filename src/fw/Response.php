<?php
/**
 * @author Dawnc
 * @date   2020-05-27
 */

namespace wms\fw;


class Response
{

    public function json($code, $msg = null, $data = null)
    {
        $out['code'] = $code;
        if ($msg !== null) {
            $out['msg'] = $msg;
        }
        if ($data !== null) {
            $out['data'] = $data;
        }
        $out['debug'] = Db::instance()->sql;
        return $out;
    }

    public function send($str)
    {
        header('content-type:application/json;charset=utf-8');
        echo $str;
    }
}
