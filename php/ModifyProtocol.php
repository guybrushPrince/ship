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

    public static $AS_IS = "AS_IS";

    /**
     * @inheritDoc
     */
    public function __construct($parent, $actionImplementation) {
        parent::__construct($parent);
        $this->actionImplementation = $actionImplementation;
    }

    /**
     * Append the modifiy protocol with another.
     * @param ModifyProtocol|TemplateObject|false $protocol The protocol.
     */
    public function append($protocol) {
        if ($protocol instanceof Protocol) {
            $protocol->setParent($this->parent);
            $this->protocol = array_merge($this->protocol, $protocol->protocol);
        } else if ($protocol instanceof TemplateObject) {
            $this->protocol[] = array(
                "operation" => "virtualDoIt",
                "protocol"  => $protocol
            );
        }
        return $this;
    }

    /**
     * Set a value in the context.
     * @param string $var The name of the variable
     * @param bool|string|object $new The value.
     * @param string $scope The scope.
     * @return $this
     */
    public function set($var, $new = false, $scope = false) {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        else $new = $this->detectView($new);
        $this->protocol[] = array(
            "operation" => "set",
            "key"       => $var,
            "value"     => $new,
            "scope"     => $scope
        );
        return $this;
    }

    /**
     * Set a cookie in the context.
     * @param bool|string|object $new The value.
     * @return $this
     */
    public function setCookie($new = false) {
        $this->protocol[] = array(
            "operation" => "setCookie",
            "value"     => $new
        );
        return $this;
    }

    /**
     * Get a value in the context.
     * @param string $var The name of the variable
     * @return $this
     */
    public function get($var) {
        $this->protocol[] = array(
            "operation" => "get",
            "key"       => $var
        );
        return $this;
    }

    /**
     * Add the new to the given view.
     * @param DirectView|string $view The given view.
     * @param DirectView|false|string $new The new view.
     */
    public function add($view, $new = false) {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
        $prot = array(
            "operation" => "add",
            "view"      => $view
        );
        if ($new !== ModifyProtocol::$AS_IS) $prot["new"] = $new;
        $this->protocol[] = $prot;
        return $this;
    }

    /**
     * Change the text of a view.
     * @param DirectView|string $view The given view.
     * @param string|Closure|false $text The text.
     */
    public function text($view, $text = false) {
        $view = $this->detectView($view);
        $prot = array(
            "operation" => "text",
            "view"      => $view
        );
        if ($text) {
            $prot["value"] = $text instanceof Closure ? $text() : $text;
        }
        $this->protocol[] = $prot;
        return $this;
    }

    /**
     * Change size to the contents size.
     * @param DirectView|string $view The given view.
     */
    public function resize($view) {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "resize",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Try to set focus on the element.
     * @param DirectView|string $view The given view.
     */
    public function focus($view) {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "focus",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Change the height to the contents size.
     * @param DirectView|string $view The given view.
     */
    public function reheight($view) {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "reheight",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Removes all element specific styles.
     * @param DirectView|string $view The given view.
     */
    public function reset($view) {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "reset",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Add the new after the given view.
     * @param DirectView|string $view The given view.
     * @param DirectView|false $new The new view.
     */
    public function after($view, $new = false) {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
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
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
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
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "replace",
            "view"      => $view,
            "new"       => $new
        );
        return $this;
    }

    /**
     * Clear the view.
     * @param DirectView|string $view The given view.
     */
    public function clear($view) {
        $view = $this->detectView($view);
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
        $view = $this->detectView($view);
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
        $view = $this->detectView($view);
        if (!is_array($clazz)) $clazz = array( $clazz );
        $this->protocol[] = array(
            "operation" => "removeClasses",
            "view"      => $view,
            "classes"   => $clazz
        );
        return $this;
    }

    /**
     * Removes classes from all view siblings.
     * @param DirectView|string $view The given view.
     * @param string|array $clazz The class(es) to remove.
     */
    public function removeClassSiblings($view, $clazz) {
        $view = $this->detectView($view);
        if (!is_array($clazz)) $clazz = array( $clazz );
        $this->protocol[] = array(
            "operation" => "removeClassesSiblings",
            "view"      => $view,
            "classes"   => $clazz
        );
        return $this;
    }

    /**
     * Toggle classes from a view.
     * @param string|array $clazz The class(es) to toggle.
     * @param DirectView|string|boolean $view The given view.
     */
    public function toggleClass($view, $clazz) {
        $view = $this->detectView($view);
        if (!is_array($clazz)) $clazz = array( $clazz );
        $this->protocol[] = array(
            "operation" => "toggleClasses",
            "view"      => $view,
            "classes"   => $clazz
        );
        return $this;
    }

    /**
     * Delete the view.
     * @param DirectView|string $view The view.
     * @return $this
     */
    public function delete($view) {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "delete",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Throw an event on the view.
     * @param integer $event The event.
     * @param DirectView|string $view The view.
     * @return $this
     */
    public function throwevent($event, $view) {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "throw",
            "event"     => $event,
            "view"      => $view
        );
        return $this;
    }

    public function modify() {

    }

    /**
     * Adds an action to the protocol.
     * @param string $controller The controller.
     * @param string $name The function name.
     * @param array $parameters (optional) parameters
     * @param array $title (optional) Titles
     * @param bool|int $seaStar (optional) Whether it should use sea star.
     * @return ActionProtocol
     */
    public function action($controller, $name, array $parameters = array(), array $title = array(
        Language::ENGLISH => 'New Task',
        Language::GERMAN => 'Neue Aktion'
    ), $seaStar = false) {
        $impl = $this->actionImplementation;
        $obj = new $impl($this, $controller, $name, $parameters, $title, $seaStar);
        $this->protocol[] = array(
            "operation"  => "action",
            "controller" => $controller,
            "method"     => $name,
            "protocol"   => $obj,
            "parameters" => $parameters,
            "title"      => $title,
            "seaStar"    => $seaStar
        );
        return $obj;
    }

    /**
     * Throws an event.
     * @param integer $event
     * @param DirectView|string $view The view.
     * @return $this
     */
    public function event($event, $view) {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation"  => "event",
            "event"      => $event,
            "view"      => $view
        );
        return $this;
    }

    /**
     * Sets the position of the view to the mouse position.
     * @param DirectView|string $view The view.
     * @return $this
     */
    public function mousePosition($view) {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation"  => "mousePosition",
            "view"       => $view
        );
        return $this;
    }

    /**
     * Call a template.
     * @param string $name The name of the template.
     * @param array $context A mapping from the template objects to real objects.
     * @param string $environment The environment.
     */
    public function doIt($name, $context = array(), $environment = 'global') {
        $this->protocol[] = array(
            "operation"   => "doIt",
            "entry"       => $this->innerView(),
            "name"        => $name,
            "context"     => $context,
            "environment" => $environment,
            "protocol"    => TemplateProtocol::get($name, $environment)
        );
        return $this;
    }

    /**
     * Detects the view if the view is a string or returns the string itself.
     * @param string|array $view The view(s).
     * @return string|array|DirectView
     */
    protected function detectView($view) {
        if ($view instanceof TemplateObject) {
            $view->setIsView(true);
            $this->addTemplateObject($view);
            return $view;
        }
        if ($view === null) return $this->innerView();
        if (is_array($view)) {
            $view = array_map(function($v) { return $this->detectSingleView($v); }, $view);
        } else {
            $view = $this->detectSingleView($view);
        }
        return $view;
    }

    /**
     * Detects a single view.
     * @param string|DirectView $view The view.
     * @return string|DirectView
     */
    private function detectSingleView($view) {
        if ($view instanceof TemplateObject) {
            $this->addTemplateObject($view);
            return $view;
        }
        if ($this->innerView() !== null) {
            $v = $view;
            if (is_string($view)) {
                $v = $this->innerView()->get($view);
                if ($v !== null) $v = $v["view"];
            }
            if ($v !== null) $view = $v;
        }
        return $view;
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