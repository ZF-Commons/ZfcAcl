<?php

namespace ZfcAcl\Service;

use Zend\Stdlib\CallbackHandler,
    ZfcBase\Service\ServiceAbstract,
    InvalidArgumentException;

class Context extends ServiceAbstract {
    
    protected $aclService;
            
    public function runAs($role, $callback, $args = array()) {
        //fix parameters
        if(!$callback instanceof CallbackHandler) {
            $callback = new CallbackHandler($callback);
        }
        if(!$role instanceof \Zend\Acl\Role) {
            if(!is_string($role) || empty($role)) {
                throw new InvalidArgumentException("Role must be instance of Zend\Acl\Role or not empty string");
            }
            
            $role = new \Zend\Acl\Role\GenericRole($role);
        }
        
        $aclService = $this->getAclService();
        
        //creates temp role provider returning role provided in method call
        $tmpRoleProvider = new Acl\GenericRoleProvider();
        $tmpRoleProvider->setCurrentRole($role);
        
        //swap role providers
        $origRoleProvider = $aclService->getRoleProvider();
        $aclService->setRoleProvider($tmpRoleProvider);
        
        //execute
        $ret = $callback->call($args);
        
        //swap it back
        $aclService->setRoleProvider($origRoleProvider);
        
        return $ret;
    }
    
    public function getAclService() {
        return $this->aclService;
    }

    public function setAclService($aclService) {
        $this->aclService = $aclService;
    }
    
}