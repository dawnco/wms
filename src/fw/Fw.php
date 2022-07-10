<?php
/**
 * @author Dawnc
 * @date   2020-05-10
 */

namespace Wms\Fw;

use Throwable;
use Wms\Exception\Handler\ExceptionHandler;
use Wms\Exception\PageNotFoundException;
use Wms\Exception\WmsException;

class Fw
{

    public Route $route;

    public function __construct()
    {
        date_default_timezone_set(Conf::get('app.timezone', 'PRC'));
    }


    public function run(): void
    {
        $response = null;
        try {
            $request = new Request();
            $this->route = new Route($request);
            $response = $this->exec($request);
            if ($response instanceof Response) {
                $this->response($response);
            } else {
                $this->response((new Response())->withHeader('Content-type',
                    'application/json; charset=UTF-8')->withContent(json_encode([
                    'code' => 0,
                    "message" => "",
                    "data" => $response
                ])));
            }
        } catch (Throwable $e) {
            $handlerCls = Conf::get('app.exception.handler', ExceptionHandler::class);
            /**
             * @var  ExceptionHandler $handler
             */
            $handler = new $handlerCls();
            $this->response($handler->handle($e, $response ?: new Response()));
        }
    }


    private function response(Response $response): void
    {
        header(sprintf('HTTP/1.1 %s %s', $response->getStatusCode(),
            Response::getReasonPhraseByCode($response->getStatusCode())));

        foreach ($response->getHeaders() as $k => $v) {
            header("$k:" . implode(", ", $v));
        }

        echo $response->getBody();
    }

    /**
     * @throws PageNotFoundException
     * @throws WmsException
     */
    private function exec($request)
    {

        $this->route = new Route($request);
        $control = $this->route->getControl();
        $method = $this->route->getMethod();
        $param = $this->route->getParam();
        if (!class_exists($control)) {
            throw new PageNotFoundException($control . " File Not Found");
        }

        $classInstance = new $control($request);

        if (!method_exists($classInstance, $method)) {
            throw new PageNotFoundException($control . "->" . $method . "() Method Not Found");
        }

        return call_user_func_array(array($classInstance, $method), $param);

    }

    /**
     * @throws WmsException
     */
    public function shell($argv)
    {

        $name = $argv[1];


        if (!$name) {
            throw new WmsException("argument is empty");
        }


        if (class_exists($name)) {
            $clsName = $name;
        } else {
            $clsName = "\\App\\Shell\\" . $name;
        }

        if (!class_exists($clsName)) {
            throw new WmsException("$clsName SHELL不存在");
        }
        $cls = new $clsName();
        return $cls->run(array_slice($argv, 2));
    }
}
