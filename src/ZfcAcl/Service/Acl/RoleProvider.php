<?php
namespace ZfcAcl\Service\Acl;

interface RoleProvider {
    /**
     * @return Zend\Acl\Role
     */
    public function getCurrentRole();
}