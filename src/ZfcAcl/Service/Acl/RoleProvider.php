<?php
namespace ZfcAcl\Service\Acl;

interface RoleProvider
{
    /**
     * @return Zend\Acl\Role\RoleInterface
     */
    public function getCurrentRole();
}