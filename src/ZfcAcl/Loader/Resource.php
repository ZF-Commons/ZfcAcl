<?php

namespace ZfcAcl\Loader;

use ZfcAcl\Exception\UnauthorizedException,
    RuntimeException as NotArrayAccessResource;

class Resource {
    protected $resourceLoaderDefMapper;
    protected $roleIdTemplate = 'identity/%d';
    
    public function onLoadResource($e) {
        $resource = $e->getParam('resource');
        $acl = $e->getParam('acl');

        if(!$resource instanceof \ArrayAccess) {
            throw new NotArrayAccessResource("Resource must implement ArrayAccess");
        }
        
        $resourceClass = get_class($resource);
        
        $def = $this->getResourceLoaderDefMapper()->findByResourceClass($resourceClass);
        if($def) {
            foreach($def->getAllowRules() as $identityProperty => $privileges) {
                $roleId = sprintf($this->roleIdTemplate, $resource[$identityProperty]);
                if(!$acl->hasRole($roleId)) {
                    $acl->addRole($roleId, $def->getParentRole());
                }
                if(!$acl->hasResource($resource)) {
                    $acl->addResource($resource, $def->getParentResource());
                }
                
                $acl->allow($roleId, $resource, $privileges);
            }
        }
    }
    
    //setters/getters
    public function getResourceLoaderDefMapper() {
        return $this->resourceLoaderDefMapper;
    }

    public function setResourceLoaderDefMapper($resourceLoaderDefMapper) {
        $this->resourceLoaderDefMapper = $resourceLoaderDefMapper;
    }

    public function getAclService() {
        return $this->aclService;
    }

    public function setAclService($aclService) {
        $this->aclService = $aclService;
    }


}