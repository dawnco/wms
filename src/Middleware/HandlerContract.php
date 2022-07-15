<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-14
 */

namespace Wms\Middleware;

use Closure;

interface HandlerContract
{
    public function handle(DataInterface $input, Closure $next): DataInterface;
}
