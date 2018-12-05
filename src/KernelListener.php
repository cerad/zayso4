<?php
namespace App;

use App\Core\ContainerTrait;
use App\Core\LoggerTrait;
use App\Core\RouterTrait;
use App\Core\SecurityTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Kernel;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class KernelListener implements EventSubscriberInterface
{
    use LoggerTrait;
    use RouterTrait;
    use SecurityTrait;
    use ContainerTrait; // For views

    private $env;
    private $secureRoutes = true;
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST    => [['onRequest']],
            KernelEvents::CONTROLLER => [['onController']],
            KernelEvents::VIEW       => [['onView']],
            KernelEvents::EXCEPTION  => [['onException']],
            KernelEvents::RESPONSE   => [['onResponseP3P']],
        ];
    }
    public function __construct(string $env, bool $secureRoutes = true)
    {
        $this->env = $env;
        $this->secureRoutes = $secureRoutes;
    }
    /* ===================================================
     * Implements _role processing
     * Implements mandatory project_person_register
     */
    public function onRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) return;

        // Disable the listener in case of problems
        if (!$this->secureRoutes) {
            return;
        }
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return; // need this for debug bar profile nonsense
        };
        $request = $event->getRequest();
        $role = $request->attributes->get('_role');

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($role) {
                $event->setResponse($this->redirectToRoute('app_welcome'));
                $event->stopPropagation();
                return;
            }
            return;
        }
        if ($role && !$this->authChecker->isGranted($role)) { // die('isGranted failed ' . $role);
            $event->setResponse($this->redirectToRoute('app_welcome'));
            $event->stopPropagation();
            return;
        }
        // Make sure register is called at least once
        $user = $this->getUser();//dd($user);
        if ($user->registered !== false) {
            return;
        }
        if (1) return;  // Disable requirement
        // Allow this one through
        if ($request->attributes->get('_route') === 'reg_person_register') {
            return;
        }
        $event->setResponse($this->redirectToRoute('reg_person_register'));
        $event->stopPropagation();
        return;
    }
    public function onController(/** @noinspection PhpUnusedParameterInspection */
        FilterControllerEvent $event)
    {
        return;
    }

    /* =================================================================
     * Creates and renders a view
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        $viewAttrName = '_view';
        if ($request->attributes->has('_format'))
        {
            $viewAttrName .= '_' . $request->attributes->get('_format');
        }
        if (!$request->attributes->has($viewAttrName)) return;

        $viewServiceId = $request->attributes->get($viewAttrName);

        /** @var Callable $view */
        $view = $this->container->get($viewServiceId);

        $response = $view($request);

        $event->setResponse($response);
    }
    /* ==========================================
     * Need my own exception handler since the default one relies on twig
     *
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        if ($this->env !== 'prod') {
            return;
        }

        // Copied from Symfony KernelEventListener
        $exception = $event->getException();
        $this->logException($exception, sprintf('UNCAUGHT PHP Exception %s: "%s" at %s line %s',
            get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()
        ));

        // Just redirect to home, no real need for a fail whale
        $response = $this->redirectToRoute('app_welcome');

        $event->setResponse($response);
    }
    // Needed for iframes in some browsers
    public function onResponseP3P(FilterResponseEvent $event)
    {
        // P3P Policy
        $event->getResponse()->headers->set('P3P',
            'CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
    }
}