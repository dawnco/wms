<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-04
 */

namespace Wms\Fw;

abstract class Shell
{
    /**
     * 名称
     * @var string
     */
    public string $name = '';

    /**
     * @var string 描述
     */
    public string $description = '';

    /**
     * @var string 执行命令
     */
    public string $cmd = '';

    abstract public function handle(?array $param = null): void;

    protected function line(string $msg): void
    {
        echo $msg . "\n";
    }

}
