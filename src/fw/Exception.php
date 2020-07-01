<?php
/**
 * @author Dawnc
 * @date   2020-05-09
 */

namespace wms\fw;


class Exception extends \Exception
{
    public function __construct($message, $code = -1, $previous = null)
    {

        if (preg_match("/cli/i", php_sapi_name())) {
            $message = " \033[31;40m" .
                       $message .
                       "\033[0m ";
        }

        parent::__construct($message, $code, $previous);
    }
}
