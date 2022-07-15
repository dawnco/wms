<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-14
 */

namespace Wms\Test;

use Wms\Fw\Shell;
use Wms\Middleware\Input;
use Wms\Middleware\Onion;
use Wms\Middleware\Runner;
use Wms\Test\TestMiddleware\Data;
use Wms\Test\TestMiddleware\Middleware1;
use Wms\Test\TestMiddleware\Middleware2;
use Wms\Test\TestMiddleware\TInput;
use Wms\Test\TestMiddleware\TOutput;

/**
 *  php src/Test/shell.php  \\Wms\\Test\\TestMiddleware
 */
class TestMiddleware extends Shell
{
    protected function handle(?array $param = null): void
    {

        $onion = new Onion();
        $onion->add([
            Middleware1::class,
            Middleware2::class,
        ]);
        $data = new Data();
        $data->arr[] = 'input';
        $output = $onion->start($data, function ($input) {
            return $input;
        });
        var_dump($output);
    }

}
