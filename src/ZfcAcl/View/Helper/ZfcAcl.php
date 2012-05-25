<?php

namespace ZfcAcl\View\Helper;

use Zend\View\Helper\AbstractHelper,
    Zend\Authentication\AuthenticationService;

class ZfcAcl extends AbstractHelper
{
    protected $aclService;

    public function isAllowed($resource, $privilege = null)
    {
        return $this->getAclService()->isAllowed($resource, $privilege);
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