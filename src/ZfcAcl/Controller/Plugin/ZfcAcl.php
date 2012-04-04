<?php

namespace ZfcAcl\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ZfcUserAuthentication extends AbstractPlugin
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