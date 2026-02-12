<?php

namespace ship\protocols;

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';
include_once dirname(__FILE__) . '/BooleanProtocol.php';

/**
 * Protocol IfThenElseProtocol.
 *
 * <p>A protocol for creating conditional actions.</p>
 *
 * @see Protocol
 * @package ship
 * @subpackge php/protocols
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.2.0
 *
 * @protocol
 */
abstract class IfThenElseProtocol extends Protocol {

    /**
     * The class name of the implementation of {@link ModifyProtocol}.
     * @var string
     */
    private string $modifyImplementation;

    /**
     * The class name of the implementation of {@link BooleanProtocol}.
     * @var string
     */
    private string $conditionImplementation;

    /**
     * Constructor.
     * @param ModifyProtocol $parent The parent protocol.
     * @param string $modifyImplementation The implementation class name of {@link ModifyProtocol}.
     * @param string $conditionImplementation The implementation class name of {@link BooleanProtocol}.
     */
    public function __construct(ModifyProtocol $parent, string $modifyImplementation,
                                string $conditionImplementation) {
        parent::__construct($parent);
        $this->modifyImplementation = $modifyImplementation;
        $this->conditionImplementation = $conditionImplementation;
    }

    /**
     * Generate a condition branch.
     * @return BooleanProtocol
     */
    public function cond() : BooleanProtocol {
        $this->protocol[] = [
            "operation" => "if"
        ];
        $impl = $this->conditionImplementation;
        return new $impl($this);
    }

    /**
     * Generate a then branch.
     * @return ModifyProtocol
     */
    public function then() : ModifyProtocol {
        $this->protocol[] = [
            "operation" => "then"
        ];
        $impl = $this->modifyImplementation;
        return new $impl($this);
    }

    /**
     * Generate an else branch.
     * @return ModifyProtocol
     */
    public function _else() : ModifyProtocol {
        $this->protocol[] = [
            "operation" => "else"
        ];
        $impl = $this->modifyImplementation;
        return new $impl($this);
    }
}
?>