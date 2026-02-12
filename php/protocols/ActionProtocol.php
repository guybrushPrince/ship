<?php

namespace ship\protocols;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ship\template\TemplateObject;
use ship\views\View;

/**
 * Protocol ActionProtocol.
 *
 * <p>A <i>Ship Protocol</i> (see {@link Protocol}), which calls a server <i>method</i> being
 * part of a specific <i>controller</i>.</p>
 *
 * @see Protocol
 * @package ship
 * @subpackge php/protocols
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 *
 * @protocol
 */
abstract class ActionProtocol extends Protocol {

    /**
     * Actions being prohibited for several reasons.
     * @var int[]|string[]
     */
    protected static array $prohibitedActions = [];

    /**
     * The url to the server.
     * @var bool|string
     */
    public static bool|string $serverURL = false;

    /**
     * The class of the implementation of the modify abstract class.
     * @var string
     */
    private string $implementation;

    /**
     * The controller.
     * @var string
     */
    protected string $controller;

    /**
     * The method.
     * @var string
     */
    protected string $method;

    /**
     * An array of parameters.
     * @var array
     */
    protected array $parameters = [];

    /**
     * Title.
     * @var string[]
     */
    protected array $title;

    /**
     * The performed action (not necessary for ordinary usage).
     * @var string|int|null
     */
    protected string|int|null $action = null;

    /**
     * Constructor.
     * @param ModifyProtocol $parent The parent protocol.
     * @param string $controller The controller class name.
     * @param string $method The controller method to invoke.
     * @param string $implementation The specific implementation class name of this protocol.
     * @param array $parameters The parameter set of the action.
     * @param string[]|string $title The title being represented during performing the action.
     * @param string|int|null $action An action.
     * @throws ReflectionException
     */
    public function __construct(ModifyProtocol $parent, string $controller, string $method, string $implementation,
                                array $parameters = [], array|string $title = [], string|int|null $action = null) {
        parent::__construct($parent);
        if (!self::$serverURL) {
            $ini = parse_ini_file(__DIR__ . '/../../../cfg.ini', TRUE);
            $urls = $ini["urls"];

            self::$serverURL = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ?
                    'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/' . $urls['baseUrl'];

            if (!self::$serverURL) self::$serverURL = $urls['fallback'];
        }
        $this->implementation = $implementation;
        $this->controller = $controller;
        $this->method = $method;
        $this->parameters = $parameters;
        if (!is_array($title)) $title = [ View::getLanguage() => $title ];
        $this->title = $title;
        $this->action = $action;
        $this->templateObjects = array_filter($parameters, function ($param) {
            return ($param instanceof TemplateObject);
        });
        $this->findHiddenParameters();
    }

    /**
     * Generate a success branch.
     * @return ModifyProtocol
     */
    public function success() : ModifyProtocol {
        $impl = $this->implementation;
        $obj = new $impl($this);
        $this->protocol[] = array(
            "operation" => "success",
            "protocol" => $obj
        );
        return $obj;
    }

    /**
     * Generates a fail branch if there was an error that shall be handled.
     * @return ModifyProtocol
     */
    public function fail() : ModifyProtocol {
        $impl = $this->implementation;
        $obj = new $impl($this);
        $this->protocol[] = array(
            "operation" => "fail",
            "protocol" => $obj
        );
        return $obj;
    }

    /**
     * Generate an error branch.
     * @return ModifyProtocol
     */
    public function error() : ModifyProtocol {
        $impl = $this->implementation;
        $obj = new $impl($this);
        $this->protocol[] = array(
            "operation" => "error",
            "protocol" => $obj
        );
        return $obj;
    }

    /**
     * Generate an eventually-branch.
     * @return ModifyProtocol
     */
    public function eventually() : ModifyProtocol {
        $impl = $this->implementation;
        $obj = new $impl($this);
        $this->protocol[] = array(
            "operation" => "eventually",
            "protocol" => $obj
        );
        return $obj;
    }

    /**
     * Add prohibited action(s).
     * @param string[]|int[]|string|int $action The action(s) to prohibit.
     * @return void
     */
    public static function addProhibitedAction(array|string|int $action) : void {
        if (!is_array($action)) $actions = [ $action ];
        else $actions = $action;
        foreach ($actions as $action) self::$prohibitedActions[$action] = $action;
    }

    /**
     * Try to find hidden parameters and add them as template objects.
     * @return void
     * @throws ReflectionException
     */
    private function findHiddenParameters() : void {
        if (is_null($this->action) || !array_key_exists($this->action, self::$prohibitedActions)) {
            // Add hidden template objects
            $clazz = new ReflectionClass($this->controller);
            $method = $clazz->getMethod($this->method);
            $hidden = array_filter($method->getParameters(), function (ReflectionParameter $param) {
                return !array_key_exists($param->getName(), $this->parameters);
            });
            // Add a template object for each hidden
            $this->templateObjects = array_merge($this->templateObjects, array_map(function (ReflectionParameter $param) {
                return TemplateObject::_new($param->getName());
            }, $hidden));
        }
    }
}
?>