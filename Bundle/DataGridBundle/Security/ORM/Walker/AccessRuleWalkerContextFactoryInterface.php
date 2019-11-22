<?php

namespace Oro\Bundle\DataGridBundle\Security\ORM\Walker;

/**
 * Represents a factory to create AccessRuleWalkerContext object.
 */
interface AccessRuleWalkerContextFactoryInterface
{
    /**
     * @param string $permission
     *
     * @return AccessRuleWalkerContextInterface
     */
    public function createContext(string $permission): AccessRuleWalkerContextInterface;
}
