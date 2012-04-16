<?php

namespace ZfcAcl\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use ZfcAcl\Service\Acl;
use ZfcAcl\Service\ZfcAclAwareInterface;

class ZfcAcl extends AbstractHelper implements ZfcAclAwareInterface
{
    /**
     * @var Acl
     */
    protected $aclService;

    /**
     * @param null|string|\Zend\Acl\Resource $resource
     * @param null|string $privilege
     * @return boolean
     */
    public function isAllowed($resource, $privilege = null)
    {
        return $this->getAclService()->isAllowed($resource, $privilege);
    }

    /**
     * {@inheritDoc}
     */
    public function setZfcAclService(Acl $acl)
    {
        $this->aclService = $acl;
    }

    /**
     * {@inheritDoc}
     */
    public function getZfcAclService()
    {
        return $this->aclService;
    }
}