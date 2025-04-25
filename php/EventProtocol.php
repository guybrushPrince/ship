<?php

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';

/**
 * Class EventProtocol.
 *
 * @package ship
 * @subpackge php
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
abstract class EventProtocol extends Protocol {

    /**
     * The event, when the modify protocol is applied.
     * @var int
     */
    protected int $event;

    /**
     * The class of the implementation of the modify abstract class.
     * @var string
     */
    private string $implementation;

    /**
     * @inheritDoc
     */
    public function __construct(int $event, DirectView $parent, string $implementation) {
        parent::__construct($parent);
        $this->event = $event;
        $this->implementation = $implementation;
    }

    /**
     * Generate a modify branch.
     * @return ModifyProtocol
     */
    public function modify() : ModifyProtocol {
        $this->protocol[] = array(
            "operation" => "modify"
        );
        $impl = $this->implementation;
        return new $impl($this);
    }

}
?>