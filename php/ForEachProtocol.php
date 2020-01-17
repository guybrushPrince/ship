<?php

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';

/**
 * Class ForEachProtocol
 * Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class ForEachProtocol extends Protocol {

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
     * Generate the body.
     * @return ModifyProtocol
     */
    public function body() {
        $this->protocol[] = array(
            "operation" => "body"
        );
        $impl = $this->implementation;
        return new $impl($this);
    }

}
?>