<?php

namespace App\Reg\Person\Register;

use App\Core\ActionInterface;
use App\Project\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegPersonRegisterAction implements ActionInterface
{
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }
    public function __invoke(Request $request) : Response
    {
        return new Response($this->render());
    }
    private function render() : string
    {
        $content = 'Register';
        return $this->project->pageTemplate->render($content);
    }
}