<?php
namespace App\User\Authen;

use App\Core\ActionInterface;

use App\Core\RouterTrait;
use App\User\UserProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class CallbackAction implements ActionInterface
{
    use RouterTrait;

    private $providerFactory;

    private $userProvider;

    private $tokenStorage;
    private $eventDispatcher;

    public function __construct(
        ProviderFactory          $providerFactory,
        UserProvider             $userProvider,
        TokenStorageInterface    $tokenStorage,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->userProvider    = $userProvider;
        $this->providerFactory = $providerFactory;
        $this->tokenStorage    = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }
    public function __invoke(Request $request) : Response
    {
        if (!$request->query->has('code')) {
            return $this->redirectToRoute('app_welcome');
        }
        
        $code         = $request->query->get('code');
        $providerName = $request->query->get('state');

        $provider = $this->providerFactory->create($providerName);
        
        $accessTokenData = $provider->getAccessToken($code);

        $userData = $provider->getUserInfoData($accessTokenData);
dump($userData);
        $email = $userData['email'];

        try {
            $user = $this->userProvider->loadUserByUsername($email);
            $this->loginUser($request,$user);
            return $this->redirectToRoute('app_welcome'); // app_home
        }
        catch (UsernameNotFoundException $e) {
        }
        return $this->redirectToRoute('app_welcome');
    }
    private function loginUser(Request $request, UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
    }
}
