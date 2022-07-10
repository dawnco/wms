<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-03
 */

namespace Wms\Exception;

use Throwable;
use Wms\Constant\ErrorCode;

class PageNotFoundException extends WmsException
{
    public function __construct(string $message, int $code = ErrorCode::PAGE_NOT_FOUND, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
