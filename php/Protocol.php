<?php

/**
 * Class Protocol
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class Protocol {

    /**
     * The protocol.
     * @var array
     */
    protected $protocol = array();

    /**
     * The parent protocol
     * @var Protocol|DirectView
     */
    protected $parent = null;

    /**
     * The children.
     * @var array
     */
    protected $children = array();

    /**
     * An array of used template objects.
     * @var array
     */
    protected $templateObjects = array();

    /**
     * An array of all collected template objects (recursively).
     * @var array|bool
     */
    protected $allTemplateObjects = false;

    /**
     * Protocol constructor.
     * @param Protocol|DirectView $parent
     */
    public function __construct($parent) {
        if ($parent instanceof Protocol) {
            $parent->children[] = $this;
        }
        $this->parent = $parent;
    }

    /**
     * Generates the code for the protocol.
     * @param array|false $context The context.
     * @return string
     */
    public abstract function generate(/*$context = false*/);

    /**
     * Set the parent.
     * @param DirectView|Protocol $parent The parent.
     */
    protected function setParent($parent) {
        $this->parent = $parent;
    }

    /**
     * Get the template objects.
     * @return array
     */
    public function getTemplateObjects() {
        return $this->templateObjects;
    }

    /**
     * Set the template objects.
     * @param array $templateObjects The objects.
     */
    public function setTemplateObjects($templateObjects) {
        $this->templateObjects = $templateObjects;
    }

    /**
     * Add a template object.
     * @param TemplateObject $object The object.
     */
    public function addTemplateObject(TemplateObject $object) {
        $this->templateObjects[] = $object;
    }

    /**
     * Collects the parameter templates with the protocol (and its children).
     * @return array
     */
    public function collectParameters() {
        if (!$this->allTemplateObjects) {
            $this->allTemplateObjects = Tools::unique(array_merge($this->getTemplateObjects(),
                $this->collectParametersInArray($this->protocol)));
        }
        return $this->allTemplateObjects;
    }

    /**
     * Collect the parameters within an array recursively.
     * @param array $parameters
     * @return array
     */
    private function collectParametersInArray(array $parameters) {
        return Tools::flatten(array_filter(array_map(function($param) {
            if ($param instanceof TemplateObject) return $param;
            if (is_array($param)) return $this->collectParametersInArray($param);
            if ($param instanceof Protocol) return $param->collectParameters();
            return false;
        }, $parameters), function($param) {
            return $param ? true : false;
        }));
    }



    // Short cuts.

    /**
     * Return the parent.
     * @return Protocol|DirectView
     */
    public function parent() {
        return $this->parent;
    }

    /**
     * The inner view.
     * @return DirectView
     */
    public function innerView() {
        if ($this->parent instanceof DirectView) {
            return $this->parent;
        } else if ($this->parent === null) {
            return null;
        } else  {
            return $this->parent->innerView();
        }
    }
}
?>