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
    private array $urlParams = [];
    private array $meta = [];

    /**
     * @return array
     */
    public function getUrlParams(): array
    {
        return $this->urlParams;
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
            if (strpos($path, $base_uri) === 0) {
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

        if (is_array($rule['c'])) {
            // 路由配置
            $this->control = $rule['c'][0];
            $this->method = $rule['c'][1] ?? 'index';
        } else {
            $this->control = $rule['c'] ?? '';
            $this->method = $rule['m'] ?? 'index';
        }

        $this->meta = $rule['meta'] ?? [];
        $this->urlParams = $matches;

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

    public function getMeta(): array
    {
        return $this->meta;
    }

}
