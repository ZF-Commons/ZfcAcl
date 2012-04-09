<?php

namespace ZfcAcl\Model\Mapper;

use ZfcBase\Model\ModelAbstract;

class DispatchableResourceMap extends ModelAbstract
{
    const DEFAULT_RESOURCE_PREFIX = 'dispatchable/';

    /**
     * @var string prefix to be used for resource names for controllers
     */
    protected $resourcePrefix = self::DEFAULT_RESOURCE_PREFIX;

    /**
     * Retrieves the resource id associated with the given controller name
     *
     * @param string $controllerName
     * @return string
     */
    public function getControllerResource($controllerName)
    {
        return $this->resourcePrefix . $controllerName;
    }

    /**
     * @param string $resourcePrefix
     */
    public function setResourcePrefix($resourcePrefix)
    {
        $this->resourcePrefix = (string) $resourcePrefix;
    }

    /**
     * @return string
     */
    public function getResourcePrefix()
    {
        return $this->resourcePrefix;
    }
}