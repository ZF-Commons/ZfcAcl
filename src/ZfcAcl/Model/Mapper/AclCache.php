<?php

namespace ZfcAcl\Model\Mapper;

use Zend\Acl\Acl as ZendAcl;

interface AclCache
{
    public function findByRoleId($roleId);
    public function persist(ZendAcl $acl, $roleId);
    public function invalidate($roleId);
}