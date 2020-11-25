<?php

/**
 * Class ArrayError
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class ArrayError extends ShipError {

    /**
     * @var array
     */
    protected $errors;

    /**
     * @inheritDoc
     */
    public function __construct($errors = array()) {
        parent::__construct('Multiple errors occured!', 2);
        $this->errors = (is_array($errors) ? $errors : array($errors));
    }
}
?>