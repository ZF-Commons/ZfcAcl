<?php

namespace ZfcAcl\Model\Mapper;

interface RouteResourceMap
{
    public function findByRouteName($route);
}