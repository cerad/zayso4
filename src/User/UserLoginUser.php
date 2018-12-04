<?php
namespace App\User;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLoginUser
{
    private $eventDispatcher;

    private $securityTokenStorage;

    private $firewallName; // main
    
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TokenStorageInterface    $securityTokenStorage,
        $firewallName = 'main'
    )
    {
        $this->firewallName          = $firewallName;
        $this->eventDispatcher       = $eventDispatcher;
        $this->securityTokenStorage  = $securityTokenStorage;
    }
    // TODO consider loading user from user provider
    public function loginUser(Request $request, UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, null, $this->firewallName, $user->getRoles());
        
        $this->securityTokenStorage->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        
        $this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
    }
}
