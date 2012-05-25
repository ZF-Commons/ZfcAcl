<?php
namespace ZfcAcl\Model;

interface EventGuardDef
{
    public function getEventId();
    public function getEvent();
    public function getResource();
    public function getPrivilege();
    public function setOptions($options);
}