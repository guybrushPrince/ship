<?php

namespace ship\access;

use ship\interoperability\ShippingTools;

/**
 * Class ViewAccessRightRegister.
 *
 * <p>A class being able to handle access rights to views (which are simple strings).</p>
 *
 * @package ship
 * @subpackge php/access
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
class ViewAccessRightRegister {

    /**
     * The implementation of {@link ShipViewAccessRight}.
     * @var string|null
     */
    private static string|null $implementation = null;

    /**
     * The unique role of super users.
     * @var string|int|null
     */
    private static string|int|null $superUserRole = null;

    /**
     * A list of access rights (as string).
     * @var string[]
     */
    private static array $accessRights = [];

    /**
     * A list of user system roles.
     * @var string[]
     */
    private static array $systemRoles = [];

    /**
     * An associative array storing for the system roles their rights.
     * @var array
     */
    private static array $systemRoleRights = [];

    /**
     * Register the role of the superuser.
     * @param string|int $role The superuser role.
     * @return void
     */
    public static function registerSuperUserRole(string|int $role) : void {
        self::$superUserRole = $role;
    }

    /**
     * Register view access rights.
     * @param string[] $rights The rights.
     * @return void
     */
    public static function registerAccessRights(array $rights) : void {
        self::$accessRights = ShippingTools::unique(array_merge(self::$accessRights, $rights));
    }

    /**
     * Register the systems' roles.
     * @param string[] $roles The roles.
     * @return void
     */
    public static function registerSystemRoles(array $roles) : void {
        self::$systemRoles = ShippingTools::unique(array_merge(self::$systemRoles, $roles));
    }

    /**
     * Register the rights for a system role.
     * @param string|int $role The role ...
     * @param string[] $rights ... and its rights.
     * @return void
     */
    public static function registerRoleRights(string|int $role, array $rights) : void {
        self::$systemRoleRights[$role] = $rights;
    }

    /**
     * Get the superuser role.
     * @return int|string|null
     */
    public static function getSuperUserRole() : int|string|null {
        return self::$superUserRole;
    }

    /**
     * Get the access rights.
     * @return string[]
     */
    public static function getAccessRights() : array {
        return self::$accessRights;
    }

    /**
     * Get the system roles.
     * @return string[]
     */
    public static function getSystemRoles() : array {
        return self::$systemRoles;
    }

    /**
     * Get all rights of a given role.
     * @param string|int $role The system role.
     * @return string[]
     */
    public static function getSystemRoleRights(string|int $role) : array {
        if (array_key_exists($role, self::$systemRoleRights)) {
            return self::$systemRoleRights[$role];
        }
        return [];
    }

    /**
     * Registers a {@link ShipViewAccessRight} implementation.
     * @param string $implementation The implementation.
     * @return void
     */
    public static function registerShipViewAccessRightImplementation(string $implementation) : void {
        self::$implementation = $implementation;
    }

    /**
     * Get the system roles of the current user (if available).
     * @return string[]|int[]
     */
    public static function getCurrentUserRoles() : array {
        if (is_null(self::$implementation)) return [];
        else return self::$implementation::getCurrentUserRoles();
    }

    /**
     * Get the view access rights of the user (if available).
     * @return string[]|int[]
     */
    public static function getCurrentViewAccessRights() : array {
        if (is_null(self::$implementation)) return [];
        else return self::$implementation::getCurrentViewAccessRights();
    }
}
?>