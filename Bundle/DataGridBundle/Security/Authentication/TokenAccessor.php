<?php

namespace Oro\Bundle\DataGridBundle\Security\Authentication;

use Nelmio\Alice\scenario3\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Default implementation of TokenAccessorInterface.
 */
class TokenAccessor implements TokenAccessorInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->tokenStorage->getToken();
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(TokenInterface $token = null)
    {
        $this->tokenStorage->setToken($token);
    }

    /**
     * {@inheritdoc}
     */
    public function hasUser()
    {
        return null !== $this->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        $result = null;
        $token = $this->tokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $user = $token->getUser();
            if ($user instanceof UserInterface) {
                $result = $user;
            }
        }

        return $result;
    }
}
