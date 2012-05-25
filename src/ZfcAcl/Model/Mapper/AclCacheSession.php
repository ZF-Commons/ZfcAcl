<?php

namespace ZfcAcl\Model\Mapper;

use Zend\Acl\Acl as ZendAcl,
    Zend\Session\Container;

class AclCacheSession implements Acl
{
    private $session;
    private $containerOffset = 'acl';

    public function findByRoleId($roleId)
    {
        $cont = $this->getContainer();
        if ($cont->offsetExists($this->containerOffset)) {
            $aclSerialized = $cont->offsetGet($this->containerOffset);

            $acl = unserialize($aclSerialized);
            if (!$acl instanceof ZendAcl) {
                return null;
            }

            return $acl;
        }

        return null;
    }

    public function persist(ZendAcl $acl, $roleId)
    {
        $cont = $this->getContainer();
        $aclSerialized = serialize($acl);
        $cont->offsetSet($this->containerOffset, $aclSerialized);
    }

    public function invalidate($roleId)
    {
        $cont = $this->getContainer();
        $cont->offsetUnset($this->containerOffset);
    }

    protected function getContainer()
    {
        if ($this->session === null) {
            $this->session = new Container(__CLASS__);
        }

        return $this->session;
    }
}