<?php

namespace ship\interoperability;

include_once __DIR__ . '/IShippable.php';

/**
 * Abstract class AShipper.
 *
 * <p>Specifies the methods of a shipper that should be implemented to allow to abstract
 * objects.</p>
 *
 * @package ship
 * @subpackge php/interoperability
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.2.0
 */
abstract class AShipper {

    /**
     * Get the key of the shippable object.
     * @param IShippable $shippable The shippable object.
     * @return string|int
     */
    public abstract function getKey(IShippable $shippable) : string|int;

    /**
     * Get the key of the shippable object but converted if necessary.
     * @param IShippable $shippable The shippable object.
     * @return string
     */
    public abstract function getConvertedKey(IShippable $shippable) : string;

    /**
     * Whether the given object has a string key, or not.
     * @param IShippable|string $shippable The shipple object or class of the object.
     * @return bool
     */
    public abstract function hasStringKey(IShippable|string $shippable) : bool;

    /**
     * Get the class of the shippable object.
     * @param IShippable $shippable The shippable object.
     * @return string
     */
    public function getClass(IShippable $shippable) : string {
        return get_class($shippable);
    }

    /**
     * Get a shippable object with a specific class and key.
     * @param string $class The class of the object.
     * @param string|int|array $key The key of the object.
     * @param bool $keyAsIs If the key should be de-converted.
     * @return IShippable|null
     */
    public abstract function getObject(string $class, string|int|array $key,
                                       bool $keyAsIs = false) : ?IShippable;

    /**
     * Does a shippable object with a specific class and key exists?
     * @param string $class The class of the object.
     * @param string|int|array $key The key of the object.
     * @param bool $keyAsIs If the key should be de-converted.
     * @return bool
     */
    public abstract function exists(string $class, string|int|array $key,
                                    bool $keyAsIs = false) : bool;

    /**
     * Is there currently an available user session?
     * @return bool
     */
    public abstract function hasActiveUserSession() : bool;

}
?>