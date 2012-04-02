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
        
        //do main stuff
        $listener = $this->getAclService()->events()->attach('getRole', function($e) use($role) {
            return $role;
        }, 1000);
        
        $ret = $callback->call($args);
        
        $this->getAclService()->events()->detach($listener);
        
        return $ret;
    }
    
    public function getAclService() {
        return $this->aclService;
    }

    public function setAclService($aclService) {
        $this->aclService = $aclService;
    }
    
}