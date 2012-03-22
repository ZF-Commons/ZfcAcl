<?php

namespace ZfcAcl\Model\Mapper;

use     Zend\Acl\Acl as ZendAcl,
        ZfcAcl\Model\EventGuardDefStatic,
        ZfcAcl\Model\EventGuardDefMap as EventGuardDefMapModel,
        InvalidArgumentException;

class EventGuardDefMapConfig implements EventGuardDefMap {
    protected $config;
    
    public function findByRoleId($roleId) {
        $config = $this->getConfig();
        //mz: do nothing if config is empty/null
        if(empty($config)) {
            return array();
        }
        
        $defMap = new EventGuardDefMapModel();
        
        foreach($config as $key => $def) {
            $defMap->append($this->createDef($def, $key));
        }
        
        return $defMap;
    }
    
    protected function createDef($data, $key) {
        //TODO different types
        foreach(array('eventId', 'event', 'resource') as $param) {
            if(empty($data[$param])) {
                throw new InvalidArgumentException("Required param '$param' not define");
            }
        }
        
        $def = new EventGuardDefStatic();
        $def->setEventId($data['eventId']);
        $def->setEvent($data['event']);
        $def->setResource($data['resource']);
        if(isset($data['privilege'])) {
            $def->setPrivilege($data['privilege']);
        }
        
        return $def;
    }
    
    //setters/getters
    public function getConfig() {
        return $this->config;
    }

    public function setConfig($config) {
        if(!is_array($config)) {
            throw new InvalidArgumentException("We accept array only for now!");
        }
        
        $this->config = $config;
    }

}