<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-09
 */

namespace Wms\Exception\Handler;

use Throwable;
use Wms\Constant\ErrorCode;
use Wms\Fw\Conf;
use Wms\Fw\Response;

class ExceptionHandler
{

    /**
     * @param Throwable $throwable
     * @param Response  $response
     * @return Response
     */
    public function handle(Throwable $throwable, Response $response): Response
    {

        $code = $throwable->getCode();
        $row = [
            'code' => $code ?: ErrorCode::SYSTEM_ERROR,
            'message' => $throwable->getMessage(),
        ];

        if (Conf::get('app.env') == 'dev') {
            $row['exception'] = [
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTrace(),
            ];
        }

        return $response->withHeader('Content-type', 'application/json; charset=UTF-8')->withContent(json_encode($row,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
