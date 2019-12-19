<?php

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';

/**
 * Class EventProtocol
 * Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class EventProtocol extends Protocol {

    /**
     * The event, when the modify protocol is applied.
     * @var integer
     */
    protected $event;

    /**
     * The class of the implementation of the modify abstract class.
     * @var string
     */
    private $implementation;

    /**
     * @inheritDoc
     */
    public function __construct($event, DirectView $parent, $implementation) {
        parent::__construct($parent);
        $this->event = $event;
        $this->implementation = $implementation;
    }

    /**
     * Generate a modify branch.
     * @return ModifyProtocol
     */
    public function modify() {
        $this->protocol[] = array(
            "operation" => "modify"
        );
        $impl = $this->implementation;
        return new $impl($this);
    }

}
?>