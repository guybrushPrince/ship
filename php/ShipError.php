<?php

/**
 * Class Error
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class ShipError extends CliffSerializable {

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
    public function getErrorCode(): int {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }
}
?>