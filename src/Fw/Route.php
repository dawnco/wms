<?php
/**
 * @author Dawnc
 * @date   2020-05-08
 */

namespace Wms\Fw;


use Wms\Constant\ErrorCode;
use Wms\Exception\PageNotFoundException;
use Wms\Exception\WmsException;

class Route
{
    private Request $request;
    private string $path;
    private string $control;
    private string $method;
    private array $param;

    /**
     * @return array
     */
    public function getParam(): array
    {
        return $this->param;
    }


    /**
     * @throws WmsException
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->path = $request->getPath();
        $this->initPath();
        $this->parse();
    }


    /**
     * @throws PageNotFoundException
     */
    public function parse(): void
    {
        $path = $this->path;
        $rules = Conf::get("route") ?? [];
        //是否配置过路由
        foreach ($rules as $u => $r) {
            $matches = array();
            if (preg_match("#^$u$#", $path, $matches)) {
                $this->param($r, $matches);
                return;
            }
        }
        throw new PageNotFoundException("no route match : $path", ErrorCode::PAGE_NOT_FOUND);
    }

    private function initPath(): void
    {

        $path = rtrim($this->path, "/");

        //去掉前缀
        $base_uri = rtrim(Conf::get("app.base_uri", ""), "/");
        if ($base_uri) {
            if (str_starts_with($path, $base_uri)) {
                $path = substr($path, strlen($base_uri));
            }
        }

        $path = rtrim($path, "/");

        //默认路由
        if (!$path) {
            $path = "/";
        }
        $this->path = $path;
    }

    private function param($rule, $matches = []): void
    {

        $this->control = $rule['c'] ?? '';
        $this->method = $rule['m'] ?? 'index';

        $url_param = array_slice($matches, 1);
        //合并参数
        if (isset($rule['p'])) {
            $param = array_merge((array)$rule['p'], $url_param);
        } else {
            $param = $url_param;
        }
        $this->param = $param;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getControl(): string
    {
        return $this->control;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
