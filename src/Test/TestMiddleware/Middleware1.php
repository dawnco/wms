<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-14
 */

namespace Wms\Test\TestMiddleware;

use Closure;
use Wms\Middleware\DataInterface;
use Wms\Middleware\HandlerContract;


class Middleware1 implements HandlerContract
{
    public function handle(DataInterface $input, Closure $next): DataInterface
    {
        echo "middleware 1 before\n";
        $input->arr[] = "ware1";

        $input->arr[] = 'stop';
        return $input;
        
        $val = $next($input);
        echo "middleware 1 after\n";
        return $val;
    }

}
