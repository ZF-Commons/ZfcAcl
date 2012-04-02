<?php

namespace ZfcAcl\Model\Mapper;

interface ResourceLoaderDef {
    public function findByResourceClass($resourceId);
}