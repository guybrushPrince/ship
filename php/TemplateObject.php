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
    protected string $name;

    /**
     * Whether the value shall be taken as it is, or not.
     * @var bool
     */
    private bool $asIs;

    /**
     * Whether this template represents a view or not.
     * @var bool
     */
    private bool $isView = false;

    /**
     * TemplateObject constructor.
     * @param string $name The name.
     */
    private function __construct(string $name, bool $asIs = false) {
        $this->name = $name;
        $this->asIs = $asIs;
    }

    /**
     * Get the name.
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Set the name.
     * @param string $name
     */
    public function setName(string $name) : void {
        $this->name = $name;
    }

    /**
     * Does the parameter should be taken as is?
     * @return bool
     */
    public function isAsIs() : bool {
        return $this->asIs;
    }

    /**
     * Set that the parameter should be taken as is.
     * @param bool $asIs
     * @return void
     */
    public function setAsIs(bool $asIs) : void {
        $this->asIs = $asIs;
    }

    /**
     * Does it represent a view?
     * @return bool
     */
    public function isView() : bool {
        return $this->isView;
    }

    /**
     * Set that it is a view.
     * @param bool $isView
     */
    public function setIsView(bool $isView) : void {
        $this->isView = $isView;
    }

    /**
     * Create a new template object.
     * @param string $name The name.
     * @param bool $asIs Whether the value of the parameter should be used as it is, or not.
     * @return TemplateObject
     */
    public static function _new(string $name, bool $asIs = false) : TemplateObject {
        return new TemplateObject($name, $asIs);
    }
}
?>