<?php

namespace App\Project;

use Psr\Container\ContainerInterface;

class ProjectFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function create(string $projectClass) : Project
    {
        return $this->container->get($projectClass);

        //throw new \InvalidArgumentException('ProjectFactory::create ' . $projectClass);
    }
}