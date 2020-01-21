<?php
/**
 * Class AccessCreationHelper
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class AccessCreationHelper {

    /**
     * Add access to a breakwater object.
     * @param BObject $obj The object.
     * @param Group|bool $group The group.
     * @param int $readRole The read role.
     * @param int $writeRole The write role.
     * @param int $deleteRole The delete role.
     */
    public static function addAccess(BObject $obj,
                                     $group = false,
                                     $readRole = AccessRole::Reporter,
                                     $writeRole = AccessRole::Developer,
                                     $deleteRole = AccessRole::Master,
                                     $ignoreUserSession = false) {
        $access = $obj->getAccess();
        if (!$access) {
            $access = new Access();
            $obj->setAccess($access);
        } else {
            return;
        }
        $persistence = Persistence::instance();
        if ($persistence->isSecureMode()) {
            $userSession = $persistence->getUserSession();
            if ($userSession && !$ignoreUserSession) {
                $readRole = $userSession->getCurrentRole();
                $writeRole = $userSession->getCurrentRole();
                $deleteRole = $userSession->getCurrentRole();
                $group = $userSession->getCurrentGroup();
            }
            $newRights = true;
            foreach ($access->getAccessRights() as $accessRight) {
                if ($accessRight instanceof AccessRights &&
                    $accessRight->getReadAccessRole() === $readRole &&
                    $accessRight->getWriteAccessRole() === $writeRole &&
                    $accessRight->getDeleteAccessRole() === $deleteRole &&
                    $accessRight->getReadAccessGroup()->getName() === $group->getName() &&
                    $accessRight->getWriteAccessGroup()->getName() === $group->getName() &&
                    $accessRight->getDeleteAccessGroup()->getName() === $group->getName()
                ) $newRights = false;
            }
            if ($newRights)
                $access->addAccessRights(
                    AccessCreationHelper::buildAccessRights(
                        $group,
                        $readRole,
                        $writeRole,
                        $deleteRole
                    )
                );
        }
    }

    /**
     * Add access rights to objects of specific classes for a group in retrospect.
     * @param array $classes The classes
     * @param Group $group The group.
     * @param int $readRole The read role.
     * @param int $writeRole The write role.
     * @param int $deleteRole The delete role.
     */
    public static function addAccessRetrospect(
        array $classes,
        Group $group,
        $readRole = AccessRole::Reporter,
        $writeRole = AccessRole::Developer,
        $deleteRole = AccessRole::Master) {
        $userSession = new UserSession();
        $user = new User();
        $user->setUserName("default");
        $userSession->setUser($user);
        $userSession->setCurrentRole(AccessRole::Master);
        $userSession->setCurrentGroup($group);

        $persistence = Persistence::instance();
        $persistence->setUserSession($userSession);
        $persistence->startTransaction();
        foreach($classes as $class) {
            AccessCreationHelper::addAccessToClass($class, $group, $readRole, $writeRole, $deleteRole);
        }
        $persistence->endTransaction();
    }

    /**
     * Add access rights to all objects of the given class.
     * @param string $class The class.
     * @param Group $group The group.
     * @param integer $readRole The read role.
     * @param integer $writeRole The write role.
     * @param integer $deleteRole The delete role.
     */
    public static function addAccessToClass($class, Group $group, $readRole, $writeRole, $deleteRole) {
        $objects = $class::getAll();
        foreach ($objects as $obj) {
            AccessCreationHelper::addAccess($obj, $group, $readRole, $writeRole, $deleteRole);
            $obj->updateClose();
        }
    }

    /**
     * Build current access rights.
     * @param Group $group The group.
     * @param int $readRole The read role.
     * @param int $writeRole The write role
     * @param int $deleteRole The delete role.
     * @return AccessRights
     */
    public static function buildAccessRights(Group $group,
                                             $readRole = AccessRole::Reporter,
                                             $writeRole = AccessRole::Developer,
                                             $deleteRole = AccessRole::Master
    ) {
        $accessRight = new AccessRights();
        $accessRight->setReadAccessRole($readRole);
        $accessRight->setReadAccessGroup($group);
        $accessRight->setWriteAccessRole($writeRole);
        $accessRight->setWriteAccessGroup($group);
        $accessRight->setDeleteAccessRole($deleteRole);
        $accessRight->setDeleteAccessGroup($group);
        return $accessRight;
    }

    /**
     * Add create access rights to a class for a given group and role.
     * @param array $classes An array of classes for which access is granted.
     * @param Group $group The group
     * @param integer $role The role.
     */
    public static function buildCreateAccess(array $classes, Group $group, $role) {
        $persistence = Persistence::instance();
        $persistence->startTransaction();
        foreach ($classes as $cl) {
            $createAccess = new CreateAccess();
            $createAccess->setGroup($group);
            $createAccess->setClass($cl);
            $createAccess->setRole($role);
            $createAccess->create();
        }
        $persistence->endTransaction();
    }

}
?>