<?php

namespace ZfcUserAcl\Model;

class RoleBase implements Role
{
    /**
     * @var int
     */
    protected $roleId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $priority;

    /**
     * getRoleId 
     * 
     * @return int
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * setRoleId 
     * 
     * @param int $roleId 
     * @return Role
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
        return $this;
    }

    /**
     * getName 
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * setName 
     * 
     * @param string $name 
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;
        return $his;
    }

    /**
     * getPriority 
     * 
     * @return Role
     */
    public function getPriority()
    {
        return $this->priority;
        
    }

    /**
     * setPriority 
     * 
     * @param int $priority 
     * @return Role
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }
}
