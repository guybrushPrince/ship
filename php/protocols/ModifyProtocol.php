<?php

namespace ship\protocols;

use Closure;
use Exception;
use ship\interoperability\IShippable;
use ship\interoperability\ShipperRegister;
use ship\interoperability\SLanguage as Language;
use ship\template\Property;
use ship\template\TemplateObject;
use ship\views\DirectView;
use ship\views\View;

/**
 * Protocol ModifyProtocol.
 *
 * <p>One of the most important {@link Protocol}s of ship which defines modifications on a view.</p>
 *
 * @see Protocol
 * @package ship
 * @subpackge php/protocols
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.2.0
 *
 * @protocol
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
     * Constructor.
     * @param Protocol|DirectView|null $parent The parent.
     * @param string $actionImplementation The class name of the implementation of {@link ActionProtocol}.
     * @param string $dialogImplementation The class name of the implementation of {@link DialogProtocol}.
     */
    protected function __construct(Protocol|DirectView|null $parent, string $actionImplementation,
                                   string $dialogImplementation) {
        parent::__construct($parent);
        $this->actionImplementation = $actionImplementation;
        $this->dialogImplementation = $dialogImplementation;
    }

    /**
     * Append modify protocol with another.
     * @param Protocol|TemplateObject|null $protocol The protocol.
     * @return $this
     */
    public function append(Protocol|TemplateObject|null $protocol) : static {
        if ($protocol instanceof Protocol) {
            $protocol->setParent($this->parent);
            $this->protocol = array_merge($this->protocol, $protocol->protocol);
        } else if ($protocol instanceof TemplateObject) {
            $this->protocol[] = [
                "operation" => "virtualDoIt",
                "protocol" => $protocol
            ];
        }
        return $this;
    }

    /**
     * Set a value in the context.
     * @param string $var The name of the variable
     * @param TemplateObject|View|Property|string|float|int|bool|IShippable|null $new The value.
     * @param string|null $scope The scope.
     * @return $this
     */
    public function set(string $var, TemplateObject|View|Property|string|float|int|bool|IShippable|null $new = false,
                        ?string $scope = null) : static {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        } else $new = $this->detectView($new);
        $this->protocol[] = [
            "operation" => "set",
            "key" => $var,
            "value" => $new,
            "scope" => $scope
        ];
        return $this;
    }

    /**
     * Set a cookie in the context.
     * @param string|null $new The value.
     * @return $this
     */
    public function setCookie(?string $new = null): static {
        $this->protocol[] = [
            "operation" => "setCookie",
            "value" => $new
        ];
        return $this;
    }

    /**
     * Get a value in the context.
     * @param string $var The name of the variable / key.
     * @return $this
     */
    public function get(string $var) : static {
        $this->protocol[] = [
            "operation" => "get",
            "key" => $var
        ];
        return $this;
    }

    /**
     * Add the new to the given view.
     * Note: If $new is false, then the view is expected to be a result of an action.
     * @param DirectView|TemplateObject|array|string $view The given view.
     * @param DirectView|TemplateObject|string|null|false $new The new view.
     * @return $this
     */
    public function add(DirectView|TemplateObject|array|string $view,
                        DirectView|TemplateObject|string|null|false $new = false) : static {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
        $p = [
            "operation" => "add",
            "view" => $view
        ];
        if ($new !== ModifyProtocol::$AS_IS) $p["new"] = $new;
        $this->protocol[] = $p;
        return $this;
    }

    /**
     * Add a new view to a given view. The given view may exist. However, if not, there is
     * a fallback view.
     * Note: If $new is false, then the view is expected to be a result of an action.
     * @param DirectView|TemplateObject|array|string $view The given view.
     * @param DirectView|TemplateObject|array|string $fallback The fallback view.
     * @param DirectView|TemplateObject|string|null|false $new The new view.
     * @return $this
     */
    public function addWithFallback(DirectView|TemplateObject|array|string $view,
                                    DirectView|TemplateObject|array|string $fallback,
                                    DirectView|TemplateObject|string|null|false $new = false) : static {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
        $fallback = $this->detectView($fallback);
        $p = [
            "operation" => "addWithFallback",
            "view" => $view,
            "fallback" => $fallback
        ];
        if ($new !== ModifyProtocol::$AS_IS) $p["new"] = $new;
        $this->protocol[] = $p;
        return $this;
    }

    /**
     * Prepends the new to the given view.
     * Note: If $new is false, then the view is expected to be a result of an action.
     * @param DirectView|TemplateObject|array|string $view The given view.
     * @param DirectView|TemplateObject|string|null|false $new The new view.
     * @return $this
     */
    public function prepend(DirectView|TemplateObject|array|string $view,
                            DirectView|TemplateObject|string|null|false $new = false) : static {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
        $p = [
            "operation" => "prepend",
            "view" => $view
        ];
        if ($new !== ModifyProtocol::$AS_IS) $p["new"] = $new;
        $this->protocol[] = $p;
        return $this;
    }

    /**
     * Change the text of a view.
     * @param DirectView|TemplateObject|array|string $view The given view.
     * @param Closure|string|null $text The text.
     * @return $this
     */
    public function text(DirectView|TemplateObject|array|string $view,
                         Closure|string|null $text = null) : static {
        $view = $this->detectView($view);
        $p = [
            "operation" => "text",
            "view" => $view
        ];
        if ($text) {
            $p["value"] = $text instanceof Closure ? $text() : $text;
        }
        $this->protocol[] = $p;
        return $this;
    }

    /**
     * Set the value of an input field or selector.
     * @param DirectView|TemplateObject|array|string $view The given view.
     * @param IShippable|TemplateObject|string|bool|int|float|null $value The value.
     * @return $this
     */
    public function value(DirectView|TemplateObject|array|string $view,
                          IShippable|TemplateObject|string|bool|int|float|null $value = null) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "value",
            "view" => $view,
            "value" => ($value instanceof IShippable ? ShipperRegister::getObjectString($value) : $value)
        ];
        return $this;
    }

    /**
     * Change the size of the view to its contents size.
     * @param DirectView|TemplateObject|string $view The given view.
     * @return $this
     */
    public function resize(DirectView|TemplateObject|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "resize",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Remove all sub views from the view.
     * @param DirectView|TemplateObject|array|string $view The given view.
     * @return $this
     */
    public function makeEmpty(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "empty",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Enable the view(s).
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @return $this
     */
    public function enable(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "enable",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Disable the view(s).
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @return $this
     */
    public function disable(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "disable",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Try to set focus on the element(s).
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @return $this
     */
    public function focus(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "focus",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Change the height of the given view(s) to its/their content's height.
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @return $this
     */
    public function reheight(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "reheight",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Removes all element specific styles of the given view(s).
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @return $this
     */
    public function reset(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "reset",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Add the new after the given view(s).
     * Note: If $new is false, then the view is expected to be a result of an action.
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @param DirectView|TemplateObject|string|null|false $new The new view.
     * @return $this
     */
    public function after(DirectView|TemplateObject|array|string $view,
                          DirectView|TemplateObject|string|null|false $new = null) : static {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "after",
            "view" => $view,
            "new" => $new
        ];
        return $this;
    }

    /**
     * Add the new before the given view(s).
     * Note: If $new is false, then the view is expected to be a result of an action.
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @param DirectView|TemplateObject|string|null|false $new The new view.
     * @return $this
     */
    public function before(DirectView|TemplateObject|array|string $view,
                           DirectView|TemplateObject|string|null|false $new = null) : static {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "before",
            "view" => $view,
            "new" => $new
        ];
        return $this;
    }

    /**
     * Replace the given view(s) with the new view.
     * Note: If $new is false, then the view is expected to be a result of an action.
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @param DirectView|TemplateObject|string|null|false $new The new view.
     * @return $this
     */
    public function replace(DirectView|TemplateObject|array|string $view,
                            DirectView|TemplateObject|string|null|false $new = null) : static {
        if ($new instanceof TemplateObject) {
            $new->setIsView(true);
            $this->addTemplateObject($new);
        }
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "replace",
            "view" => $view,
            "new" => $new
        ];
        return $this;
    }

    /**
     * Clear the view(s).
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @return $this
     */
    public function clear(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "clear",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Adds classes to the given view(s).
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @param TemplateObject|array|string $clazz The class(es) to add.
     * @return $this
     */
    public function addClass(DirectView|TemplateObject|array|string $view,
                             TemplateObject|array|string $clazz) : static {
        if ($clazz instanceof TemplateObject) {
            $this->addTemplateObject($clazz);
        }
        $view = $this->detectView($view);
        if (!is_array($clazz)) $clazz = [$clazz];
        $this->protocol[] = [
            "operation" => "addClasses",
            "view" => $view,
            "classes" => $clazz
        ];
        return $this;
    }

    /**
     * Removes classes from the given view(s).
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @param TemplateObject|array|string $clazz The class(es) to remove.
     * @return $this
     */
    public function removeClass(DirectView|TemplateObject|array|string $view,
                                TemplateObject|array|string $clazz) : static {
        if ($clazz instanceof TemplateObject) {
            $this->addTemplateObject($clazz);
        }
        $view = $this->detectView($view);
        if (!is_array($clazz)) $clazz = [$clazz];
        $this->protocol[] = [
            "operation" => "removeClasses",
            "view" => $view,
            "classes" => $clazz
        ];
        return $this;
    }

    /**
     * Removes classes from all the view(s)'s siblings.
     * @param DirectView|TemplateObject|array|string $view The given view(s).
     * @param TemplateObject|array|string $clazz The class(es) to remove.
     * @param bool|null $onlyChildren If only children shall be searched.
     * @return $this
     */
    public function removeClassSiblings(DirectView|TemplateObject|array|string $view,
                                        TemplateObject|array|string $clazz,
                                        TemplateObject|bool|null $onlyChildren = false) : static {
        if ($clazz instanceof TemplateObject) {
            $this->addTemplateObject($clazz);
        }
        if ($onlyChildren instanceof TemplateObject) {
            $this->addTemplateObject($onlyChildren);
        }
        $view = $this->detectView($view);
        if (!is_array($clazz)) $clazz = [$clazz];
        $this->protocol[] = [
            "operation" => "removeClassesSiblings",
            "view" => $view,
            "classes" => $clazz,
            "children" => $onlyChildren
        ];
        return $this;
    }

    /**
     * Toggle classes from the given (or non-given) view(s).
     * Note: If $view is null, then it will be tried to take the result of an action as view.
     * @param DirectView|TemplateObject|array|string|null $view The given view(s).
     * @param TemplateObject|array|string $clazz The class(es) to toggle.
     * @return $this
     */
    public function toggleClass(DirectView|TemplateObject|array|string|null $view,
                                TemplateObject|array|string  $clazz) : static {
        if ($clazz instanceof TemplateObject) {
            $this->addTemplateObject($clazz);
        }
        $view = $this->detectView($view);
        if (!is_array($clazz)) $clazz = [$clazz];
        $this->protocol[] = [
            "operation" => "toggleClasses",
            "view" => $view,
            "classes" => $clazz
        ];
        return $this;
    }

    /**
     * Delete the view(s).
     * @param DirectView|TemplateObject|array|string $view The view(s).
     * @return $this
     */
    public function delete(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "delete",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Throw an event on the view(s).
     * @param TemplateObject|int $event The event to throw.
     * @param DirectView|TemplateObject|array|string $view The view(s).
     * @param TemplateObject|array|null $data Some additional data.
     * @return $this
     */
    public function throwevent(TemplateObject|int $event, DirectView|TemplateObject|array|string $view,
                               TemplateObject|array|null $data = null) : static {
        if ($event instanceof TemplateObject) {
            $this->addTemplateObject($event);
        }
        if ($data instanceof TemplateObject) {
            $this->addTemplateObject($data);
        }
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "throw",
            "event" => $event,
            "view" => $view,
            "data" => $data
        ];
        return $this;
    }

    /**
     * Throw an event to an object.
     * @param TemplateObject|int $event The event.
     * @param TemplateObject|IShippable|array $object The object(s).
     * @param TemplateObject|string[]|string|null $scopes The scopes.
     * @param TemplateObject|array|null $data Some additional data.
     * @return $this
     */
    public function throwObjectEvent(TemplateObject|int $event, TemplateObject|IShippable|array $object,
                                     TemplateObject|array|string|null $scopes = null,
                                     TemplateObject|array|null $data = null) : static {
        if ($event instanceof TemplateObject) {
            $this->addTemplateObject($event);
        }
        if ($object instanceof TemplateObject) {
            $this->addTemplateObject($object);
        }
        if ($scopes instanceof TemplateObject) {
            $this->addTemplateObject($scopes);
        } else if ($scopes && is_string($scopes)) {
            $scopes = [$scopes];
        }
        if ($data instanceof TemplateObject) {
            $this->addTemplateObject($data);
        }
        $this->protocol[] = [
            "operation" => "throwObjectEvent",
            "event" => $event,
            "object" => $object,
            "scopes" => $scopes,
            "data" => $data
        ];
        return $this;
    }

    /**
     * Unbind events and their objects from the given view(s).
     * @param DirectView|TemplateObject|array|string $view The view(s).
     * @param TemplateObject|int $event The event.
     * @param TemplateObject|IShippable|array $object The object(s) to unbind.
     * @param TemplateObject|string[]|string|null $scopes The scopes.
     * @return $this
     */
    public function unbindObjectEvent(DirectView|TemplateObject|array|string $view, TemplateObject|int $event,
                                      TemplateObject|IShippable|array $object,
                                      TemplateObject|array|string|null $scopes = null) : static {
        $view = $this->detectView($view);
        if ($event instanceof TemplateObject) {
            $this->addTemplateObject($event);
        }
        if ($object instanceof TemplateObject) {
            $this->addTemplateObject($object);
        }
        if ($scopes instanceof TemplateObject) {
            $this->addTemplateObject($scopes);
        } else if ($scopes && is_string($scopes)) {
            $scopes = [$scopes];
        }
        $this->protocol[] = [
            "operation" => "unbindObjectEvent",
            "view" => $view,
            "event" => $event,
            "object" => $object,
            "scopes" => $scopes
        ];
        return $this;
    }

    /**
     * Broadcasts an event.
     * @param TemplateObject|int $event The event.
     * @param TemplateObject|string[]|string|null $scopes The scope(s).
     * @param TemplateObject|array|null $data Some additional data.
     * @return $this
     */
    public function broadcastEvent(TemplateObject|int $event,
                                   TemplateObject|array|string|null $scopes = null,
                                   TemplateObject|array|null $data = null) : static {
        if ($event instanceof TemplateObject) {
            $this->addTemplateObject($event);
        }
        if ($scopes instanceof TemplateObject) {
            $this->addTemplateObject($scopes);
        } else if ($scopes && is_string($scopes)) {
            $scopes = [$scopes];
        }
        if ($data instanceof TemplateObject) {
            $this->addTemplateObject($data);
        }
        $this->protocol[] = [
            "operation" => "broadcastEvent",
            "event" => $event,
            "scopes" => $scopes,
            "data" => $data
        ];
        return $this;
    }

    /**
     * Opens a new browser popup window with a given url and name.
     * @param TemplateObject|string $url The url.
     * @param TemplateObject|string $name The name of the window.
     * @param TemplateObject|null $protocol A protocol what happen if the window is closed.
     * @param DirectView|TemplateObject|array|string|null $view A view from which some content shall be taken.
     * @return $this
     */
    public function openWindow(TemplateObject|string $url, TemplateObject|string $name,
                               ?TemplateObject $protocol = null,
                               DirectView|TemplateObject|array|string|null $view = null) : static {
        if ($url instanceof TemplateObject) {
            $this->addTemplateObject($url);
        }
        if ($name instanceof TemplateObject) {
            $this->addTemplateObject($name);
        }
        if ($protocol instanceof TemplateObject) {
            $this->addTemplateObject($protocol);
        }
        if ($view) {
            $view = $this->detectView($view);
        }
        $this->protocol[] = [
            "operation" => "openWindow",
            "url" => $url,
            "name" => $name,
            "protocol" => $protocol,
            "view" => $view
        ];
        return $this;
    }

    /**
     * Adds an action to the protocol.
     * @param string $controller The controller.
     * @param string $name The function name.
     * @param array $parameters (optional) parameters
     * @param string[] $title (optional) Titles
     * @param bool|int $seaStar (optional) Whether it should use sea star.
     * @return ActionProtocol
     */
    public function action(string $controller, string $name, array $parameters = [],
                           array $title = [
                               Language::ENGLISH => 'New Task',
                               Language::GERMAN => 'Neue Aktion'
                           ], int|bool $seaStar = false) : ActionProtocol {
        $impl = $this->actionImplementation;
        $obj = new $impl($this, $controller, $name, $parameters, $title, $seaStar);
        $this->protocol[] = [
            "operation" => "action",
            "controller" => $controller,
            "method" => $name,
            "protocol" => $obj,
            "parameters" => $parameters,
            "title" => $title,
            "seaStar" => $seaStar
        ];
        return $obj;
    }

    /**
     * Print an alert and do only something if the right option was taken.
     * @param string[] $texts The texts.
     * @param array|null $options The options (optional).
     * @return DialogProtocol
     */
    public function alert(array $texts = [], ?array $options = null) : DialogProtocol {
        $impl = $this->dialogImplementation;
        $obj = new $impl($this, $texts, $options);
        $this->protocol[] = [
            "operation" => "alert",
            "texts" => $texts,
            "options" => $options,
            "protocol" => $obj
        ];
        return $obj;
    }

    /**
     * Throws an event.
     * @param TemplateObject|int $event The event.
     * @param DirectView|TemplateObject|array|string $view The view.
     * @return $this
     */
    public function event(TemplateObject|int $event, DirectView|TemplateObject|array|string $view) : static {
        return $this->throwevent($event, $view);
    }

    /**
     * Sets the position of the view(s) to the mouse position.
     * @param DirectView|TemplateObject|array|string $view The view(s).
     * @return $this
     */
    public function mousePosition(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "mousePosition",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Reloads the page.
     * @return $this
     */
    public function reload() : static {
        $this->protocol[] = [
            "operation" => "reload"
        ];
        return $this;
    }

    /**
     * Print a function code.
     * @param TemplateObject|string $code The code.
     * @return $this
     */
    public function function (TemplateObject|string $code) : static {
        $this->protocol[] = [
            "operation" => "function",
            "code" => $code
        ];
        return $this;
    }

    /**
     * Just stores the view(s) in the context.
     * @param DirectView|TemplateObject|array|string $view The view(s).
     * @return $this
     */
    public function retainView(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "retain",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Scrolls to the top in the view(s).
     * @param DirectView|TemplateObject|array|string $view The view(s).
     * @return $this
     */
    public function scrollTop(DirectView|TemplateObject|array|string $view) : static {
        $view = $this->detectView($view);
        $this->protocol[] = [
            "operation" => "scrollTop",
            "view" => $view
        ];
        return $this;
    }

    /**
     * Call a template.
     * @param string $name The name of the template.
     * @param array $context A mapping from the template objects to real objects.
     * @param string $environment The environment.
     * @return $this
     * @throws Exception
     */
    public function doIt(string $name, array $context = [], string $environment = 'global') : static {
        $this->protocol[] = [
            "operation" => "doIt",
            "name" => $name,
            "context" => $context,
            "environment" => $environment,
            "protocol" => TemplateProtocol::get($name, $environment)
        ];
        return $this;
    }

    /**
     * Call the garbage collector.
     * @return $this
     */
    public function gc() : static {
        $this->protocol[] = ["operation" => 'gc'];
        return $this;
    }

    /**
     * Detects the view if the view is a string or returns the string itself.
     * @param DirectView|TemplateObject|IShippable|array|string|null|false $view The view(s).
     * @return DirectView|TemplateObject|ModifyProtocol|array|string|null|false
     */
    protected function detectView(DirectView|TemplateObject|IShippable|array|string|null|false $view) :
        DirectView|TemplateObject|ModifyProtocol|array|string|null|false {
        if ($view instanceof TemplateObject) {
            $view->setIsView(true);
            $this->addTemplateObject($view);
            return $view;
        }
        if ($view === null) return $this->innerView();
        if (is_array($view)) {
            $view = array_map(function ($v) {
                return $this->detectSingleView($v);
            }, $view);
        } else {
            $view = $this->detectSingleView($view);
        }
        return $view;
    }

    /**
     * Detects a single view.
     * @param DirectView|TemplateObject|IShippable|string|false $view The view.
     * @return DirectView|TemplateObject|IShippable|string|DirectView[]|false
     */
    private function detectSingleView(DirectView|TemplateObject|IShippable|string|false $view) :
        DirectView|TemplateObject|string|IShippable|array|false {
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
    public function parent() : Protocol|DirectView|null {
        if ($this->parent instanceof EventProtocol) {
            return $this->parent->parent();
        } else {
            return $this->parent;
        }
    }
}
?>