<?php
/**
 * @author Dawnc
 * @date   2020-05-27
 */

namespace wms\fw;


class Response
{
    public function http($code, $msg = null, $data = null)
    {
        $out['code'] = $code;
        if ($msg !== null) {
            $out['msg'] = $msg;
        }
        if ($data !== null) {
            $out['data'] = $data;
        }
        echo json_encode($out);
    }
}
