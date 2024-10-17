<?php

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';

/**
 * Class ActionProtocol
 * Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class ActionProtocol extends Protocol {

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
     * @var array
     */
    protected array $title;

    /**
     * Whether it should use sea star or not.
     * @var bool|int
     */
    protected bool|int $seaStar = false;

    /**
     * @inheritDoc
     */
    public function __construct(
        ModifyProtocol $parent,
        $controller,
        $method,
        $implementation,
        $parameters = array(),
        $title = array(),
        $seaStar = false
    ) {
        parent::__construct($parent);
        $this->implementation = $implementation;
        $this->controller = $controller;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->title = $title;
        $this->seaStar = $seaStar;
        $this->templateObjects = array_filter($parameters, function($param) {
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
            "protocol"  => $obj
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
            "protocol"  => $obj
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
            "protocol"  => $obj
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
            "protocol"  => $obj
    );
        return $obj;
    }

    private function findHiddenParameters() {
        if ($this->seaStar !== SeaStarMethod::EVENT) {
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