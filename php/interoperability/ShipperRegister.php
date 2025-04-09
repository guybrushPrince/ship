<?php

include_once __DIR__ . '/AShipper.php';

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
}
?>