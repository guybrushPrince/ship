<?php

namespace ship\interoperability;

use Error;

/**
 * Class ShipperRegister.
 *
 * <p>Allows for registering an {@link AShipper} implementation or instance to a central place.
 * In addition, this class provides the same methods of {@link AShipper} but statically, which
 * simply redirects to the implementation.</p>
 *
 * @package ship
 * @subpackge php/interoperability
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
class ShipperRegister {

    /**
     * The registered shipper.
     * @var AShipper|null
     */
    private static ?AShipper $shipper = null;

    /**
     * Register an {@link AShipper} implementation to this register.
     * @param AShipper|string $shipper The shipper implementation (or instance).
     * @return void
     */
    public static function register(AShipper|string $shipper) : void {
        if (is_string($shipper)) $shipper = new $shipper();
        self::$shipper = $shipper;
    }

    /**
     * Get the key of the shippable object.
     * @param IShippable $shippable The shippable object.
     * @return string|int
     */
    public static function getKey(IShippable $shippable) : string|int {
        if (is_null(self::$shipper))
            throw new Error('Please register an implementation of an AShipper.');
        return self::$shipper->getKey($shippable);
    }

    /**
     * Get the class of the shippable object.
     * @param IShippable $shippable The shippable object.
     * @return string
     */
    public static function getClass(IShippable $shippable) : string {
        if (is_null(self::$shipper)) return get_class($shippable);
        else return self::$shipper->getClass($shippable);
    }

    /**
     * Get a shippable object with a specific class and key.
     * @param string $class The class of the object.
     * @param string|int|array $key The key of the object.
     * @param bool $keyAsIs If the key shall be used as-is or shall be de-converted (default).
     * @return IShippable|null
     */
    public static function getObject(string $class, string|int|array $key,
                                     bool $keyAsIs = false) : ?IShippable {
        if (is_null(self::$shipper))
            throw new Error('Please register an implementation of an AShipper.');
        return self::$shipper->getObject($class, $key, $keyAsIs);
    }

    /**
     * Checks if a shippable object with a specific class and key exists.
     * @param string $class The class of the object.
     * @param string|int|array $key The key of the object.
     * @param bool $keyAsIs If the key shall be used as-is or shall be de-converted (default).
     * @return bool
     */
    public static function exists(string $class, string|int|array $key,
                                  bool $keyAsIs = false) : bool {
        if (is_null(self::$shipper))
            throw new Error('Please register an implementation of an AShipper.');
        return self::$shipper->exists($class, $key, $keyAsIs);
    }

    /**
     * Is there currently an available user session?
     * @return bool
     */
    public static function hasActiveUserSession() : bool {
        if (is_null(self::$shipper))
            throw new Error('Please register an implementation of an AShipper.');
        return self::$shipper->hasActiveUserSession();
    }

    /**
     * Get the object reference serialized to a string.
     * @param IShippable $object The object string.
     * @return string
     */
    public static function getObjectString(IShippable $object) : string {
        $key = self::$shipper->getKey($object);
        return self::$shipper->getClass($object) . '<::>' .
            (self::$shipper->hasStringKey($object) ? $key : self::convertKey($key));
    }

    /**
     * Convert the key
     * @param string|int $value The value / key.
     * @return string
     */
    public static function convertKey(string|int $value) : string {
        return base_convert($value, 10, 36);
    }

    /**
     * Reconvert the key.
     * @param string|int $value The value / key.
     * @return string
     */
    public static function deConvertKey(string|int $value) : string {
        return base_convert($value, 36, 10);
    }
}
?>