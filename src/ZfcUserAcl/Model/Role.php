<?php

namespace ZfcUserAcl\Model;

interface Role
{
    /**
     * getRoleId 
     * 
     * @return int
     */
    public function getRoleId();

    /**
     * setRoleId 
     * 
     * @param int $roleId 
     * @return Role
     */
    public function setRoleId($roleId);

    /**
     * getName 
     * 
     * @return string
     */
    public function getName();

    /**
     * setName 
     * 
     * @param string $name 
     * @return Role
     */
    public function setName($name);

    /**
     * getPriority 
     * 
     * @return Role
     */
    public function getPriority();

    /**
     * setPriority 
     * 
     * @param int $priority 
     * @return Role
     */
    public function setPriority($priority);

    /**
     * fromArray
     * 
     * @param array $role 
     * @return Role
     */
    public static function fromArray(array $role);
}
