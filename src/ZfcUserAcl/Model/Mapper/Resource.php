<?php

namespace ZfcUserAcl\Model\Mapper;

use ZfcUserAcl\Model\Resource as ResourceModel;

interface Resource
{
    public function save(ResourceModel $resource);

    public function getAllResources();
}
