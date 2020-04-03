<?php

	include_once dirname(__FILE__) . "/../../../yarn/CoastSerializableFunctions.php";

	/**
	 * Trait ViewAccessRightTrait
	 * @author Dr. Dipl.-Inf. Thomas M. Prinz
	 *
	 */
	trait ViewAccessRightTrait {

        /**
         * Add an access right.
         * @param string $name
         * @return bool
         */
		public function addRight($name) {
            $rights = $this->getRights();
			$content = array();
			if (!$rights) {
                $content = array();
			} else {
				$content = simpleJsonDecode($rights);
			}
			$content[] = $name;
            $this->setRights(simpleJsonEncode($content));
			return true;
		}

        /**
         * Get a list of all rights.
         * @return array
         */
		public function getRightsList() {
            $rightsList = array();
            if ($rights = $this->getRights()) {
                $rightsDecoded = simpleJsonDecode($rights);
                $rightsList = array_merge($rightsList, $rightsDecoded);
            }
            return $rightsList;
        }

        /**
         * Checks whether the right is there.
         * @param string $name
         * @return bool
         */
        public function hasRight($name) {
            if ($rights = $this->getRights()) {
                $rightsDecoded = simpleJsonDecode($rights);
                if (in_array($name, $rightsDecoded)) {
                    return true;
                }
            }
            return false;
        }
		
	}
?>