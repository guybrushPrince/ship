<?php

include_once dirname(__FILE__) . '/../../yarn/CoastSerializableFunctions.php';

/**
 * Class ActionRegister
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class ActionRegister {

    /**
     * The API handler
     * @var \Slim\Slim
     */
    private $app = null;

    /**
     * The only instance.
     * @var ActionRegister
     */
    private static $singleton = false;

    /**
     * Create or get the singleton action protocol instance.
     * @param \Slim\Slim $app
     * @return ActionRegister
     */
    public static function instance(\Slim\Slim $app = null) {
        if (!ActionRegister::$singleton) {
            ActionRegister::$singleton = new ActionRegister($app);
        } else {
            if ($app !== null) {
                ActionRegister::$singleton->app = $app;
            }
        }
        return ActionRegister::$singleton;
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
                $fun = function ($user = false, $group = false) use ($obj, $method) {
                    $closure = $method->getClosure($obj);

                    // Get the json object with all information
                    if (preg_match('/@urlParameters(.*)/', $method->getDocComment(), $matches) >= 1) {
                        $params = $this->app->request()->params();
                    } else {
                        $json = $this->app->request()->getBody();
                        $params = simpleJsonDecode($json);
                    }

                    $params = array_map(function(ReflectionParameter $param) use ($params) {
                        $name = $param->getName();
                        $type = $param->getClass();
                        if ($type !== null && !Tools::isScalar($type->name)) {
                            $class = $type->name;
                            $keyBinding = $class::getKeyBinding();
                            return call_user_func($type->name . '::get',
                                ($keyBinding === 'i' ? intval($params[$name]) : $params[$name])
                            );
                        } else return $params[$name];
                    }, $method->getParameters());

                    $retValue = call_user_func_array($closure, $params);

                    if (is_string($retValue)) {
                        if (preg_match('/@CSV(.*)/', $method->getDocComment(), $matches) >= 1) {
                            $response = $this->app->response;
                            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                            $response->headers->set('Content-Disposition', 'attachment;filename=survey_data.csv');
                            $response->status(200);
                            $response->body($retValue);
                        } else {
                            $response = $this->app->response;
                            $response->headers->set('Content-Type', 'text/html; charset=utf-8');
                            $response->status(200);
                            $response->body($retValue);
                        }
                    } else if (is_array($retValue)) {
                        $response = $this->app->response;
                        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
                        $response->status(200);
                        $response->body(simpleJsonEncode($retValue));
                    } else if (is_bool($retValue)) {
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
                                $retValue->generate(!($retValue instanceof ApplicationHtmlView))
                            );
                        }
                    } else if ($retValue instanceof Error) {
                        $response = $this->app->response;
                        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
                        $response->status(404);
                        $response->body(
                            fullJsonEncode($retValue)
                        );
                    } else {
                        echo "ERROR";
                    }
                    if (isset($_SESSION) && array_key_exists('rights', $_SESSION)) {
                        unset($_SESSION["rights"]);
                    }
                };
                $isGet = (preg_match('/@get(.*)/', $method->getDocComment(), $matches) >= 1);
                $without = (preg_match('/@without(.*)/', $method->getDocComment(), $matches) >= 1);
                $isGetParameters = (preg_match('/@urlParameters(.*)/', $method->getDocComment(), $matches) >= 1);
                if ($isGet && $isGet >= 0) {
                    $this->app->get('/api/' . $method->getName(), $fun);
                } else if ($isGetParameters) {
                    $this->app->get('/:user/:group/api/' . $method->getName(), $fun);
                } else if (!$without) {
                    $this->app->post('/:user/:group/api/' . $method->getName(), $fun);
                } else {
                    $this->app->post('/api/' . $method->getName(), $fun);
                }
            }
        }
    }
}
?>