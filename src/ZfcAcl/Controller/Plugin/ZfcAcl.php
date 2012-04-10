<?php

namespace ZfcAcl\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ZfcAcl extends AbstractPlugin
{
    protected $aclService;
    
    public function isAllowed($resource, $privilege = null)
    {
        return $this->getAclService()->isAllowed($resource, $privilege);
    }
    
    public function getAclService ()
    {
        return $this->aclService;
    }

    public function setAclService ($aclService)
    {
        $this->aclService = $aclService;
    }
}