<?php

namespace App\Reg\Person\Update;

use App\Core\ActionInterface;
use App\Core\AuthenticationTrait;
use App\Core\RouterTrait;
use App\Project\Project;
use App\Reg\Person\RegPersonFinder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RegPersonUpdateAction implements ActionInterface
{
    use RouterTrait;
    use AuthenticationTrait;

    private $project;
    private $regPersonFinder;

    public function __construct(Project $project, RegPersonFinder $regPersonFinder)
    {
        $this->project = $project;
        $this->regPersonFinder = $regPersonFinder;
    }
    public function __invoke(Request $request) : Response
    {
        // Make sure not already registered
        $user = $this->getUser();
        $regPerson = $this->regPersonFinder->findRegPerson($this->project->id,$user->personId);
        if (!$regPerson) {
            return $this->redirectToRoute('reg_person_register');
        }
        return new Response($this->render());
    }
    private function render() : string
    {
        $content = 'Reg Person Update';
        return $this->project->pageTemplate->render($content);
    }
}