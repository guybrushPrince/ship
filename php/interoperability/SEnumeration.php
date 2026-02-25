<?php

namespace ship\interoperability;

/**
 * Abstract class SEnumeration.
 *
 * <p>Gives the ability to declare a class as an enumeration with a usual API.</p>
 *
 * @package ship
 * @subpackge php/interoperability
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
abstract class SEnumeration {

    /**
     * Get the values.
     * @return int[]
     */
    public abstract static function getValues() : array;

    /**
     * Get the names of the values.
     * @return string[]
     */
    public abstract static function getNames() : array;

}
?>
