<?php

namespace ship\access;

/**
 * Interface ShipViewAccessRight.
 *
 * <p>Defines static function stubs to enable the access to the system roles of the current user and to the
 * current user's view access rights.</p>
 *
 * @package ship
 * @subpackge php/access
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
interface ShipViewAccessRight {

    /**
     * Add an access right.
     * @param string $name The name of the right.
     * @return bool
     */
    public function addRight(string $name) : bool;

    /**
     * Remove an access right.
     * @param string $name The name of the right.
     * @return bool
     */
    public function removeRight(string $name) : bool;

    /**
     * Get a list of all rights.
     * @return string[]
     */
    public function getRightsList() : array;

    /**
     * Checks whether the right is there.
     * @param string $name The name of the right.
     * @return bool
     */
    public function hasRight(string $name) : bool;

    /**
     * Get the system roles of the current user (if available).
     * @return string[]|int[]
     */
    public static function getCurrentUserRoles() : array;

    /**
     * Get the view access rights of the user (if available).
     * @return string[]|int[]
     */
    public static function getCurrentViewAccessRights() : array;
}
?>