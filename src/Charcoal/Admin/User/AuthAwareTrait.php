<?php

namespace Charcoal\Admin\User;

use \RuntimeException;

// From Pimple
use \Pimple\Container;

// From 'charcoal-base'
use \Charcoal\User\Authenticator;
use \Charcoal\User\Authorizer;

/**
 * An implementation, as Trait, of the {@see \Charcoal\Admin\User\AuthAwareInterface}.
 */
trait AuthAwareTrait
{
    /**
     * @var Authenticator $authenticator
     */
    private $authenticator;

    /**
     * @var Authorizer $authorizer
     */
    private $authorizer;

    /**
     * @param  Container $container The DI container.
     * @return void
     */
    protected function setAuthDependencies(Container $container)
    {
        $this->setAuthenticator($container['admin/authenticator']);
        $this->setAuthorizer($container['admin/authorizer']);
    }

    /**
     * Set the authentication service.
     *
     * @param  Authenticator $authenticator The authentication service.
     * @return AuthAwareInterface
     */
    public function setAuthenticator(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    /**
     * Retrieve the authentication service.
     *
     * @throws RuntimeException If the authenticator was not previously set.
     * @return Authenticator
     */
    public function authenticator()
    {
        if (!$this->authenticator) {
            throw new RuntimeException(
                sprintf('Authenticator service is not defined for "%s"', get_class($this))
            );
        }

        return $this->authenticator;
    }

    /**
     * Set the authorization service.
     *
     * @param  Authorizer $authorizer The authorization service.
     * @return AuthAwareInterface
     */
    public function setAuthorizer(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;

        return $this;
    }

    /**
     * Retrieve the authorization service.
     *
     * @throws RuntimeException If the authorizer was not previously set.
     * @return Authorizer
     */
    public function authorizer()
    {
        if (!$this->authenticator) {
            throw new RuntimeException(
                sprintf('Authorizer service is not defined for "%s"', get_class($this))
            );
        }

        return $this->authorizer;
    }
}
