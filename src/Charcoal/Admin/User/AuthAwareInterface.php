<?php

namespace Charcoal\Admin\User;

use Charcoal\User\Authenticator;
use Charcoal\User\Authorizer;

/**
 * Defines a class with authentication capabilities.
 *
 * Implementation, as trait, provided by {@see \Charcoal\Admin\User\AuthAwareTrait}.
 */
interface AuthAwareInterface
{
    /**
     * Set the authentication service.
     *
     * @param  Authenticator $authenticator The authentication service.
     * @return AuthAwareInterface
     */
    public function setAuthenticator(Authenticator $authenticator);

    /**
     * Retrieve the authentication service.
     *
     * @return Authenticator
     */
    public function authenticator();

    /**
     * Set the authorization service.
     *
     * @param  Authorizer $authorizer The authorization service.
     * @return AuthAwareInterface
     */
    public function setAuthorizer(Authorizer $authorizer);

    /**
     * Retrieve the authorization service.
     *
     * @return Authorizer
     */
    public function authorizer();
}
