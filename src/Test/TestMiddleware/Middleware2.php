<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-14
 */

namespace Wms\Test\TestMiddleware;

use Wms\Middleware\DataInterface;
use Wms\Middleware\HandlerContract;
use Wms\Middleware\OnionData;
use Closure;

class Middleware2 implements HandlerContract
{
    public function handle(DataInterface $input, Closure $next): DataInterface
    {
        echo "middleware 2 before\n";
        $input->arr[] = "ware2";
        $val = $next($input);
        echo "middleware 2 after\n";
        return $val;
    }
}
