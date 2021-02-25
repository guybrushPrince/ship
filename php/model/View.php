<?php

include_once dirname(__FILE__) . '/../DirectView.php';
include_once dirname(__FILE__) . '/../ActionRegister.php';
include_once dirname(__FILE__) . '/access/ViewAccessRight.php';

/**
 * Class View
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class View extends DirectView {

    /**
     * An array of subviews.
     * @var View[]
     */
    protected array $subviews = array();

    /**
     * The current language.
     * @var integer
     */
    protected static int $language = Language::GERMAN;

    /**
     * The parent view of this view.
     * @var ?View
     */
    protected ?View $parent = null;

    /**
     * An array of hints.
     * @var array
     */
    protected array $hints = array();

    /**
     * The unique id of the view.
     * @var string|boolean
     */
    protected $uniqueId = false;

    /**
     * Which access must be granted.
     * @var array
     */
    protected array $access = array();

    /**
     * View constructor.
     */
    public function __construct() { }

    /**
     * Generates a unique id.
     * @return bool|string
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
        return $view;
    }

    /**
     * @inheritDoc
     */
    public function access($access) : View {
        $this->access = (is_array($access) ? $access : array( $access ));
        return $this;
    }

    /**
     * Generates the code safe (with access).
     * @param boolean $complete Whether with all information or not.
     * @param boolean $templates Whether with templates or not.
     * @return string
     */
    public function generateSafe($complete = false, $templates = false) : string {
        if ($this->isSafe()) return $this->generate($complete, $templates);
        else return '';
    }

    /**
     * Checks whether it is safe or not.
     * @return bool
     */
    protected function isSafe() : bool {
        if (count($this->access) === 0) return true;
        if (isset($_SESSION) &&
            array_key_exists('group', $_SESSION) &&
            array_key_exists('role', $_SESSION)) {

            if (!array_key_exists('rights', $_SESSION)) {
                $rights = ViewAccessRight::get($_SESSION['group'] . ':' . $_SESSION['role']);
                if ($rights && $rights instanceof ViewAccessRight) {
                    $_SESSION['rights'] = $rights->getRightsList();
                } else {
                    $_SESSION['rights'] = array();
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
     * @param boolean $complete Whether with all information or not.
     * @param boolean $templates Whether with templates or not.
     * @return string
     */
    public abstract function generate($complete = false, $templates = false) : string;

    /**
     * Set the language.
     * @param $language
     */
    public static function setLanguage($language) {
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
     * @return ?View
     */
    public function getParent() : ?View {
        return $this->parent;
    }

    /**
     * Determine the uppermost parent view.
     * @return ?View
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
     * Add an hint.
     * @param int $language The language.
     * @param string $hint The hint.
     */
    public function addHint(int $language, string $hint) {
        if (!array_key_exists($language, $this->hints)) $this->hints[$language] = array();
        $this->hints[$language][] = $hint;
    }

    /**
     * @inheritDoc
     */
    public function get($id, $lastChance = false) {
        $view = parent::get($id);
        if ($view) return $view;
        if ($view === null && $this->parent !== null) {
            return $this->parent->get($id, $lastChance);
        } else {
            if ($view === null && $lastChance && isset($_SESSION) && array_key_exists('references', $_SESSION) &&
                array_key_exists($id, $_SESSION['references'])) {
                $view = new TextView();
                $view->uniqueId = $_SESSION['references'][$id];
                $view = array(
                    "view"  => ($view),
                    "scope" => false
                );
            }
            return $view;
        }
    }

    /**
     * @inheritDoc
     */
    public function ref($id, $scope = false, $view = false) {
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



    // Short cuts

    /**
     * Get the subviews.
     * @return View[]
     */
    public function views() : array {
        return $this->subviews;
    }

    /**
     * Get the parent view.
     * @return ?View
     */
    public function parent() : ?View {
        return $this->getParent();
    }

    /**
     * @see View->getRoot
     * @return ?View
     */
    public function root() : ?View {
        return $this->getRoot();
    }

    /**
     * @see View->addHint
     * @param int $language The language.
     * @param string|false $hint The hint.
     * @return $this|string[]
     */
    public function hint(int $language, $hint = false) {
        if ($hint) {
            $this->addHint($language, $hint);
            return $this;
        } else return $this->hints[$language];
    }

    /**
     * An for each loop.
     * @param array|object $array An array.
     * @param Closure $callback The callback function of type (View, Element, Key)
     * @return $this
     */
    public function each($array, Closure $callback) : View {
        if (!is_array($array)) $array = array( $array );
        foreach ($array as $key => $el) {
            $callback($this, $el, $key);
        }
        return $this;
    }

    /**
     * Append something conditional.
     * @param bool|object $cond A condition.
     * @param Closure $thenCallback The then callback (View).
     * @param Closure|false $elseCallback The else callback (View).
     * @return $this
     */
    public function conditional($cond, Closure $thenCallback, $elseCallback = false) : View {
        if ($cond) {
            $thenCallback($this);
        } else {
            if (!$elseCallback) $elseCallback = function(View $view) {};
            $elseCallback($this);
        }
        return $this;
    }

    /**
     * Do something with the current view.
     * @param Closure $callback The callback.
     * @return $this
     */
    public function self(Closure $callback): View {
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

}