<?php

namespace ZfcUserAcl\Model\Mapper;

use ZfcUserAcl\Model\Role as RoleModel;

interface Role
{
    public function save(RoleModel $role);

    public function getAllRoles();
}

