<?php

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';
include_once dirname(__FILE__) . '/BooleanProtocol.php';

/**
 * Class IfThenElseProtocol
 * Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class IfThenElseProtocol extends Protocol {

    /**
     * The class of the implementation of the modify abstract class.
     * @var string
     */
    private $modifyImplementation;

    /**
     * The class of the implementation of the condition abstract class.
     * @var string
     */
    private $conditionImplementation;

    /**
     * @inheritDoc
     */
    public function __construct(ModifyProtocol $parent, $modifyImplementation, $conditionImplementation) {
        parent::__construct($parent);
        $this->modifyImplementation = $modifyImplementation;
        $this->conditionImplementation = $conditionImplementation;
    }

    /**
     * Generate a condition branch.
     * @return BooleanProtocol
     */
    public function cond() {
        $this->protocol[] = array(
            "operation" => "then"
        );
        $impl = $this->conditionImplementation;
        return new $impl($this);
    }

    /**
     * Generate a then branch.
     * @return ModifyProtocol
     */
    public function then() {
        $this->protocol[] = array(
            "operation" => "then"
        );
        $impl = $this->modifyImplementation;
        return new $impl($this);
    }

    /**
     * Generate an else branch.
     * @return ModifyProtocol
     */
    public function _else() {
        $this->protocol[] = array(
            "operation" => "else"
        );
        $impl = $this->modifyImplementation;
        return new $impl($this);
    }

}
?>