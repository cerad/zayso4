<?php

namespace App\User\Authen;

use App\Core\ActionInterface;
use App\Core\RouterTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConnectAction implements ActionInterface
{
    use RouterTrait;

    private $providerFactory;

    public function __construct(ProviderFactory $providerFactory)
    {
        $this->providerFactory = $providerFactory;
    }
    public function __invoke(Request $request, string $providerName) : Response
    {
        $provider = $this->providerFactory->create($providerName);

        return $this->redirect($provider->getAuthorizationUrl());
    }
}