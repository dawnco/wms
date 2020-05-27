<?php
/**
 * @author Dawnc
 * @date   2020-05-10
 */

namespace wms\fw;


class Fw
{
    /**
     * @var Fw
     */
    public static $application = null;

    public $hook  = null;
    public $route = null;

    /**
     * @var Request
     */
    public $request = null;

    /**
     * @var Response
     */
    public $response = null;

    public static function instance()
    {
        return self::$application = new self();
    }


    public function __construct()
    {
        $this->autoload();
        $this->loadConf();

        $this->request  = new Request();
        $this->response = new Response();

    }

    public function loadConf()
    {
        //load config
        Conf::set('app', include APP_PATH . "/conf/app.conf.php");
        Conf::set('route', include APP_PATH . "/conf/route.conf.php");
        Conf::set('db', include APP_PATH . "/conf/db.conf.php");
    }

    public function run()
    {

        try {
            $this->exec();
        } catch (\Exception $e) {
            echo "<pre>";
            echo $e->getMessage();
            echo "\r\n";
            echo $e->getTraceAsString();
            echo "</pre>";
        }
    }

    private function exec()
    {
        $this->route = new Route();
        $this->hook  = new Hook();
        $this->hook();

        $this->hook->handle('pre_control');

        $control = $this->route->getControl();
        $method  = $this->route->getMethod();
        $param   = $this->route->getParam();

        if (!class_exists($control)) {
            throw new Exception($control . " File Not Found");
        }

        $classInstance = new $control();

        if (!method_exists($classInstance, $method)) {
            throw new Exception($control . "->" . $method . "() Method Not Found");
        }

        try {
            $this->response->http(0, null, call_user_func_array(array($classInstance, $method), $param));
        } catch (\Exception $e) {
            $this->response->http($e->getCode(), $e->getMessage());
        }

        $this->hook->handle('after_control');

    }

    public function shell()
    {
    }

    public function autoload()
    {
        spl_autoload_register([$this, 'autoloadFn']);
    }

    public function autoloadFn($class_name)
    {

        $_class = str_replace(array("\\", "/"), "/", $class_name) . ".php";

        $dir  = explode("/", $_class);
        $file = "";

        switch ($dir[0]) {
            case "wms":
                $file = WMS_PATH . "/" . substr($_class, 4);
            break;
            default :
                $file = WMS_PATH . "/" . $_class;
            break;
        }

        if (is_file($file)) {
            include $file;
        }
    }

    public function hook()
    {
        $hooks = Conf::get("hook");
        foreach ((array)$hooks as $preg => $hook) {
            if (preg_match("#^$preg$#i", $this->route->getUri())) {
                $this->hook->add($hook['weld'], [
                    new $hook['h'](),
                    isset($hook['m']) ? $hook['m'] : "hook"
                ], $hook['seq'], isset($hook['p']) ? $hook['p'] : []);
            }
        }
    }

}
