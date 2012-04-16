<?php
namespace ZfcAcl\Service;

use ZfcAcl\Service\Acl;

/**
 * Provides methods required by any object that should be aware of the ZfcAcl Acl service
 */
interface ZfcAclAwareInterface
{
    /**
     * @abstract
     * @param Acl $acl
     */
    public function setZfcAclService(Acl $acl);

    /**
     * @abstract
     * @return Acl|null
     */
    public function getZfcAclService();
}
