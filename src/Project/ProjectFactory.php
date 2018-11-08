<?php

namespace App\Project;


use App\Project\NG2019\NG2019Project;

class ProjectFactory
{
    public function create(string $slug) : Project
    {
        switch ($slug) {
            case 'ng2019': return new NG2019Project();
        }
        throw new \InvalidArgumentException('ProjectFactory::create ' . $slug);
    }
}