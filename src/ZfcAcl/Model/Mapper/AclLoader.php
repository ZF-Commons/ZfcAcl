<?php

namespace ZfcAcl\Model\Mapper;

use Zend\Acl\Acl as ZendAcl;

interface AclLoader
{
    public function loadAclByRoleId(ZendAcl $acl, $roleId);
}