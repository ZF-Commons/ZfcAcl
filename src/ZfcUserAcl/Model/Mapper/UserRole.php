<?php

namespace ZfcUserAcl\Model\Mapper;

interface UserRole
{
    public function addRoleToUser($userId, $roleId);

    public function getUserRoles($userId);
}
