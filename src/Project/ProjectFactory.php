<?php

namespace App\Project;

class ProjectFactory
{
    private $projectLocator;

    public function __construct(ProjectLocator $projectLocator)
    {
        $this->projectLocator = $projectLocator;
    }
    public function create(string $projectClass) : Project
    {
        return $this->projectLocator->get($projectClass);
    }
}