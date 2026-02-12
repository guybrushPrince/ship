<?php

namespace ship\outputs;

/**
 * Class Fail.
 *
 * <p>A special return type for API methods to signal a failing of an operation.</p>
 *
 * @package ship
 * @subpackage php/outputs
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.1.0
 */
class Fail {

    /**
     * A constant identifying a fail in an array.
     */
    public const INTERN_FAIL = '__intern_fail';

    /**
     * Arbitrary content.
     * @var mixed
     */
    private mixed $content;

    /**
     * Constructor.
     * @param mixed $content Arbitrary content.
     */
    public function __construct(mixed $content) {
        $this->content = $content;
    }

    /**
     * The content.
     * @return mixed
     */
    public function getContent() : mixed {
        return $this->content;
    }
}
?>