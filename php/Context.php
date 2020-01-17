<?php

/**
 * Class Context
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class Context {

    /**
     * An array with contexts.
     * @var array
     */
    private static $contexts = array();

    /**
     * @var string
     */
    protected $field;

    /**
     * Context constructor.
     * @param string $field The name of the field
     */
    private function __construct($field) {
        $this->field = $field;
    }

    /**
     * Get or create the context.
     * @param string $field The field.
     * @param string $clazz The implementation.
     * @return Context
     */
    protected static function get($field, $clazz) {
        if (!array_key_exists($field, Context::$contexts)) {
            Context::$contexts[$field] = new $clazz($field);
        }
        return Context::$contexts[$field];
    }

    /**
     * Generate code for the context.
     * @return string
     */
    public abstract function generate();

}
?>