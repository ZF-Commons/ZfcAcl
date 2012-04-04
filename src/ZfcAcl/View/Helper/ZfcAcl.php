<?php

namespace ZfcAcl\View\Helper;

use Zend\View\Helper\AbstractHelper,
    Zend\Authentication\AuthenticationService;

class ZfcAcl extends AbstractHelper
{
    protected $zfcAcl;

    public function isAllowed($resource, $privilege)
    {
        return $this->getZfcAcl()->isAllowed($resource, $privilege);
    }
    
    public function getZfcAcl ()
    {
        return $this->ZfcAcl;
    }
    
    public function setZfcAcl ($ZfcAcl)
    {
        $this->ZfcAcl = $ZfcAcl;
        return $this->ZfcAcl;
    }
}