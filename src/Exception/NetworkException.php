<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-04
 */

namespace Wms\Exception;

use Throwable;
use Wms\Constant\ErrorCode;

class NetworkException extends WmsException
{

    public function __construct(string $message, int $code = ErrorCode::NETWORK_ERROR, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
