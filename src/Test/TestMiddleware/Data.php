<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-14
 */

namespace Wms\Test\TestMiddleware;

use Wms\Middleware\DataInterface;

class Data implements DataInterface
{
    public array $arr = [];
}
