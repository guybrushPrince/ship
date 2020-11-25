<?php
/**
 * Class TemplateObject
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class TemplateObject {

    /**
     * The reference to the object.
     * @var string
     */
    protected $name;

    /**
     * Whether this template represents a view or not.
     * @var bool
     */
    private $isView = false;

    /**
     * TemplateObject constructor.
     * @param string $name The name.
     */
    private function __construct($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isView() {
        return $this->isView;
    }

    /**
     * @param bool $isView
     */
    public function setIsView($isView) {
        $this->isView = $isView;
    }

    /**
     * Create a new template object.
     * @param string $name The name.
     * @return TemplateObject
     */
    public static function _new($name) {
        return new TemplateObject($name);
    }
}
?>