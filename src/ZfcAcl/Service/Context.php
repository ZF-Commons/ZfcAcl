<?php

namespace ZfcAcl\Service;

use Zend\Stdlib\CallbackHandler,
    Zend\Acl\Role\RoleInterface as Role,
    Zend\Acl\Role\GenericRole,
    ZfcBase\Service\ServiceAbstract,
    ZfcAcl\Service\Acl\GenericRoleProvider,
    InvalidArgumentException,
    RuntimeException as TempRoleNotSetException,
    RuntimeException as TempRoleSetAlreadyException;

class Context extends ServiceAbstract
{

    protected $aclService;
    protected $originalRoleProvider;

    public function runAs($role, $callback, $args = array())
    {
        if (!$callback instanceof CallbackHandler) {
            $callback = new CallbackHandler($callback);
        }

        $this->setTempRole($role);

        //execute
        $ret = $callback->call($args);

        $this->rollbackTempRole();

        return $ret;
    }

    public function setTempRole($role)
    {
        if ($this->originalRoleProvider !== null) {
            throw new TempRoleSetAlreadyException("You have to rollback currently set temp role");
        }

        //fix parameters
        if (!$role instanceof Role) {
            if (!is_string($role) || empty($role)) {
                throw new InvalidArgumentException("Role must be instance of Zend\Acl\Role or not empty string");
            }

            $role = new GenericRole($role);
        }

        $aclService = $this->getAclService();

        //creates temp role provider returning role provided in method call
        $tmpRoleProvider = new GenericRoleProvider();
        $tmpRoleProvider->setCurrentRole($role);

        //swap role providers
        $this->originalRoleProvider = $aclService->getRoleProvider();
        $aclService->setRoleProvider($tmpRoleProvider);
    }

    public function rollbackTempRole()
    {
        if ($this->originalRoleProvider === null) {
            throw new TempRoleNotSetException("Can't rollback the role provider");
        }

        $aclService = $this->getAclService();
        //swap it back
        $aclService->setRoleProvider($this->originalRoleProvider);
    }

    public function getAclService()
    {
        return $this->aclService;
    }

    public function setAclService($aclService)
    {
        $this->aclService = $aclService;
    }
    
}