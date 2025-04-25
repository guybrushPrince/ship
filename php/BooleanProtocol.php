<?php

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';

/**
 * Class BooleanProtocol.
 *
 * @package ship
 * @subpackge php
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
abstract class BooleanProtocol extends Protocol {

    /**
     * The class of the implementation of this abstract class.
     * @var string
     */
    private string $implementation;

    /**
     * Constructor.
     * @param ModifyProtocol $parent The parent protocol.
     * @param string $implementation The specific implementation class name of this protocol.
     */
    public function __construct(ModifyProtocol $parent, string $implementation) {
        parent::__construct($parent);
        $this->implementation = $implementation;
    }

    /**
     * Generate an and.
     * @param bool $negated Negate the whole.
     * @return BooleanProtocol
     */
    public function _and(bool$negated = false) : BooleanProtocol {
        $this->protocol[] = array(
            "operation" => "and",
            "negated"   => $negated
        );
        $impl = $this->implementation;
        return new $impl($this);
    }

    /**
     * Generate an or.
     * @param bool $negated Negate the whole.
     * @return BooleanProtocol
     */
    public function _or(bool $negated = false) : BooleanProtocol {
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
    public function axiom(bool $negated = false) : BooleanProtocol {
        $this->protocol[] = array(
            "operation" => "axiom",
            "negated"   => $negated
        );
        $impl = $this->implementation;
        return new $impl($this);
    }

}
?>