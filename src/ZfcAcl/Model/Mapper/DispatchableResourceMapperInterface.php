<?php

namespace ZfcAcl\Model\Mapper;

/**
 * Maps provided dispatchable names to resource names. In this case by simply adding some prefix.
 *
 * @todo add method to reverse mappings?
 */
interface DispatchableResourceMapperInterface
{
    /**
     * Retrieves the resource id associated with the given dispatchable name
     *
     * @param string $dispatchableName
     * @return string
     */
    public function getDispatchableResource($dispatchableName);
}