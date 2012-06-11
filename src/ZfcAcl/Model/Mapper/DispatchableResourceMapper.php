<?php

namespace ZfcAcl\Model\Mapper;

use ZfcBase\Model\AbstractModel;

/**
 * Simply maps provided dispatchable names to resource names. In this case by simply adding some prefix.
 *
 * @todo add method to reverse mappings?
 */
class DispatchableResourceMapper
    extends AbstractModel
    implements DispatchableResourceMapperInterface
{
    const DEFAULT_RESOURCE_PREFIX = 'dispatchable/';

    /**
     * @var string prefix to be used for resource names for controllers
     */
    protected $resourcePrefix = self::DEFAULT_RESOURCE_PREFIX;

    /**
     * {@inheritDoc}
     */
    public function getDispatchableResource($dispatchableName)
    {
        return $this->resourcePrefix . $dispatchableName;
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