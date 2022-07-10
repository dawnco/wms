<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-03
 */

namespace Wms\Exception;

use Throwable;
use Wms\Constant\ErrorCode;

class WmsException extends \Exception
{

    protected array $errorData = [];

    public function __construct(string $message, int $code = ErrorCode::SYSTEM_ERROR, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function setErrorData(array $errorData)
    {
        $this->errorData = $errorData;
    }

    public function getErrorData(): array
    {
        return $this->errorData;
    }
}
