<?php

namespace ship\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ship\exceptions\ShipError;
use ship\interoperability\IShippable;
use ship\interoperability\ShipperRegister;
use ship\interoperability\ShippingTools;
use ship\outputs\Fail;
use ship\outputs\Script;
use ship\protocols\ActionProtocol;
use ship\protocols\Protocol;
use ship\views\View;
use Slim\Routing\RouteCollectorProxy as RouteCollectorProxy;

/**
 * Class ActionRegister.
 *
 * <p>The core of <em>Ship</em>. {@link ShipController} and {@link EventAdder} will be registered on this
 * {@link ActionRegister} to be available as APIs. Everything else is done by magic.</p>
 *
 * @package ship
 * @subpackge php/controller
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.5.0
 */
class ActionRegister {

    /**
     * The API handler
     * @var RouteCollectorProxy|null
     */
    private ?RouteCollectorProxy $app = null;

    /**
     * The only instance.
     * @var ActionRegister|null
     */
    private static ActionRegister|null $singleton = null;

    /**
     * A list of View classes that should not be exported completely.
     * @var string[]
     */
    private array $notComplete = [];

    /**
     * Additional middlewares that are added.
     * @var string[]
     */
    private array $middlewares = [];

    /**
     * A protocol that is executed if the session has a timeout.
     * @var Protocol|null
     */
    private ?Protocol $sessionTimeout = null;

    /**
     * The registered controllers.
     * @var string[]
     */
    private static array $controllers = [];

    /**
     * Create or get the singleton action protocol instance.
     * @param RouteCollectorProxy|null $app The Slim app.
     * @return ActionRegister
     */
    public static function instance(RouteCollectorProxy $app = null) : ActionRegister {
        if (is_null(ActionRegister::$singleton)) {
            ActionRegister::$singleton = new ActionRegister($app);
        } else {
            if ($app !== null) {
                ActionRegister::$singleton->app = $app;
            }
        }
        return ActionRegister::$singleton;
    }

    /**
     * Constructor.
     * @param RouteCollectorProxy|null $app The Slim app.
     */
    private function __construct(RouteCollectorProxy $app = null) {
        $this->app = $app;
    }

    /**
     * Binds the events to the action register.
     * @param string[]|string $events
     */
    public function bindEvents(array|string $events) : void {
        $events = is_array($events) ? $events : [$events];
        foreach ($events as $event) {
            $event::add();
        }
    }

    /**
     * Bind the session timeout protocol.
     * @param Protocol|null $protocol The protocol.
     * @return void
     */
    public function bindSessionTimeout(?Protocol $protocol) : void {
        $this->sessionTimeout = $protocol;
    }

    /**
     * Register a controller.
     * @param string[]|string $classNames The class name of the controller.
     * @throws ReflectionException
     */
    public function register(array|string $classNames) : void {
        if (!is_array($classNames)) $classNames = array($classNames);
        self::$controllers = array_merge(self::$controllers, $classNames);
        foreach ($classNames as $className) {
            $clazz = new ReflectionClass($className);
            $obj = new $className;
            if ($this->app) {
                foreach ($clazz->getMethods() as $method) {
                    if ($this->shallIgnore($method)) continue;
                    // Create a service function that maps parameters and handles the output.
                    $isGet = $this->isGet($method);
                    $without = $this->isWithout($method);
                    $isGetParameters = $this->hasURLParameters($method);
                    $fun = function (Request $request, Response $response, array $args) use ($obj, $method, $without, $isGet) {
                        $user = array_key_exists('user', $args) ? $args['user'] : false;
                        $group = array_key_exists('group', $args) ? $args['group'] : false;
                        $closure = $method->getClosure($obj);

                        if ((!$without && !($isGet && $isGet >= 0)) && !ShipperRegister::hasActiveUserSession()) {
                            if ($this->sessionTimeout) {
                                $response = $this->setResponse($response, $this->sessionTimeout->generate(), 'application/javascript');
                            }
                            return $response;
                        }

                        // Get the json object with all information
                        if ($this->hasURLParameters($method)) {
                            $params = $request->getQueryParams();
                        } else {
                            $json = $request->getBody();
                            $params = ShippingTools::jsonDecode($json);
                        }
                        if (is_null($params) || $params === '') $params = array();
                        // Replace parameters in the method call which are objects.
                        $params = self::mapMethodParameters($method, $params);
                        // Invoke the function.
                        $retValue = call_user_func_array($closure, $params);
                        // Handle the method output
                        $response = $this->handleMethodOutput($response, $method, $retValue);
                        return $response;
                    };

                    // Register the service function as REST service
                    if ($isGet && $isGet >= 0) {
                        $this->app->get('/api/' . $method->getName(), $fun);
                    } else if ($isGetParameters) {
                        $route = $this->app->get('/{user}/{group}/api/' . $method->getName(), $fun);
                        foreach ($this->middlewares as $middleware) $route->add($middleware);
                    } else if (!$without) {
                        $route = $this->app->post('/{user}/{group}/api/' . $method->getName(), $fun);
                        foreach ($this->middlewares as $middleware) $route->add($middleware);
                    } else {
                        $this->app->post('/api/' . $method->getName(), $fun);
                    }
                }
            }
        }
    }

