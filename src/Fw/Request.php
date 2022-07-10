<?php
/**
 * @author Dawnc
 * @date   2020-05-27
 */

namespace Wms\Fw;


class Request
{

    private array $rawJson;
    private array $post;
    private array $get;

    public function __construct()
    {
        $this->rawJson = json_decode(file_get_contents("php://input"), true) ?: [];
        $this->rawJson = $this->trim($this->rawJson);
        $this->post = $this->trim($_POST);
        $this->get = $this->trim($_GET);
    }

    public function getHeaderLine($name): string
    {
        $key = "HTTP_" . str_replace("-", "_", strtoupper($name));
        return $_SERVER[$key] ?? '';
    }

    public function input($key = null, $default = null)
    {

        $val = $this->data($key);
        if ($val !== null) {
            return $val;
        }

        $val = $this->post($key);
        if ($val !== null) {
            return $val;
        }

        $val = $this->get($key);
        if ($val !== null) {
            return $val;
        }

        return $default;

    }

    /**
     *  获取GET值 空字符串 则使用默认值
     * @param null $key
     * @param null $default
     * @return array|string|null
     */
    public function get($key = null, $default = null)
    {
        return $this->getValue($this->get, $key, $default);
    }

    /**
     * 获取POST值 空字符串 则使用默认值
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function post($key = null, $default = null)
    {
        return $this->getValue($this->post, $key, $default);
    }

    public function getMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? "GET");
    }

    /**
     * 获取JSON值 空字符串 则使用默认值
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function data($key = null, $default = null)
    {
        return $this->getValue($this->rawJson, $key, $default);
    }

    private function getValue($data, $key, $default)
    {
        if ($key === null) {
            return $data;
        } else {
            if (isset($data[$key])) {
                $value = $data[$key];
                return $value === '' ? $default : $value;
            } else {
                return $default;
            }
        }
    }

    /**
     * 去掉空格
     * @param $val
     * @return array|string
     */
    protected function trim($val)
    {
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                if (is_string($v)) {
                    $val[$k] = trim($v);
                }
            }
        } elseif (is_string($val)) {
            $val = trim($val);
        }
        return $val;
    }


    public function getPath(): string
    {
        if (isset($_GET['route'])) {
            $path = $_GET['route'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $pos = strpos($_SERVER['REQUEST_URI'], "?");
            $path = $pos > 0 ? substr($_SERVER['REQUEST_URI'], 0, $pos) : $_SERVER['REQUEST_URI'];
        } else {
            $path = "/";
        }
        return $path;
    }

}
