<?php

namespace ZfcAcl\Model;

use ZfcBase\Model\AbstractModel;

class RouteResourceMap extends AbstractModel
{
    protected $defaultResource;
    protected $childMaps = array();

    /**
     * TODO this does not look like optimized but it does its job
     * @param string $routeNames
     * @return string
     */
    public function getRouteResource($routeNames)
    {
        if (!is_string($routeNames)) {
            return $this->getDefaultResource();
        }

        $routeName = strstr($routeNames, '/', true);
        if ($routeName === false) {
            $routeName = $routeNames;
        }
        $restRouteName = substr(strstr($routeNames, '/'), 1);

        $childMap = $this->getChildMap($routeName);
        if ($childMap) {
            $resource = $childMap->getRouteResource($restRouteName);
            if ($resource) {
                return $resource;
            }
            return $this->getDefaultResource();
        }

        return $this->getDefaultResource();
    }

    public function getDefaultResource()
    {
        return $this->defaultResource;
    }

    public function setDefaultResource($defaultResource)
    {
        $this->defaultResource = $defaultResource;
    }

    public function getChildMaps()
    {
        return $this->childMaps;
    }

    public function setChildMap(RouteResourceMap $childMap, $key)
    {
        $this->childMaps[$key] = $childMap;
    }

    public function getChildMap($key)
    {
        return (isset($this->childMaps[$key]) ? $this->childMaps[$key] : null);
    }
}