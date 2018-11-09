<?php

namespace App\User\Authen;

use App\User\Authen\Provider\AbstractProvider;
use Symfony\Component\Routing\RouterInterface;

class ProviderFactory
{
    private $router;
    private $providers;

    public function __construct(RouterInterface $router, array $providers)
    {
        $this->router    = $router;
        $this->providers = $providers;
    }
    public function create(string $providerName) : AbstractProvider
    {
        $params = isset($this->providers[$providerName]) ? $this->providers[$providerName] : null;

        if (!$params) {
            throw new \InvalidArgumentException;
        }

        $providerClass = 'App\\User\\Authen\\Provider\\' . ucfirst($providerName) . 'Provider';

        return new $providerClass($this->router,$params['client_id'],$params['client_secret']);
    }
}