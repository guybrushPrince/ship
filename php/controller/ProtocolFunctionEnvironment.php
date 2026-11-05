<?php

namespace ship\controllers;

use ship\protocols\TemplateProtocol;

/**
 * Interface ProtocolFunctionEnvironment.
 *
 * <p>An interface being required to define {@link TemplateProtocol} and register them to {@link ActionRegister}.</p>
 *
 * @package ship
 * @subpackge php/controller
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.2.0
 */
interface ProtocolFunctionEnvironment {

    /**
     * Adds events (templates).
     * @return bool
     */
    public static function add() : bool;

    /**
     * Get the name of the environment.
     * @return string
     */
    public static function environment() : string;

}
?>