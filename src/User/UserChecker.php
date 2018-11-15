<?php

namespace App\User;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface,EventSubscriberInterface
{
    private $hash; // Newly generated hash

    private $userConnection;

    public static function getSubscribedEvents()
    {
        return [
            UserEncoderEvent::class => 'onEncoderEvent',
        ];
    }
    public function __construct(UserConnection $userConnection)
    {
        $this->userConnection = $userConnection;
    }
    public function onEncoderEvent(UserEncoderEvent $event) : void
    {
        $this->hash = $event->hash;
    }
    public function checkPreAuth(UserInterface $user)
    {

    }
    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
        if (!$this->hash) {
            return;
        }
        $this->userConnection->update('users',
            ['password' => $this->hash,'salt' => null],
            ['username' => $user->getUsername()],
            [
                'password' => \PDO::PARAM_STR,
                'salt'     => \PDO::PARAM_STR,
                'username' => \PDO::PARAM_STR,
            ]
        );
        //dd('checkPostAuth ' . $user->getUsername() . ' ' . $this->hash);
    }
}