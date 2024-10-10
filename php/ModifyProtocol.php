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
    protected string $actionImplementation;

    /**
     * The dialog implementation.
     * @var string
     */
    protected string $dialogImplementation;

    /**
     * Constant.
     * @var string
     */
    public static string $AS_IS = "AS_IS";

    /**
     * @inheritDoc
     */
    public function __construct($parent, string $actionImplementation, string $dialogImplementation) {
        parent::__construct($parent);
        $this->actionImplementation = $actionImplementation;
        $this->dialogImplementation = $dialogImplementation;
    }

    /**
     * Append modify protocol with another.
     * @param ModifyProtocol|TemplateObject|false $protocol The protocol.
     * @return $this
     */
    public function append($protocol) : ModifyProtocol {
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
    public function set(string $var, $new = false, $scope = false) : ModifyProtocol {
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
    public function setCookie($new = false) : ModifyProtocol {
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
    public function get(string $var) : ModifyProtocol {
        $this->protocol[] = array(
            "operation" => "get",
            "key"       => $var
        );
        return $this;
    }

    /**
     * Add the new to the given view.
     * @param DirectView|string|TemplateObject $view The given view.
     * @param DirectView|false|string|TemplateObject $new The new view.
     * @return $this
     */
    public function add($view, $new = false) : ModifyProtocol {
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
     * Add a new view to an old view. The old view can exist. However, if not, there is
     * a fallback view.
     * @param DirectView|string|TemplateObject $view The given view.
     * @param DirectView|string|TemplateObject $fallback The fallback view.
     * @param DirectView|string|false|TemplateObject $new The new view.
     * @return $this
     */
    public function addWithFallback($view, $fallback, $new = false) : ModifyProtocol {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
        $fallback = $this->detectView($fallback);
        $prot = array(
            "operation" => "addWithFallback",
            "view"      => $view,
            "fallback"  => $fallback
        );
        if ($new !== ModifyProtocol::$AS_IS) $prot["new"] = $new;
        $this->protocol[] = $prot;
        return $this;
    }

    /**
     * Prepends the new to the given view.
     * @param DirectView|string $view The given view.
     * @param DirectView|false|string $new The new view.
     * @return $this
     */
    public function prepend($view, $new = false) : ModifyProtocol {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
        $prot = array(
            "operation" => "prepend",
            "view"      => $view
        );
        if ($new !== ModifyProtocol::$AS_IS) $prot["new"] = $new;
        $this->protocol[] = $prot;
        return $this;
    }

    /**
     * Change the text of a view.
     * @param DirectView|string|TemplateObject $view The given view.
     * @param string|Closure|false $text The text.
     * @return $this
     */
    public function text($view, $text = false) : ModifyProtocol {
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
     * Set the value of an input field or selector.
     * @param DirectView|string|TemplateObject $view The given view.
     * @param CObject|string|bool|int|float|TemplateObject $value The value.
     * @return $this
     */
    public function value($view, $value = false) : ModifyProtocol {
        $view = $this->detectView($view);
        $prot = array(
            "operation" => "value",
            "view"      => $view,
            "value"     => ($value instanceof CObject ? get_class($value) . '<::>' . ActionRegister::convertKey($value->getId()) : $value)
        );
        $this->protocol[] = $prot;
        return $this;
    }

    /**
     * Change size to the contents size.
     * @param DirectView|string|TemplateObject $view The given view.
     * @return $this
     */
    public function resize($view) : ModifyProtocol {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "resize",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Remove all sub views from the view.
     * @param DirectView|string|TemplateObject $view The given view.
     * @return $this
     */
    public function makeEmpty($view) : ModifyProtocol {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "empty",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Disable the views.
     * @param DirectView|string|TemplateObject $view The given view.
     * @return $this
     */
    public function disable($view) : ModifyProtocol {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "disable",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Try to set focus on the element.
     * @param DirectView|string $view The given view.
     * @return $this
     */
    public function focus($view) : ModifyProtocol {
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
     * @return $this
     */
    public function reheight($view) : ModifyProtocol {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "reheight",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Removes all element specific styles.
     * @param DirectView|string|TemplateObject $view The given view.
     * @return $this
     */
    public function reset($view) : ModifyProtocol {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "reset",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Add the new after the given view.
     * @param DirectView|string|TemplateObject $view The given view.
     * @param DirectView|false|TemplateObject $new The new view.
     * @return $this
     */
    public function after($view, $new = false) : ModifyProtocol {
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
     * @param DirectView|string|TemplateObject $view The given view.
     * @param DirectView|false|TemplateObject $new The new view.
     * @return $this
     */
    public function before($view, $new = false) : ModifyProtocol {
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
     * @param DirectView|string|TemplateObject $view The given view.
     * @param DirectView|false|TemplateObject $new The new view.
     * @return $this
     */
    public function replace($view, $new = false) : ModifyProtocol {
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
     * @param DirectView|string|TemplateObject $view The given view.
     * @return $this
     */
    public function clear($view) : ModifyProtocol {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "clear",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Adds classes to a view.
     * @param DirectView|string|string[]|TemplateObject $view The given view.
     * @param string|array|TemplateObject $clazz The class(es) to add.
     * @return $this
     */
    public function addClass($view, $clazz) : ModifyProtocol {
        if ($clazz instanceof TemplateObject) {
            $this->addTemplateObject($clazz);
        }
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
     * @param DirectView|string|TemplateObject $view The given view.
     * @param string|array|TemplateObject $clazz The class(es) to remove.
     * @return $this
     */
    public function removeClass($view, $clazz) : ModifyProtocol {
        if ($clazz instanceof TemplateObject) {
            $this->addTemplateObject($clazz);
        }
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
     * @param DirectView|string|TemplateObject $view The given view.
     * @param string|array $clazz The class(es) to remove.
     * @return $this
     */
    public function removeClassSiblings($view, $clazz) : ModifyProtocol {
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
     * @param DirectView|string|boolean|TemplateObject $view The given view.
     * @param string|array|TemplateObject $clazz The class(es) to toggle.
     * @return $this
     */
    public function toggleClass($view, $clazz) : ModifyProtocol {
        if ($clazz instanceof TemplateObject) {
            $this->addTemplateObject($clazz);
        }
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
     * @param DirectView|string|TemplateObject $view The view.
     * @return $this
     */
    public function delete($view) : ModifyProtocol {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "delete",
            "view"      => $view
        );
        return $this;
    }

    /**
     * Throw an event on the view.
     * @param int|TemplateObject $event The event.
     * @param DirectView|string|TemplateObject|PersistentTrait $view The view.
     * @param array|false|TemplateObject $data Some additional data.
     * @return $this
     */
    public function throwevent($event, $view, $data = false) : ModifyProtocol {
        if ($event instanceof TemplateObject) {
            $this->addTemplateObject($event);
        }
        if ($data instanceof TemplateObject) {
            $this->addTemplateObject($data);
        }
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation" => "throw",
            "event"     => $event,
            "view"      => $view,
            "data"      => $data
        );
        return $this;
    }

    /**
     * Throw an event on the view.
     * @param int|TemplateObject $event The event.
     * @param TemplateObject|PersistentTrait $view The view.
     * @param TemplateObject|string|string[]|null $scopes The scope.
     * @param array|null|TemplateObject $data Some additional data.
     * @return $this
     */
    public function throwObjectEvent($event, $object, $scopes = null, $data = null) : ModifyProtocol {
        if ($event instanceof TemplateObject) {
            $this->addTemplateObject($event);
        }
        if ($object instanceof TemplateObject) {
            $this->addTemplateObject($object);
        }
        if ($scopes instanceof TemplateObject) {
            $this->addTemplateObject($scopes);
        } else if ($scopes && is_string($scopes)) {
            $scopes = [ $scopes ];
        }
        if ($data instanceof TemplateObject) {
            $this->addTemplateObject($data);
        }
        $this->protocol[] = array(
            "operation" => "throwObjectEvent",
            "event"     => $event,
            "object"    => $object,
            "scopes"    => $scopes,
            "data"      => $data
        );
        return $this;
    }

    /**
     * Opens a new browser popup window with a given url and name.
     * @param string|TemplateObject $url The url.
     * @param string|TemplateObject $name The name of the window.
     * @return $this
     */
    public function openWindow($url, $name, $protocol = null) : ModifyProtocol {
        if ($url instanceof TemplateObject) {
            $this->addTemplateObject($url);
        }
        if ($name instanceof TemplateObject) {
            $this->addTemplateObject($name);
        }
        if ($protocol instanceof TemplateObject) {
            $this->addTemplateObject($protocol);
        }
        $this->protocol[] = array(
            "operation" => "openWindow",
            "url"       => $url,
            "name"      => $name,
            "protocol"  => $protocol
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
    public function action(string $controller, string $name, array $parameters = [], array $title = [
        Language::ENGLISH => 'New Task',
        Language::GERMAN => 'Neue Aktion'
    ], $seaStar = false) : ActionProtocol {
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
     * Print an alert and do only something if the right option was taken.
     * @param array $texts The texts.
     * @param array|null $options The options (optional).
     * @return DialogProtocol
     */
    public function alert(array $texts = [], ?array $options = null) : DialogProtocol {
        $impl = $this->dialogImplementation;
        $obj = new $impl($this, $texts, $options);
        $this->protocol[] = [
            "operation"  => "alert",
            "texts"      => $texts,
            "options"    => $options,
            "protocol"   => $obj
        ];
        return $obj;
    }

    /**
     * Throws an event.
     * @param int $event The event.
     * @param DirectView|string|TemplateObject $view The view.
     * @return $this
     */
    public function event(int $event, $view) : ModifyProtocol {
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
     * @param DirectView|string|TemplateObject $view The view.
     * @return $this
     */
    public function mousePosition($view) : ModifyProtocol {
        $view = $this->detectView($view);
        $this->protocol[] = array(
            "operation"  => "mousePosition",
            "view"       => $view
        );
        return $this;
    }

    /**
     * Reloads the page.
     * @return $this
     */
    public function reload() : ModifyProtocol {
        $this->protocol[] = array(
            "operation"  => "reload"
        );
        return $this;
    }

    /**
     * Print a function code.
     * @param string|TemplateObject $code The code.
     * @return $this
     */
    public function function($code) : ModifyProtocol {
        $this->protocol[] = array(
            "operation"  => "function",
            "code" => $code
        );
        return $this;
    }

    /**
     * Call a template.
     * @param string $name The name of the template.
     * @param array $context A mapping from the template objects to real objects.
     * @param string $environment The environment.
     * @return $this
     */
    public function doIt(string $name, array $context = [], string $environment = 'global') : ModifyProtocol {
        $this->protocol[] = array(
            "operation"   => "doIt",
            "name"        => $name,
            "context"     => $context,
            "environment" => $environment,
            "protocol"    => TemplateProtocol::get($name, $environment)
        );
        return $this;
    }

    /**
     * Call the garbage collector.
     * @return $this
     */
    public function gc() : ModifyProtocol {
        $this->protocol[] = ["operation" => 'gc'];
        return $this;
    }

    /**
     * Detects the view if the view is a string or returns the string itself.
     * @param string|array|TemplateObject|PersistentTrait $view The view(s).
     * @return string|array|DirectView|TemplateObject
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
     * @param string|DirectView|TemplateObject|PersistentTrait $view The view.
     * @return string|DirectView|TemplateObject|DirectView[]
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
            } else if (is_object($view) && !($view instanceof View)) {
                $v = $this->innerView()->pick($view);
                if ($v) return $v;
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