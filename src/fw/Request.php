<?php
/**
 * @author Dawnc
 * @date   2020-05-27
 */

namespace wms\fw;


class Request
{

    public function input($key = null)
    {
        $input = file_get_contents('php://input');

        parse_str($input, $data);
        if ($key == null) {
            return $data;
        } else {
            return $data[$key] ?? null;
        }
    }

    public function get($key = null)
    {
        if ($key == null) {
            return $_GET;
        } else {
            return $_GET[$key] ?? null;
        }
    }

    public function post($key = null)
    {
        if ($key == null) {
            return $_POST;
        } else {
            return $_POST[$key] ?? null;
        }
    }
}
