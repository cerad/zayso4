<?php

namespace App\Home;

use App\Core\ActionInterface;
use App\Core\AuthenticationTrait;
use App\Project\Project;
use App\Reg\Person\RegPersonFinder;
use Symfony\Component\HttpFoundation\Response;

class HomeAction implements ActionInterface
{
    use AuthenticationTrait;

    private $project;

    private $regPersonFinder;

    public function __construct(
        Project                $project,
        RegPersonFinder        $regPersonFinder)
    {
        $this->project                = $project;
        $this->regPersonFinder        = $regPersonFinder;
    }
    public function __invoke() : Response
    {
        $user = $this->getUser();

        $regPerson = $this->regPersonFinder->findByProjectPerson($this->project->id,$user->personId);

        $homeTemplate = $this->project->homeTemplate;
        $pageTemplate = $this->project->pageTemplate;

        return new Response($pageTemplate->render($homeTemplate->render($regPerson)));
    }
}