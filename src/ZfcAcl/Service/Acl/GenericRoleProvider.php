<?php

namespace ZfcAcl\Service\Acl;

use Zend\Acl\Role\RoleInterface as Role,
    Zend\Acl\Role\GenericRole,
    InvalidArgumentException;

class GenericRoleProvider implements RoleProvider
{
    protected $currentRole;

    public function getCurrentRole()
    {
        return $this->currentRole;
    }

    public function setCurrentRole($currentRole)
    {
        if (!$currentRole instanceof Role) {
            if (!is_string($currentRole)) {
                throw new InvalidArgumentException("Invalid role - string or Role instance needed");
            }
            
            $currentRole = new GenericRole($currentRole);
        }
        $this->currentRole = $currentRole;
    }
}