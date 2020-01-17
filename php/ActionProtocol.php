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
    private $implementation;

    /**
     * The controller.
     * @var string
     */
    protected $controller;

    /**
     * The method.
     * @var string
     */
    protected $method;

    /**
     * An array of parameters.
     * @var array
     */
    protected $parameters = array();

    /**
     * Title.
     * @var array
     */
    protected $title;

    /**
     * @inheritDoc
     */
    public function __construct(
        ModifyProtocol $parent,
        $controller,
        $method,
        $implementation,
        $parameters = array(),
        $title = array()
    ) {
        parent::__construct($parent);
        $this->implementation = $implementation;
        $this->controller = $controller;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->title = $title;
    }

    /**
     * Generate a success branch.
     * @return ModifyProtocol
     */
    public function success() {
        $impl = $this->implementation;
        $obj = new $impl($this);
        $this->protocol[] = array(
            "operation" => "success",
            "protocol"  => $obj
        );
        return $obj;
    }

    /**
     * Generate an error branch.
     * @return ModifyProtocol
     */
    public function error() {
        $impl = $this->implementation;
        $obj = new $impl($this);
        $this->protocol[] = array(
            "operation" => "error",
            "protocol"  => $obj
    );
        return $obj;
    }

    /**
     * Generate a eventually branch.
     * @return ModifyProtocol
     */
    public function eventually() {
        $impl = $this->implementation;
        $obj = new $impl($this);
        $this->protocol[] = array(
            "operation" => "eventually",
            "protocol"  => $obj
    );
        return $obj;
    }

}
?>