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
     * @return string
     */
    public abstract function generate();

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
    protected function innerView() {
        if ($this->parent instanceof DirectView) {
            return $this->parent;
        } else {
            return $this->parent->innerView();
        }
    }
}
?>