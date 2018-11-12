<?php

declare(strict_types = 1);

namespace App\Core;

use App\User\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

trait AuthenticationTrait
{
    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @required */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /* Directly copied from ControllerTrait */
    protected function getUser() : ?User
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }
        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }
        /**
         * Just a bit of a hack to keep type hinting happy
         * Can't seem to cast an object
         * @var User $userx
         */
        $userx = $user;
        return $userx;
    }
}
