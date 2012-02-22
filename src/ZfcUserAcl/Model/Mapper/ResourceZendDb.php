<?php

namespace ZfcUserAcl\Model\Mapper;

use ZfcUserAcl\Module as ZfcUserAcl,
    ZfcUserAcl\Model\Resource as ResourceModel,
    ZfcBase\Mapper\DbMapperAbstract;

class ResourceZendDb extends DbMapperAbstract implements Resource
{
    protected $tableName = 'resource';

    public function save(ResourceModel $resource)
    {
        $data = array(
            'resource_id' => $resource->getResourceId(),
            'name' => $resource->getName()
        );

        $db = $this->getWriteAdapter();
        return $db->insert($this->getTableName(), $data);
    }

    public function getAllResources()
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
            ->from($this->getTableName());

        $rows = $db->fetchAll($sql);

        $modelClass = ZfcUserAcl::getOption('resource_model_class');
        $models = array();

        foreach ($rows as $row) {
            $models[] = $modelClass::fromArray($row);
        }

        return $models;
    }
}
