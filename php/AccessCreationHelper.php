<?php
/**
 * Class AccessCreationHelper
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class AccessCreationHelper {
    /**
     * Add access to a breakwater object.
     * @param BObject $obj The object.
     */
    public static function addAccess(BObject $obj) {
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
            $newRights = true;
            foreach ($access->getAccessRights() as $accessRight) {
                if ($accessRight instanceof AccessRights &&
                    $accessRight->getReadAccessRole() === $userSession->getCurrentRole() &&
                    $accessRight->getWriteAccessRole() === $userSession->getCurrentRole() &&
                    $accessRight->getDeleteAccessRole() === $userSession->getCurrentRole() &&
                    $accessRight->getReadAccessGroup()->getName() === $userSession->getCurrentGroup()->getName() &&
                    $accessRight->getWriteAccessGroup()->getName() === $userSession->getCurrentGroup()->getName() &&
                    $accessRight->getDeleteAccessGroup()->getName() === $userSession->getCurrentGroup()->getName()
                ) $newRights = false;
            }
            if ($newRights)
                $access->addAccessRights(
                    AccessCreationHelper::buildAccessRights(
                        $userSession->getCurrentGroup(),
                        $userSession->getCurrentRole()
                    )
                );
        }
    }

    /**
     * Build current access rights.
     * @param Group $group The current group.
     * @param int $role The current role.
     * @return AccessRights
     */
    public static function buildAccessRights(Group $group, $role) {
        $accessRight = new AccessRights();
        $accessRight->setReadAccessRole($role);
        $accessRight->setReadAccessGroup($group);
        $accessRight->setWriteAccessRole($role);
        $accessRight->setWriteAccessGroup($group);
        $accessRight->setDeleteAccessRole($role);
        $accessRight->setDeleteAccessGroup($group);
        return $accessRight;
    }

}
?>