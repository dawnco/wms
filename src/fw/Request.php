<?php
/**
 * @author Dawnc
 * @date   2020-05-27
 */

namespace wms\fw;


class Request
{

    public function header($name)
    {
        $key = "HTTP_" . str_replace("-", "_", strtoupper($name));
        return $_SERVER[$key] ?? null;
    }

    public function input($key = null)
    {
        return $this->post($key) ?: $this->get($key);
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
