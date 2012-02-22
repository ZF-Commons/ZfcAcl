<?php

namespace ZfcUserAcl\Model;

interface Resource
{
    /**
     * getResourceId 
     * 
     * @return int
     */
    public function getResourceId();

    /**
     * setResourceId 
     * 
     * @param int $roleId 
     * @return Resource
     */
    public function setResourceId($roleId);

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
     * @return Resource
     */
    public function setName($name);
}
