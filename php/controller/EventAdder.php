<?php

namespace ship\controllers;

use ship\protocols\TemplateProtocol;

/**
 * Interface EventAdder.
 *
 * <p>An interface being required to define {@link TemplateProtocol} and register them to {@link ActionRegister}.</p>
 *
 * @package ship
 * @subpackge php/controller
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.2.0
 */
interface EventAdder {

    /**
     * Adds events (templates).
     * @return bool
     */
    public static function add() : bool;

}
?>