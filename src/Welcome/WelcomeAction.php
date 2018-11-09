<?php

namespace App\Welcome;

use App\Core\ActionInterface;
use App\Project\Project;
use App\User\Login\UserLoginForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WelcomeAction implements ActionInterface
{
    private $project;
    private $userLoginForm;

    public function __construct(
        Project       $project,
        UserLoginForm $userLoginForm
    )
    {
        $this->project       = $project;
        $this->userLoginForm = $userLoginForm;
    }

    public function __invoke(Request $request) : Response
    {
        $welcomeTemplate = $this->project->welcomeTemplate;

        $pageTemplate = $this->project->pageTemplate;

        return new Response($pageTemplate->render($welcomeTemplate->render($this->userLoginForm->render())));
    }
}