<?php

namespace ZfcAcl\Model\Mapper;

use Zend\Stdlib\ArrayUtils,
    ZfcAcl\Model\ResourceLoaderDef,
    ZfcAcl\Model\Mapper\ResourceLoaderDef as ResourceLoaderDefMapper,
    InvalidArgumentException;

class ResourceLoaderDefConfig implements ResourceLoaderDefMapper {
    protected $config;
    
    public function findByResourceClass($resourceClass) {
        $config = $this->getConfig();
        //mz: do nothing if config is empty/null
        if(empty($config) || !isset($config[$resourceClass])) {
            return null;
        }
        
        $cnf = $config[$resourceClass];
        $def = new ResourceLoaderDef();
        if(empty($cnf['parent_resource'])) {
            throw new InvalidArgumentException("No parent_resource");
        }
        $def->setParentResource($cnf['parent_resource']);
        
        if(empty($cnf['parent_role'])) {
            throw new InvalidArgumentException("No parent_role");
        }
        $def->setParentRole($cnf['parent_role']);

        if(empty($cnf['allow_rules']) || !ArrayUtils::isHashTable($cnf['allow_rules'])) {
            throw new InvalidArgumentException("No allow_rules or it's not hash table");
        }
        $def->setAllowRules($cnf['allow_rules']);
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