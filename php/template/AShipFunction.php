<?php

namespace ship\template;

/**
 * Abstract class AShipFunction.
 *
 * <p>Some outputs contain program code. This is a placeholder to provide (dynamic) program code.</p>
 *
 * @package ship
 * @subpackage php/template
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
abstract class AShipFunction {

    /**
     * Generate program code (usually for the browser, i.e., JS).
     * @return string
     */
    public abstract function generate() : string;
}
?>