<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-14
 */

namespace Wms\Middleware;

class Onion
{

    protected array $middlewares = [];

    public function add(array $middlewares)
    {
        foreach ($middlewares as $ware) {
            if (is_string($ware)) {
                $this->middlewares[] = new $ware();
            } else {
                $this->middlewares[] = $ware;
            }
        }
    }

    public function start(DataInterface $input, \Closure $next): DataInterface
    {
        $this->middlewares = array_reverse($this->middlewares);
        foreach ($this->middlewares as $middleware) {
            $next = function ($input) use ($next, $middleware) {
                return $middleware->handle($input, $next);
            };
        }
        return $next($input);
    }
    //
    //public function __invoke(Input $input): Output
    //{
    //    return $this->handle($input);
    //}
}
