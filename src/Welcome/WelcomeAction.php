<?php

namespace App\Welcome;

use App\Core\ActionInterface;
use App\Core\PageTemplate;
use App\User\Login\UserLoginForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WelcomeAction implements ActionInterface
{
    private $pageTemplate;
    private $userLoginForm;

    public function __construct(
        PageTemplate  $pageTemplate,
        UserLoginForm $userLoginForm
    )
    {
        $this->pageTemplate  = $pageTemplate;
        $this->userLoginForm = $userLoginForm;
    }

    public function __invoke(Request $request) : Response
    {
        return new Response($this->pageTemplate->render($this->userLoginForm->render()));
    }
}