    /**
     * Find the controller for a given method (and scope).
     * @param string $method The method.
     * @param string|null $scope The optional scope.
     * @return object|null
     */
    public static function findControllerFor(string $method, ?string $scope = null) : object|null {
        foreach (self::$controllers as $controllerClass) {
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if ($scope && $controller instanceof ShipController) {
                    if ($scope === $controller->getScope()) {
                        $refClass = new ReflectionClass($controllerClass);
                        if ($refClass->hasMethod($method)) return $controller;
                        else return null;
                    }
                } else {
                    $refClass = new ReflectionClass($controllerClass);
                    if ($refClass->hasMethod($method)) return $controller;
                }
            }
        }
        return null;
    }

    /**
     * Maps parameters to the parameters of the method.
     * @param ReflectionMethod $method The method.
     * @param array $params The parameters.
     * @param array $ignore Classes to ignore.
     * @return array
     */
    public static function mapMethodParameters(ReflectionMethod $method, array $params, array $ignore = []) : array {
        return array_map(function (ReflectionParameter $param) use ($params, $ignore) {
            $name = $param->getName();
            $type = $param->getType();
            if (array_key_exists($name, $params)) {
                $value = $params[$name];

                if ($type !== null) {
                    $types = ($type instanceof ReflectionNamedType ? [$type->getName()] : array_map(function (ReflectionType $t) {
                        return $t->getName();
                    }, $type->getTypes()));

                    if (is_string($value) && in_array('array', $types) && count($types) === 1) {
                        $value = ShippingTools::jsonDecode($value);
                    }

                    $types = array_diff($types, array_merge($ignore, ['null', 'array']));
                    if (!ShippingTools::isEmpty($types)) {
                        $class = $types[array_key_first($types)];

                        if (!ShippingTools::isScalar($class)) {
                            if ($value === null) return null;
                            if (is_array($value)) {
                                if ((!ShippingTools::hasStringKeys($value) || ShippingTools::hasMaskedIntegerKeys($value))) {
                                    return array_map(function ($val) use ($class) {
                                        if (is_array($val) && array_key_exists('IClass', $val)) {
                                            $class = $val['IClass'];
                                        } else if (is_string($val) && strpos($val, '<::>')) {
                                            $class = substr($val, 0, strpos($val, '<::>'));
                                            $val = substr($val, strpos($val, '<::>') + 4);
                                        }
                                        return self::extractObject($class, $val);
                                    }, $value);
                                } else if (array_key_exists('IClass', $value)) {
                                    $class = $value['IClass'];
                                    $value = $value['value'];
                                    return self::extractObject($class, $value);
                                }
                            }
                            if (array_key_exists($name . 'IClass', $params)) {
                                $class = $params[$name . 'IClass'];
                            } else if (strpos($value, '<::>')) {
                                $class = substr($value, 0, strpos($value, '<::>'));
                                $value = substr($value, strpos($value, '<::>') + 4);
                            }
                            return self::extractObject($class, $value);
                        }
                    }
                }
                if (is_array($value)) {
                    if (!ShippingTools::isEmpty($value)) {
                        $first = $value[array_key_first($value)];
                        if (is_array($first)) {
                            if (array_key_exists('IClass', $first)) {
                                return array_map(function (array $content) {
                                    $class = $content['IClass'];
                                    $value = $content['value'];
                                    return self::extractObject($class, $value);
                                }, $value);
                            }
                        } else if (is_string($first) && strpos($first, '<::>')) {
                            return array_map(function ($val) {
                                $class = substr($val, 0, strpos($val, '<::>'));
                                $val = substr($val, strpos($val, '<::>') + 4);
                                return self::extractObject($class, $val);
                            }, $value);
                        }
                    }
                    return self::unpreserveArrayKeys($value);
                } else return $value;
            } else return null;
        }, $method->getParameters());
    }

    /**
     * Tries to extract an object from the database.
     * @param string $class The class.
     * @param array|int|string $value The value.
     * @return IShippable|View|null
     */
    private static function extractObject(string $class, array|int|string $value) : IShippable|View|null {
        if (View::isImplementation($class)) {
            $view = new $class();
            $view->forceUniqueIdIntern(substr($value, 2));
            return $view;
        }
        return ShipperRegister::getObject($class, $value);
    }

    /**
     * Handle the output of the method.
     * @param Response $response The response.
     * @param ReflectionMethod $method The method.
     * @param Fail|bool|array|string|ShipError|Script|View $retValue The return value of the call.
     * @return Response
     */
    private function handleMethodOutput(Response $response, ReflectionMethod $method,
                                        View|Fail|Script|ShipError|bool|array|string|null $retValue) : Response {
        if ($retValue instanceof Fail) {
            $content = $retValue->getContent();
            if (is_array($content)) {
                $isViewsArray = count(array_filter($content, function ($val) {
                        return !($val instanceof View);
                    })) === 0;
                if ($isViewsArray) $content = $this->serializeView($content, $this->isPure($method));
            } else if ($content instanceof View) $content = $this->serializeView($content, $this->isPure($method));
            $retValue = [
                Fail::INTERN_FAIL => Fail::INTERN_FAIL,
                "content" => $content
            ];
        }
        if (is_string($retValue)) {
            if ($this->isCSV($method)) {
                $response = $this->setResponse($response, $retValue, 'text/csv');
                $response = $response->withHeader('Content-Disposition', 'attachment;filename=survey_data.csv');
            } else if ($this->isPDF($method)) {
                $response = $this->setResponse($response, null, 'application/pdf');
                $response = $response->withHeader('Pragma', "public")
                    ->withHeader('Content-Transfer-Encoding', 'binary')
                    ->withHeader('Content-Length', filesize($retValue))
                    ->withHeader('Content-Description', 'File Transfer')
                    ->withHeader('Content-Type', 'application/octet-stream')
                    ->withHeader('Content-Type', 'application/force-download')
                    ->withHeader('Content-Disposition', 'attachment;filename=survey.pdf')
                    ->withHeader('Expires', '0')
                    ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
                readfile($retValue);
            } else if ($this->isZIP($method)) {
                $response = $this->setResponse($response, ActionProtocol::$serverURL . $retValue, 'text/html');
            } else $response = $this->setResponse($response, $retValue, 'text/html');
        } else if (is_bool($retValue)) {
            $response = $this->setResponse($response, ShippingTools::jsonEncode($retValue));
        } else if (is_array($retValue)) {
            $isViewsArray = count(array_filter($retValue, function ($val) {
                    return !($val instanceof View);
                })) === 0;
            if ($isViewsArray) $response = $this->setResponse($response, $this->serializeView($retValue, $this->isPure($method)) . ' ', 'text/html');
            else $response = $this->setResponse($response, ShippingTools::jsonEncode($retValue));
        } else if ($retValue instanceof View) {
            $response = $this->setResponse($response, $this->serializeView($retValue, $this->isPure($method)) . ' ', 'text/html');
        } else if (is_null($retValue)) {
            $response = $this->setResponse($response, '', 'text/html');
        } else if ($retValue instanceof ShipError) {
            $response = $this->setResponse($response, ShippingTools::jsonEncode($retValue->serialize()), 'application/json', 404);
        } else if ($retValue instanceof Script) {
            $response = $this->setResponse($response, $retValue->getCode() . ' ', 'application/javascript');
        } else {
            echo "ERROR";
        }
        if (isset($_SESSION) && array_key_exists('rights', $_SESSION)) {
            unset($_SESSION["rights"]);
        }
        return $response;
    }

    /**
     * A single or multiple views.
     * @param View|View[] $views The view(s)
     * @param bool $pure If the pure view without JS, etc. shall be returned.
     * @return string
     */
    private function serializeView(array|View $views, bool $pure = false) : string {
        $views = is_array($views) ? $views : array($views);
        return implode(array_map(function (View $view) use ($pure) {
            if (View::isImplementation($view)) {
                return $view->generate(!$pure && $this->shouldParsedCompletely($view), !$pure);
            } else return '';
        }, $views));
    }

    /**
     * Add a middleware or a set of middlewares.
     * @param string[]|string $middleware The middleware(s) to add.
     * @return void
     */
    public function addMiddleware(array|string $middleware) : void {
        if (!is_array($middleware)) $middlewares = [ $middleware ];
        else $middlewares = $middleware;
        foreach ($middlewares as $middleware) {
            if (class_exists($middleware)) $this->middlewares[] = $middleware;
        }
    }

    /**
     * Adds a view class that should not be parsed.
     * @param string|string[] $clazz The class.
     */
    public function parseNotCompletely(string|array $clazz) : void {
        if (!is_array($clazz)) $this->notComplete[] = $clazz;
        else $this->notComplete = array_merge($this->notComplete, $clazz);
    }

    /**
     * Should the current class of the view be parsed completely.
     * @param View $view The view.
     * @return boolean
     */
    private function shouldParsedCompletely(View $view) : bool {
        return count(array_filter($this->notComplete, function ($clazz) use ($view) {
                return $view instanceof $clazz;
            })) === 0;
    }

    /**
     * Unpreserve the keys of a preserved array.
     * @param array $a The array.
     * @return array
     */
    private static function unpreserveArrayKeys(array $a) : array {
        return array_reduce(array_keys($a), function (array $c, $k) use ($a) {
            $v = (is_array($a[$k]) ? self::unpreserveArrayKeys($a[$k]) : $a[$k]);
            $k = str_replace('__WS__', ' ', $k);
            if (ShippingTools::startsWith('i', $k)) {
                $n = substr($k, 1);
                if (intval($n) . '' === $n) $c[intval($n)] = $v;
                else $c[$k] = $v;
            } else $c[$k] = $v;
            return $c;
        }, []);
    }

    /**
     * Set the response.
     * @param Response $response The response.
     * @param string|null $body The content.
     * @param string $contentType The content type.
     * @param int $status The status of the response.
     * @return Response
     */
    private function setResponse(Response $response, ?string $body, string $contentType = 'application/json',
                                 int $status = 200) : Response {
        $response = $response->withHeader('Content-Type', $contentType . '; charset=utf-8')->withStatus($status);
        if ($body !== null) $response->getBody()->write($body);
        return $response;
    }

    private function shallIgnore(ReflectionMethod $method) : bool {
        return $this->isArbitrary($method, 'ignore');
    }

    private function isPure(ReflectionMethod $method) : bool {
        return $this->isArbitrary($method, 'pure');
    }

    private function isGet(ReflectionMethod $method) : bool {
        return $this->isArbitrary($method, 'get');
    }

    private function isWithout(ReflectionMethod $method) : bool {
        return $this->isArbitrary($method, 'without');
    }

    private function hasURLParameters(ReflectionMethod $method) : bool {
        return $this->isArbitrary($method, 'urlParameters');
    }

    private function isPDF(ReflectionMethod $method) : bool {
        return $this->isArbitrary($method, 'PDF');
    }

    private function isCSV(ReflectionMethod $method) : bool {
        return $this->isArbitrary($method, 'CSV');
    }

    private function isZIP(ReflectionMethod $method) : bool {
        return $this->isArbitrary($method, 'ZIP');
    }

    private function isArbitrary(ReflectionMethod $method, string $arbitrary) : bool {
        return (preg_match('/@' . $arbitrary . '(.*)/', $method->getDocComment(), $matches) >= 1);
    }
}
?>