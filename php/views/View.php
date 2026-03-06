<?php

namespace ship\views;

use Closure;
use Exception;
use ship\access\ViewAccessRightRegister;
use ship\interoperability\IShippable;
use ship\interoperability\ShippingTools;
use ship\interoperability\SLanguage as Language;
use ship\protocols\ModifyProtocol;

include_once __DIR__ . '/DirectView.php';
include_once __DIR__ . '/../controller/ActionRegister.php';

/**
 * Class View.
 *
 * @package ship
 * @subpackge php
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
abstract class View extends DirectView {

    /**
     * Stores implementations.
     * @var string[]
     */
    private static array $basicImplementations = [];

    /**
     * An array of subviews.
     * @var View[]
     */
    protected array $subviews = [];

    /**
     * The current language.
     * @var int
     */
    protected static int $language = Language::GERMAN;

    /**
     * The parent view of this view.
     * @var View|null
     */
    protected ?View $parent = null;

    /**
     * An array of hints (separated by a language as index).
     * @var string[][]
     */
    protected array $hints = [];

    /**
     * The unique id of the view.
     * @var string|null
     */
    protected ?string $uniqueId = null;

    /**
     * Which access-strings must be available to show this view.
     * @var string[]
     */
    protected array $access = [];

    /**
     * Constructor.
     * @param string|null $class The specialization class.
     */
    public function __construct(string|null $class = null) {
        if (!is_null($class)) self::$basicImplementations[$class] = $class;
    }

    /**
     * Generates a unique id.
     * @return string
     */
    protected function generateId() : string {
        return base_convert(rand(), 10, 36);
    }

    /**
     * The view.
     * @param View $view
     * @return View
     */
    public function add(View $view) : View {
        $this->subviews[] = $view;
        $view->parent = $this;
        foreach ($view->references as $name => $info) {
            $this->ref($name, $info["scope"], $info["view"]);
        }
        return $view;
    }

    /**
     * @inheritDoc
     */
    public function access(string|array $access) : static {
        $this->access = (is_array($access) ? $access : [$access]);
        return $this;
    }

    /**
     * Generates the code safe (with access).
     * @param bool $complete Whether with all information (eg. CSS, JS, etc.), or not.
     * @param bool $templates Whether with templates, or not.
     * @return string
     * @throws Exception
     */
    public function generateSafe(bool $complete = false, bool $templates = false) : string {
        if ($this->isSafe()) return $this->generate($complete, $templates);
        else return '';
    }

    /**
     * Checks whether it is safe to give access to this view, or not.
     * @return bool
     * @throws Exception
     */
    protected function isSafe() : bool {
        if (count($this->access) === 0) return true;
        if (isset($_SESSION) &&
            array_key_exists('user', $_SESSION) &&
            array_key_exists('group', $_SESSION) &&
            array_key_exists('role', $_SESSION)) {

            if (!array_key_exists('rights', $_SESSION)) {
                $userRoles = ViewAccessRightRegister::getCurrentUserRoles();
                if (in_array(ViewAccessRightRegister::getSuperUserRole(), $userRoles)) {
                    $_SESSION['rights'] = ViewAccessRightRegister::getAccessRights();
                } else {
                    $_SESSION['rights'] = ViewAccessRightRegister::getCurrentViewAccessRights();
                }
                $roles = array_intersect($userRoles, ViewAccessRightRegister::getSystemRoles());
                if (!ShippingTools::isEmpty($roles)) {
                    $rights = [];
                    foreach ($roles as $role) {
                        $rights = array_merge(
                            $rights,
                            array_intersect($_SESSION['rights'], ViewAccessRightRegister::getSystemRoleRights($role))
                        );
                    }
                    $_SESSION['rights'] = array_unique($rights);
                }
            }
            if (count(array_intersect($_SESSION['rights'], $this->access)) > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate code.
     * @param bool $complete Whether with all information (eg, CSS, JS, ...), or not.
     * @param bool $templates Whether with templates, or not.
     * @param bool $force Whether the templates shall be generated even though they were already generated.
     * @return string
     */
    public abstract function generate(bool $complete = false, bool $templates = false, bool $force = false) : string;

    /**
     * Set the language.
     * @param int $language The language.
     */
    public static function setLanguage(int $language) : void {
        View::$language = $language;
    }

    /**
     * Get the language.
     */
    public static function getLanguage() : int {
        return self::$language;
    }

    /**
     * Get the parent view.
     * @return View|null
     */
    public function getParent() : ?View {
        return $this->parent;
    }

    /**
     * Determine the uppermost parent view.
     * @return View|null
     */
    public function getRoot() : ?View {
        $parent = $this->parent;
        if ($parent === null) return $this;
        while ($parent->getParent() !== null) {
            $parent = $parent->parent();
        }
        return $parent;
    }

    /**
     * Adds a hint.
     * @param int $language The language.
     * @param string $hint The hint.
     * @return $this
     */
    public function addHint(int $language, string $hint) : static {
        if (!array_key_exists($language, $this->hints)) $this->hints[$language] = array();
        $this->hints[$language][] = $hint;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $id, bool $lastChance = false) : ?array {
        $view = parent::get($id);
        if ($view) return $view;
        if ($view === null && $this->parent !== null) {
            return $this->parent->get($id, $lastChance);
        } else {
            if ($view === null && $lastChance && isset($_SESSION) && array_key_exists('references', $_SESSION) &&
                array_key_exists($id, $_SESSION['references'])) {
                $view = new TextView();
                $view->uniqueId = $_SESSION['references'][$id];
                $view = [
                    "view" => ($view),
                    "scope" => false,
                    "name" => $id
                ];
            }
            return $view;
        }
    }

    /**
     * @inheritDoc
     */
    public function ref(string|IShippable $id, bool|string $scope = false, DirectView|bool $view = false) : static {
        if (is_object($id)) $id = self::getObjectId($id);
        parent::ref($id, $scope, $view);
        if (!$this->uniqueId) {
            $this->uniqueId = $this->generateId();
        }
        if (isset($_SESSION) && !$view) {
            if (!array_key_exists('references', $_SESSION)) $_SESSION['references'] = array();
            $_SESSION['references'][$id] = $this->uniqueId;
        }
        if ($this->parent !== null) $this->parent->ref($id, $scope, ($view ? $view : $this));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function pick(IShippable $object) : ?array {
        $view = parent::pick($object);
        if ($view) return $view;
        if ($view === null && $this->parent !== null) {
            return $this->parent->pick($object);
        } else {
            $id = self::getObjectId($object);
            if ($view === null && isset($_SESSION) && array_key_exists('objects', $_SESSION) &&
                array_key_exists($id, $_SESSION['objects'])) {
                return array_map(function ($uniqueId) {
                    $view = new TextView();
                    $view->uniqueId = $uniqueId;
                    return $view;
                }, $_SESSION['objects'][$id]);
            }
            return $view;
        }
    }

    /**
     * @inheritDoc
     */
    public function bind(IShippable $object, array|int|null $event = null, ?ModifyProtocol $protocol = null,
                         ?string $scope = null, ?array $ignoreData = null, ?DirectView $view = null) : static {
        if (!$this->uniqueId) {
            $this->uniqueId = $this->generateId();
        }
        parent::bind($object, $event, $protocol, $scope, $ignoreData, $view);
        if (isset($_SESSION)) {
            if (!array_key_exists('objects', $_SESSION)) $_SESSION['objects'] = [];
            $id = self::getObjectId($object);
            if (!array_key_exists($id, $_SESSION['objects'])) $_SESSION['objects'][$id] = [];
            $_SESSION['objects'][$id][] = $this->uniqueId;
        }
        if ($this->parent !== null && $event === null)
            $this->parent->bind($object, $event, $protocol, $scope, $ignoreData, ($view ? $view : $this));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bindBroadcast(array|int $event, ?ModifyProtocol $protocol = null,
                                  ?string $scope = null, ?array $ignoreData = null, ?DirectView $view = null) : static {
        if (!$this->uniqueId) {
            $this->uniqueId = $this->generateId();
        }
        parent::bindBroadcast($event, $protocol, $scope, $ignoreData, $view);

        if ($this->parent !== null && $event === null)
            $this->parent->bindBroadcast($event, $protocol, $scope, $ignoreData, ($view ? $view : $this));
        return $this;
    }

    /**
     * Internal function: Garbage collector.
     * @param string[] $alive Views that are alive.
     * @return void
     */
    public static function _gc(array $alive) : void {
        if (isset($_SESSION)) {
            if (array_key_exists('references', $_SESSION)) {
                $_SESSION['references'] = array_filter($_SESSION['references'], function ($uniqueId) use ($alive) {
                    return in_array('s' . $uniqueId, $alive);
                });
            }
            if (array_key_exists('objects', $_SESSION)) {
                $_SESSION['objects'] = array_filter($_SESSION['objects'], function (array $uniqueIds) use ($alive) {
                    return !ShippingTools::isEmpty(array_filter($uniqueIds, function ($uI) use ($alive) {
                        return in_array('s' . $uI, $alive);
                    }));
                });
            }
        }
    }


    ///////////////////////////////////////////
    // Shortcuts
    ///////////////////////////////////////////

    /**
     * Get the subviews.
     * @return View[]
     */
    public function views() : array {
        return $this->subviews;
    }

    /**
     * Get the parent view.
     * @return View|null
     */
    public function parent() : ?View {
        return $this->getParent();
    }

    /**
     * @return View|null
     * @see View->getRoot
     */
    public function root() : ?View {
        return $this->getRoot();
    }

    /**
     * @param int $language The language.
     * @param string|null $hint The hint.
     * @return $this|string[]
     * @see View->addHint
     */
    public function hint(int $language, ?string $hint = null) : static|array {
        if ($hint) {
            $this->addHint($language, $hint);
            return $this;
        } else return $this->hints[$language];
    }

    /**
     * A for each loop shortcut with a callback (View, Element, Key).
     * @param array|IShippable $array An array.
     * @param Closure $callback The callback function of type (View, Element, Key)
     * @return $this
     */
    public function each(array|IShippable $array, Closure $callback) : static {
        if (!is_array($array)) $array = [$array];
        foreach ($array as $key => $el) {
            $callback($this, $el, $key);
        }
        return $this;
    }

    /**
     * Append something conditional.
     * @param bool|IShippable|null $cond A condition.
     * @param Closure $thenCallback The then callback (View).
     * @param Closure|null $elseCallback The else callback (View).
     * @return $this
     */
    public function conditional(bool|IShippable|null $cond, Closure $thenCallback,
                                ?Closure $elseCallback = null) : static {
        if ($cond) {
            $thenCallback($this);
        } else {
            if (!$elseCallback) $elseCallback = function (View $view) {
            };
            $elseCallback($this);
        }
        return $this;
    }

    /**
     * Do something with the current view.
     * @param Closure $callback The callback.
     * @return $this
     */
    public function self(Closure $callback) : static {
        $callback($this);
        return $this;
    }

    /**
     * Get the unique id.
     * @return string
     */
    public function id() : string {
        return (!$this->uniqueId ? '' : 's' . $this->uniqueId);
    }

    /**
     * Forces to set the unique id to the given one.
     * @param string $id The id.
     */
    public function forceUniqueIdIntern(string $id) : void {
        $this->uniqueId = $id;
    }

    /**
     * Checks if a given view is an implementation of Ship.
     * @param View|string|null $view The view to check.
     * @return bool
     */
    public static function isImplementation(View|string|null $view) : bool {
        if (is_null($view)) return false;
        if (is_string($view)) {
            return array_key_exists($view, self::$basicImplementations);
        }
        foreach (self::$basicImplementations as $implementation) {
            if ($view instanceof $implementation) {
                return true;
            }
        }
        return false;
    }
}
?>