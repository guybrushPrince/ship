<?php

/**
 * Class Error
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class Error extends CoastSerializable {

    /**
     * The code of the error.
     * @var integer
     */
    protected $errorCode;

    /**
     * The error message.
     * @var string
     */
    protected $message;

    /**
     * Error constructor.
     * @param string $message (optional) The message.
     * @param integer $errorCode (optional) The error code.
     */
    public function __construct($message = "", $errorCode = 1) {
        $this->errorCode = $errorCode;
        $this->message = $message;
    }

}
?>