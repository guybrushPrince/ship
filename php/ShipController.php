<?php

/**
 * Abstract Class ShipController.
 *
 * <p>Marks a class as controller for *Ship* although it is not crucial to mark.</p>
 *
 * @package ship
 * @subpackge php
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
abstract class ShipController {

    /**
     * Get the scope of the controller.
     * @return string|null
     */
    public function getScope() : ?string {
        return null;
    }

}
?>