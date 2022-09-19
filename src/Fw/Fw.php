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
    private function exec(Request $request)
    {

        $this->route = new Route($request);
        $control = $this->route->getControl();
        $method = $this->route->getMethod();
        $param = $this->route->getParam();
        if (!class_exists($control)) {
            throw new PageNotFoundException($control . " File Not Found");
        }
        $request->meta = $param;
        $classInstance = new $control($request);

        if (!method_exists($classInstance, $method)) {
            throw new PageNotFoundException($control . "->" . $method . "() Method Not Found");
        }

        return call_user_func_array(array($classInstance, $method), $param);

    }

    /**
     * @throws WmsException
     * @throws \ReflectionException
     */
    public function shell(array $param)
    {

        $cmd = $param[1] ?? '';

        $cli = [];
        $dir = Conf::get('app.shell_dir', "");
        if (!$dir) {
            throw new WmsException("shell_dir 没有指定");
        }
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                $basename = basename($file, ".php");
                $baseShellNamespace = Conf::get('app.baseShellNamespace', 'App\Shell');
                if ($basename != "." && $basename != "..") {
                    $className = $baseShellNamespace . "\\" . $basename;
                    if (!class_exists($className)) {
                        continue;
                    }
                    $ref = new \ReflectionClass($className);
                    $props = $ref->getDefaultProperties();
                    $cli[$props['cmd']] = [
                        "name" => $props['name'],
                        "cmd" => $props['cmd'],
                        "description" => $props['description'],
                        "class" => $className,
                    ];
                }
            }
        }


        if (!$cmd) {
            foreach ($cli as $item) {
                echo sprintf("%s\n\033[32m php shell.php %s \033[0m\n%s\n-----------------------\n", $item['name'],
                    $item['cmd'],
                    $item['description']);
            }
        } else {
            foreach ($cli as $item) {
                if ($item['cmd'] == $cmd) {
                    $clsName = $item['class'];
                    /**
                     * @var Shell $cls
                     */
                    $cls = new $clsName();
                    $cls->handle(array_slice($param, 2));
                    return;
                }
            }
        }
    }
}
