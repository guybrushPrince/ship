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
     * A list of View classes that should not be exported completely.
     * @var array
     */
    private $notComplete = array();

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
     * @param array|string $className The class name of the controller.
     */
    public function register($classNames) {
        if (!is_array($classNames)) $classNames = array($classNames);
        foreach ($classNames as $className) {
            $clazz = new ReflectionClass($className);
            $obj = new $className;
            if ($this->app) {
                foreach ($clazz->getMethods() as $method) {
                    // Create a service function that maps parameters and handles the output.
                    $fun = function ($user = false, $group = false) use ($obj, $method) {
                        $closure = $method->getClosure($obj);

                        // Get the json object with all information
                        if ($this->hasURLParameters($method)) {
                            $params = $this->app->request()->params();
                        } else {
                            $json = $this->app->request()->getBody();
                            $params = simpleJsonDecode($json);
                        }
                        if (is_null($params) || $params === '') $params = array();
                        DebugLogger::instance()->debug('Call', $method->getName(), 'with params', $params);
                        // Replace parameters in the method call which are objects.
                        $params = $this->mapMethodParameters($method, $params);
                        // Invoke the function.
                        $retValue = call_user_func_array($closure, $params);
                        // Handle the method output
                        $this->handleMethodOutput($method, $retValue);
                    };

                    // Register the service function as REST service
                    $isGet = $this->isGet($method);
                    $without = $this->isWithout($method);
                    $isGetParameters = $this->hasURLParameters($method);
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

    /**
     * Maps parameters to the parameters of the method.
     * @param ReflectionMethod $method The method.
     * @param array $params The parameters.
     * @return array
     */
    private function mapMethodParameters(ReflectionMethod $method, array $params) {
        return array_map(function(ReflectionParameter $param) use ($params) {
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
    }

    /**
     * Handle the output of the method.
     * @param ReflectionMethod $method
     * @param $retValue
     */
    private function handleMethodOutput(ReflectionMethod $method, $retValue) {
        if (is_string($retValue) || is_string($retValue)) {
            if ($this->isCSV($method)) {
                $response = $this->setResponse($retValue, 'text/csv');
                $response->headers->set('Content-Disposition', 'attachment;filename=survey_data.csv');
            } else if ($this->isPDF($method)) {
                $response = $this->setResponse(null, 'application/pdf');
                $response->headers->set('Pragma', "public");
                $response->headers->set('Content-Transfer-Encoding', 'binary');
                $response->headers->set('Content-Length', filesize($retValue));
                $response->headers->set('Content-Description', 'File Transfer');
                $response->headers->set('Content-Type', 'application/octet-stream');
                $response->headers->set('Content-Type', 'application/force-download');
                $response->headers->set('Content-Disposition', 'attachment;filename=survey.pdf');
                $response->headers->set('Expires', '0');
                $response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
                readfile($retValue);
            } else $this->setResponse($retValue, 'text/html');
        } else if (is_bool($retValue)) {
            $this->setResponse(simpleJsonEncode($retValue));
        } else if (is_array($retValue)) {
            $isViewsArray = count(array_filter($retValue, function($val) {
                return !($val instanceof View);
            })) === 0;
            if ($isViewsArray) $this->setResponse($this->serializeView($retValue),  'text/html');
            else $this->setResponse(simpleJsonEncode($retValue));
        } else if ($retValue instanceof View) {
            $this->setResponse($this->serializeView($retValue),  'text/html');
        } else if ($retValue instanceof Error) {
            $this->setResponse(fullJsonEncode($retValue), 'application/json', 404);
        } else {
            echo "ERROR";
        }
        if (isset($_SESSION) && array_key_exists('rights', $_SESSION)) {
            unset($_SESSION["rights"]);
        }
    }

    /**
     * A single or multiple views.
     * @param View|array $views The view(s)
     * @return string
     */
    private function serializeView($views) {
        $views = is_array($views) ? $views : array($views);
        return implode(array_map(function(View $view) {
            if ($view instanceof HtmlView) {
                return $view->generate($this->shouldParsedCompletely($view));
            } else return '';
        }, $views));
    }

    /**
     * Adds a view class that should not be parsed.
     * @param string $clazz The class.
     */
    public function parseNotCompletely($clazz) {
        $this->notComplete[] = $clazz;
    }

    /**
     * Should the current class of the view be parsed completely.
     * @param View $view The view.
     * @return boolean
     */
    private function shouldParsedCompletely(View $view) {
        return count(array_filter($this->notComplete, function($clazz) use ($view) {
            return $view instanceof $clazz;
        })) === 0;
    }

    /**
     * Set the response.
     * @param string $body The content.
     * @param string $contentType The content type.
     * @param int $status The status of the response.
     * @return \Slim\Http\Response
     */
    private function setResponse($body, $contentType = 'application/json', $status = 200) {
        $response = $this->app->response;
        $response->headers->set('Content-Type', $contentType . '; charset=utf-8');
        $response->status($status);
        if ($body !== null) $response->body($body);
        return $response;
    }

    private function isGet(ReflectionMethod $method) {
        return $this->isArbitrary($method, 'get');
    }

    private function isWithout(ReflectionMethod $method) {
        return $this->isArbitrary($method, 'without');
    }

    private function hasURLParameters(ReflectionMethod $method) {
        return $this->isArbitrary($method, 'urlParameters');
    }

    private function isPDF(ReflectionMethod $method) {
        return $this->isArbitrary($method, 'PDF');
    }

    private function isCSV(ReflectionMethod $method) {
        return $this->isArbitrary($method, 'CSV');
    }

    private function isArbitrary(ReflectionMethod $method, $arbitrary) {
        return (preg_match('/@' . $arbitrary . '(.*)/', $method->getDocComment(), $matches) >= 1);
    }
}
?>