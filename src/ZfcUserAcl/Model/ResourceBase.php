<?php

namespace ZfcUserAcl\Model;

class ResourceBase implements Resource
{

    /**
     * @var int
     */
    protected $resourceId;

    /**
     * @var string
     */
    protected $name;
 
    /**
     * getResourceId
     *
     * @return int
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }
 
    /**
     * getResourceId
     *
     * @param int $resourceId
     */
    public function setResourceId($resourceId)
    {
        $this->resourceId = $resourceId;
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
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
