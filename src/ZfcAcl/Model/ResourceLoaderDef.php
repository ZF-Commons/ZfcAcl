<?php

namespace ZfcAcl\Model;

use ZfcBase\Model\ModelAbstract;

class ResourceLoaderDef extends ModelAbstract {
    protected $parentRole;
    protected $parentResource;
    protected $allowRules;
    
    public function getParentRole() {
        return $this->parentRole;
    }

    public function setParentRole($parentRole) {
        $this->parentRole = $parentRole;
    }

    public function getParentResource() {
        return $this->parentResource;
    }

    public function setParentResource($parentResource) {
        $this->parentResource = $parentResource;
    }

    public function getAllowRules() {
        return $this->allowRules;
    }

    public function setAllowRules($allowRules) {
        $this->allowRules = $allowRules;
    }


}