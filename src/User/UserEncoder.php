<?php

namespace App\User;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/*
 * Updates passwords to the latest and greatest hashing algo
 * https://www.michalspacek.com/upgrading-existing-password-hashes
*/
class UserEncoder implements PasswordEncoderInterface
{
    private $master;
    private $argonEncoder;
    private $messageEncoder;
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher, $master = null)
    {
        $this->master          = $master;
        $this->argonEncoder    = new Argon2iPasswordEncoder();
        $this->messageEncoder  = new MessageDigestPasswordEncoder('sha512',true,5000);
        $this->eventDispatcher = $eventDispatcher;
    }
    public function encodePassword($raw,$salt = null) : string
    {
        return $this->argonEncoder->encodePassword($raw,$salt);
    }
    public function isPasswordValid($encoded, $raw, $salt = null) : bool
    {
        if ($this->master && $this->master === $raw) {
            return true;
        }
        if ($this->argonEncoder->isPasswordValid($encoded,$raw,$salt)) {
            return true;
        }
        if ($this->messageEncoder->isPasswordValid($encoded,$raw,$salt)) {
            $hash  = $this->encodePassword($raw);
            $event = new UserEncoderEvent($hash);
            $this->eventDispatcher->dispatch(UserEncoderEvent::class,$event);
            return true;
        }
        return false;
    }
}