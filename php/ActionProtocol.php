<?php

include_once dirname(__FILE__) . '/../../yarn/CoastSerializableFunctions.php';

/**
 * Class ActionProtocol
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class ActionProtocol {

    /**
     * A register of closures.
     * @var array Closure
     */
    private $actionRegister = array();

    /**
     * The API handler
     * @var \Slim\Slim
     */
    private $app = null;

    /**
     * Unregistered actions.
     * @var array
     */
    private $unregistered = array();

    /**
     * The only instance.
     * @var ActionProtocol
     */
    private static $singleton = false;

    /**
     * Create or get the singleton action protocol instance.
     * @param \Slim\Slim $app
     * @return ActionProtocol
     */
    public static function instance(\Slim\Slim $app = null) {
        if (!ActionProtocol::$singleton) {
            ActionProtocol::$singleton = new ActionProtocol($app);
        } else {
            if ($app !== null) {
                ActionProtocol::$singleton->app = $app;
            }
        }
        return ActionProtocol::$singleton;
    }

    /**
     * ActionProtocol constructor.
     * @param \Slim\Slim $app The slim app.
     */
    private function __construct(\Slim\Slim $app = null) {
        $this->app = $app;
    }

    /**
     * Register a controller.
     * @param $className
     */
    public function register($className) {
        $clazz = new ReflectionClass($className);
        $obj = new $className;
        if ($this->app) {
            foreach ($clazz->getMethods() as $method) {
                $this->app->post('/:user/api/' . $method->getName(),
                    function ($user) use ($obj, $method) {
                        $closure = $method->getClosure($obj);

                        // Get the json object with all information
                        $json = $this->app->request()->getBody();
                        $params = simpleJsonDecode($json);

                        $params = array_map(function(ReflectionParameter $param) use ($params) {
                            $name = $param->getName();
                            $type = $param->getClass();
                            if ($type !== null && !Tools::isScalar($type->name)) {
                                return call_user_func($type->name . '::get', $params[$name]);
                            } else return $params[$name];
                        }, $method->getParameters());

                        $retValue = call_user_func_array($closure, $params);

                        if (is_string($retValue)) {
                            $response = $this->app->response;
                            $response->headers->set('Content-Type', 'text/html; charset=utf-8');
                            $response->status(200);
                            $response->body($retValue);
                        } else if (is_array($retValue)) {
                            $response = $this->app->response;
                            $response->headers->set('Content-Type', 'application/json; charset=utf-8');
                            $response->status(200);
                            $response->body(simpleJsonEncode($retValue));
                        } else if ($retValue instanceof View) {
                            if ($retValue instanceof HtmlView) {
                                $response = $this->app->response;
                                $response->headers->set('Content-Type', 'text/html; charset=utf-8');
                                $response->status(200);
                                $response->body(
                                    $retValue->generate() .
                                    '<style type="text/css">' . $retValue->generateStyle() . '</style>' .
                                    '<script>' . $retValue->generateJS() . '</script>'
                                );
                            }
                        } else {
                            echo "ERROR";
                        }
                    }
                );
            }
        }
    }
}
?>