<?php

namespace App\Project;

use App\Core\ContainerTrait;
use Psr\Container\ContainerInterface;

class ProjectFactory
{
    use ContainerTrait;

    public function __construct()
    {
        //$this->container = $container;
    }
    public function create(string $projectClass) : Project
    {
        return $this->container->get($projectClass);

        //throw new \InvalidArgumentException('ProjectFactory::create ' . $projectClass);
    }
}