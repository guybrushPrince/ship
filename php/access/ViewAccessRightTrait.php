<?php

namespace ship\access;

use ship\interoperability\ShippingTools;

/**
 * Trait ViewAccessRightTrait.
 *
 * <p>A trait to separate implementation details from the core model of a {@link ShipViewAccessRight}.</p>
 *
 * @package ship
 * @subpackge php/access
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 0.8.0
 */
trait ViewAccessRightTrait {

    /**
     * Add an access right.
     * @param string $name The name of the right.
     * @return bool
     */
    public function addRight(string $name) : bool {
        $rights = $this->getRights();
        $content = [];
        if ($rights) {
            $content = ShippingTools::jsonDecode($rights);
        }
        $content[] = $name;
        $this->setRights(ShippingTools::jsonEncode($content));
        return true;
    }

    /**
     * Remove an access right.
     * @param string $name The name of the right.
     * @return bool
     */
    public function removeRight(string $name) : bool {
        $rights = $this->getRights();
        $content = [];
        if ($rights) {
            $content = ShippingTools::jsonDecode($rights);
        }
        $content = array_diff($content, [ $name ]);
        $this->setRights(ShippingTools::jsonEncode($content));
        return true;
    }

    /**
     * Get a list of all rights.
     * @return string[]
     */
    public function getRightsList() : array {
        $rightsList = [];
        if ($rights = $this->getRights()) {
            $rightsDecoded = ShippingTools::jsonDecode($rights);
            $rightsList = array_merge($rightsList, $rightsDecoded);
        }
        return $rightsList;
    }

    /**
     * Checks whether the right is there.
     * @param string $name The name of the right.
     * @return bool
     */
    public function hasRight(string $name) : bool {
        if ($rights = $this->getRights()) {
            $rightsDecoded = ShippingTools::jsonDecode($rights);
            if (in_array($name, $rightsDecoded)) {
                return true;
            }
        }
        return false;
    }
}
?>