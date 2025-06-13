<?php

include_once dirname(__FILE__) . "/../../../../breakwater/util/Authentication.php";
include_once dirname(__FILE__) . "/../../../../breakwater/model/breakwater/BObject.php";
include_once dirname(__FILE__) . "/../../util/ViewAccessRightTrait.php";

/**
 * A ViewAccessRight defines what a user can do within a group and role in the view.
 *
 * @pretty { "0": "Access Rights for Views", "1": "Zugriffsrechte für Ansichten" }
 * @persistent
 * @keyGiven
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class ViewAccessRight extends BObject {

    // Set the traits for the DAOs, i.e., the database accesses
    use ViewAccessRightPersistentTrait;
    use ViewAccessRightTrait;

    /**
     * The id.
     * @length 255
     * @key
     * @type string
     * @var string
     */
    protected $id;

    /**
     * The group.
     * @pretty { "0": "Gruppe", "1": "Group" }
     * @crucial
     * @type Group
     * @var Group
     * @index group_idx
     */
    protected $group;

    /**
     * The role.
     * @pretty { "0": "Role", "1": "Rolle" }
     * @crucial
     * @values AccessRole
     * @type integer
     * @var int
     */
    protected $role = AccessRole::Reporter;

    /**
     * The rights (as json).
     * @type string
     * @crucial
     * @json { "string" }
     * @length LONG
     * @var string
     */
    protected $rights;

    /**
     * Get the id.
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the id.
     * @param int $id The new value of the id.
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get the group.
     * @return Group
     */
    public function getGroup() {
        if (is_callable($this->group)) {
            $this->group = $this->group->__invoke();
        }
        return $this->group;
    }

    /**
     * Set the group.
     * @param Group $group
     */
    public function setGroup(Group $group) {
        $this->setId($group->getName() . ':' . $this->role);
        $this->group = $group;
    }

    /**
     * Get the role.
     * @return int
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Set the role.
     * @param int $role The new role.
     */
    public function setRole($role) {
        $this->setId($this->group->getName() . ':' . $role);
        $this->role = $role;
    }

    /**
     * Get the rights.
     * @return string
     */
    public function getRights() {
        return $this->rights;
    }

    /**
     * Set the rights.
     * @param string $rights The new rights.
     */
    public function setRights($rights) {
        $this->rights = $rights;
    }

}
?>