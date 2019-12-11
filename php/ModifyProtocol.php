<?php

/**
 * Class ModifyProtocol
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class ModifyProtocol {

    /**
     * The view this protocol belongs to
     * @var DirectView
     */
    protected $view;

    /**
     * The event, when the modify protocol is applied.
     * @var integer
     */
    protected $event;

    /**
     * The protocol.
     * @var array
     */
    protected $protocol = array();

    /**
     * ModifyProtocol constructor.
     * @param integer $event The event.
     * @param DirectView $view The view.
     */
    public function __construct($event, DirectView $view) {
        $this->event = $event;
        $this->view = $view;
    }

    /**
     * Add the new to the given view.
     * @param DirectView|string $view The given view.
     * @param DirectView|false $new The new view.
     */
    public function add($view, $new = false) {
        if (is_string($view)) $view = $this->view->get($view);
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
        $viewT = $view;
        if (is_string($view)) $view = $this->view->get($view)["view"];
        if ($view === null) {
            var_dump($this->view->get($viewT));
            var_dump($this->view);
        }
        $this->protocol[] = array(
            "operation" => "after",
            "view"      => $view,
            "new"       => $new
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
     * @return $this
     */
    public function action($controller, $name) {
        $this->protocol[] = array(
            "operation"  => "action",
            "controller" => $controller,
            "method"     => $name
        );
        return $this;
    }

    /**
     * Generates the code for the protocol.
     * @return string
     */
    public abstract function generate();

    // Short cuts.

    /**
     * Return the view.
     * @return DirectView
     */
    public function parent() {
        return $this->view;
    }

}
?>