<?php

namespace ZfcAcl\Model\Mapper;

use Zend\Acl\Acl as ZendAcl,
    ZfcAcl\Model\RouteResourceMap as RouteResourceMapModel,
    InvalidArgumentException;

class RouteResourceMapConfig implements RouteResourceMap
{
    protected $config;

    public function findByRouteName($route)
    {
        $config = $this->getConfig();
        //matuszemi: do nothing if config is empty/null
        if (empty($config)) {
            return null;
        }

        $map = $this->createMap($config);
        return $map;
    }

    protected function createMap($config)
    {
        $map = new RouteResourceMapModel();
        if (is_string($config)) {
            $map->setDefaultResource($config);
            return $map;
        }

        if (isset($config['default'])) {
            $map->setDefaultResource($config['default']);
        }
        if (isset($config['child_map'])) {
            foreach ($config['child_map'] as $key => $childMap) {
                $childMap = $this->createMap($childMap);
                $map->setChildMap($childMap, $key);
            }
        }

        return $map;
    }

    //setters/getters
    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        if (!is_array($config)) {
            throw new InvalidArgumentException("We accept array only for now!");
        }

        $this->config = $config;
    }

}