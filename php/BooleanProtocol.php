<?php

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';

/**
 * Class BooleanProtocol
 * Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class BooleanProtocol extends Protocol {

    /**
     * The class of the implementation of this abstract class.
     * @var string
     */
    private $implementation;

    /**
     * @inheritDoc
     */
    public function __construct(ModifyProtocol $parent, $implementation) {
        parent::__construct($parent);
        $this->implementation;
    }

    /**
     * Generate an and.
     * @param boolean $negated Negate the whole.
     * @return BooleanProtocol
     */
    public function _and($negated = false) {
        $this->protocol[] = array(
            "operation" => "and",
            "negated"   => $negated
        );
        $impl = $this->implementation;
        return new $impl($this);
    }

    /**
     * Generate an or.
     * @param boolean $negated Negate the whole.
     * @return BooleanProtocol
     */
    public function _or($negated = false) {
        $this->protocol[] = array(
            "operation" => "or",
            "negated"   => $negated
        );
        $impl = $this->implementation;
        return new $impl($this);
    }

    /**
     * Generate an axiom.
     * @param boolean $negated Negate the whole.
     * @return BooleanProtocol
     */
    public function axiom($negated = false) {
        $this->protocol[] = array(
            "operation" => "axiom",
            "negated"   => $negated
        );
        $impl = $this->implementation;
        return new $impl($this);
    }

}
?>