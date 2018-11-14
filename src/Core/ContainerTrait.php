<?php

namespace App\Core;

use Psr\Container\ContainerInterface;

trait ContainerTrait
{
    /** @var ContainerInterface */
    protected $container;

    /** @required */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}