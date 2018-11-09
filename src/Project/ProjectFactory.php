<?php

namespace App\Project;


use App\Project\NG2019\NG2019Project;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProjectFactory
{
    private $router;
    private $authChecker;

    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authChecker)
    {
        $this->router = $router;
        $this->authChecker = $authChecker;
    }

    public function create(string $slug) : Project
    {
        switch ($slug) {
            case 'ng2019': return new NG2019Project($this->router,$this->authChecker);
        }
        throw new \InvalidArgumentException('ProjectFactory::create ' . $slug);
    }
}