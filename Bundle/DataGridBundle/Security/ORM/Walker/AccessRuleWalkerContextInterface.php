<?php

namespace Oro\Bundle\DataGridBundle\Security\ORM\Walker;

/**
 * Represents a context in which AccessRuleWalker works in.
 */
interface AccessRuleWalkerContextInterface extends \Serializable
{
    /**
     * Returns the permission the object should be checked.
     *
     * @return string
     */
    public function getPermission();

    /**
     * Returns current logged user class name.
     *
     * @return string
     */
    public function getUserClass();

    /**
     * Returns current logged user id.
     *
     * @return int|null
     */
    public function getUserId();

    /**
     * Returns true if the additional parameter exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasOption(string $key): bool;

    /**
     * Gets the given additional option. In case if the option does not exist, the default value is returned.
     *
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getOption(string $key, $defaultValue = null);

    /**
     * Gets all additional options.
     *
     * @return array [option name => option value, ...]
     */
    public function getOptions(): array;

    /**
     * Sets an additional option.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setOption(string $key, $value): void;

    /**
     * Removes an additional option.
     *
     * @param string $key
     */
    public function removeOption(string $key): void;
}
