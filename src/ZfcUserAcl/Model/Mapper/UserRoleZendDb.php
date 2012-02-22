<?php

namespace ZfcUser\Model\Mapper;

use ZfcBase\Mapper\DbMapperAbstract;

class UserRoleZendDb extends DbMapperAbstract implements UserRole
{
    protected $tableName = 'user_role';

    public function addRoleToUser($userId, $roleId)
    {
        $data = array(
            'user_id' => $userId,
            'role_id' => $roleId
        );

        $db = $this->getWriteAdapter();
        return $db->insert($this->getTableName(), $data);
    }

    public function getUserRoles($userId)
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
            ->from($this->getTableName())
            ->where('user_id = ?', $userId)
            ->join('role', 'user_role.role_id = role.role_id');

        $rows = $db->fetchAll($sql);
        return $rows;
    }
}
