<?php

namespace ship\outputs;

/**
 * Class Script.
 *
 * <p>A script with code (e.g., JS) to be returned by API methods.</p>
 *
 * @package ship
 * @subpackge php/outputs
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.3.0
 */
class Script {

    /**
     * The programming language.
     * @var int
     */
    private int $language;

    /**
     * The code.
     * @var string
     */
    private string $code;

    /**
     * Script constructor.
     * @param int $language The programming language.
     * @param string $code The code.
     */
    public function __construct(int $language, string $code) {
        $this->language = $language;
        $this->code = $code;
    }

    /**
     * Get the code.
     * @return string
     */
    public function getCode() : string {
        return $this->code;
    }

    /**
     * Get the programming language.
     * @return int
     */
    public function getLanguage() : int {
        return $this->language;
    }
}
?>