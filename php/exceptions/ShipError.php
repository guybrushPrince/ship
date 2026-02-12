<?php

namespace ship\exceptions;

/**
 * Class ShipError.
 *
 * <p>An error within <em>Ship</em> that can be shared with the front end.</p>
 *
 * @package ship
 * @subpackge php/exceptions
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.3.0
 */
class ShipError {

    /**
     * The code of the error.
     * @var int
     */
    protected int $errorCode;

    /**
     * The error message.
     * @var string
     */
    protected string $message;

    /**
     * Error constructor.
     * @param string $message (optional) The message.
     * @param int $errorCode (optional) The error code.
     */
    public function __construct(string $message = "", int $errorCode = 1) {
        $this->errorCode = $errorCode;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getErrorCode() : int {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getMessage() : string {
        return $this->message;
    }

    /**
     * Returns the serialized ship error.
     * @return array
     */
    public function serialize() : array {
        return [
            '__class_name__' => __CLASS__,
            'errorCode' => $this->errorCode,
            'message' => $this->message
        ];
    }
}
?>