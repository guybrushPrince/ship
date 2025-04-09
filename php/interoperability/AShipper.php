<?php

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
 * @version 1.0.0
 */
abstract class AShipper {

    /**
     * Get the key of the shippable object.
     * @param IShippable $shippable The shippable object.
     * @return string|int
     */
    public abstract function getKey(IShippable $shippable) : string|int;

    /**
     * Get the class of the shippable object.
     * @param IShippable $shippable The shippable object.
     * @return string
     */
    public function getClass(IShippable $shippable) : string {
        return get_class($shippable);
    }

}
?>