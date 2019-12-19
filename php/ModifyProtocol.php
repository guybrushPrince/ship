<?php

/**
 * Class ModifyProtocol
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class ModifyProtocol extends Protocol {

    /**
     * The action implementation.
     * @var string
     */
    protected $actionImplementation;

    /**
     * @inheritDoc
     */
    public function __construct($parent, $actionImplementation) {
        parent::__construct($parent);
        $this->actionImplementation = $actionImplementation;
    }


    /**
     * Add the new to the given view.
     * @param DirectView|string $view The given view.
     * @param DirectView|false $new The new view.
     */
    public function add($view, $new = false) {
        if (is_string($view)) $view = $this->innerView()->get($view);
        $this->protocol[] = array(
            "operation" => "add",
            "view"      => $view,
            "new"       => $new
        );
        return $this;
    }

    /**
     * Add the new after the given view.
     * @param DirectView|string $view The given view.
     * @param DirectView|false $new The new view.
     */
    public function after($view, $new = false) {
        if (is_string($view)) $view = $this->innerView()->get($view)["view"];
        $this->protocol[] = array(
            "operation" => "after",
            "view"      => $view,
            "new"       => $new
        );
        return $this;
    }

    /**
     * Add the new before the given view.
     * @param DirectView|string $view The given view.
     * @param DirectView|false $new The new view.
     */
    public function before($view, $new = false) {
        if (is_string($view)) $view = $this->innerView()->get($view)["view"];
        $this->protocol[] = array(
            "operation" => "before",
            "view"      => $view,
            "new"       => $new
        );
        return $this;
    }

    /**
     * Replace the given view with the new view.
     * @param DirectView|string $view The given view.
     * @param DirectView|false $new The new view.
     */
    public function replace($view, $new = false) {
        if (is_string($view)) $view = $this->innerView()->get($view)["view"];
        $this->protocol[] = array(
            "operation" => "replace",
            "view"      => $view,
            "new"       => $new
        );
        return $this;
    }

    /**
     * Add the new before the given view.
     * @param DirectView|string $view The given view.
     */
    public function clear($view) {
        if (is_string($view)) $view = $this->innerView()->get($view)["view"];
        $this->protocol[] = array(
            "operation" => "clear",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Adds classes to a view.
     * @param DirectView|string $view The given view.
     * @param string|array $clazz The class(es) to add.
     */
    public function addClass($view, $clazz) {
        if (is_string($view)) $view = $this->innerView()->get($view)["view"];
        if (!is_array($clazz)) $clazz = array( $clazz );
        $this->protocol[] = array(
            "operation" => "addClasses",
            "view"      => $view,
            "classes"   => $clazz
        );
        return $this;
    }

    /**
     * Removes classes from a view.
     * @param DirectView|string $view The given view.
     * @param string|array $clazz The class(es) to remove.
     */
    public function removeClass($view, $clazz) {
        if (is_string($view)) $view = $this->innerView()->get($view)["view"];
        if (!is_array($clazz)) $clazz = array( $clazz );
        $this->protocol[] = array(
            "operation" => "removeClasses",
            "view"      => $view,
            "classes"   => $clazz
        );
        return $this;
    }

    /**
     * Toggle classes from a view.
     * @param DirectView|string $view The given view.
     * @param string|array $clazz The class(es) to toggle.
     */
    public function toggleClass($view, $clazz) {
        if (is_string($view)) $view = $this->innerView()->get($view)["view"];
        if (!is_array($clazz)) $clazz = array( $clazz );
        $this->protocol[] = array(
            "operation" => "toggleClasses",
            "view"      => $view,
            "classes"   => $clazz
        );
        return $this;
    }

    public function delete() {

    }

    public function modify() {

    }

    /**
     * Adds an action to the protocol.
     * @param string $controller The controller.
     * @param string $name The function name.
     * @param array $parameters (optional) parameters
     * @return $this
     */
    public function action($controller, $name, array $parameters = array()) {
        $impl = $this->actionImplementation;
        $obj = new $impl($this, $controller, $name, $parameters);
        $this->protocol[] = array(
            "operation"  => "action",
            "controller" => $controller,
            "method"     => $name,
            "protocol"   => $obj,
            "parameters" => $parameters
        );
        return $obj;
    }


    /**
     * @inheritDoc
     */
    public function parent() {
        if ($this->parent instanceof EventProtocol) {
            return $this->parent->parent();
        } else {
            return $this->parent;
        }
    }

}
?>