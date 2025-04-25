<?php

/**
 * Class Context.
 * An implementation-unspecific version of a context consisting of fields for representing
 * context variables (fields).
 *
 * @package ship
 * @subpackge php
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
abstract class Context {

    /**
     * A static array with contexts indexed by field names (string).
     * @var Context[]
     */
    private static array $contexts = [];

    /**
     * THe field of the context.
     * @var string
     */
    protected string $field;

    /**
     * Context constructor.
     * @param string $field The name of the field
     */
    private function __construct(string $field) {
        $this->field = $field;
    }

    /**
     * Get or create the context.
     * @param string $field The field.
     * @param string $clazz The implementation.
     * @return Context
     */
    protected static function get(string $field, string $clazz) : Context {
        if (!array_key_exists($field, Context::$contexts)) {
            Context::$contexts[$field] = new $clazz($field);
        }
        return Context::$contexts[$field];
    }

    /**
     * Generate code for the context.
     * @return string
     */
    public abstract function generate() : string;

}
?>