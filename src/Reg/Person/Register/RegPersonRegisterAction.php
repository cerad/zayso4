<?php

namespace App\Reg\Person\Register;

use App\Core\ActionInterface;
use App\Core\AuthenticationTrait;
use App\Core\RouterTrait;
use App\Project\Project;
use App\Reg\Person\RegPersonFinder;
use App\Reg\Person\RegPersonForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RegPersonRegisterAction implements ActionInterface
{
    use RouterTrait;
    use AuthenticationTrait;

    private $project;
    private $regPersonForm;
    private $regPersonFinder;

    public function __construct(
        Project $project,
        RegPersonFinder $regPersonFinder,
        RegPersonForm $regPersonForm)
    {
        $this->project = $project;
        $this->regPersonForm   = $regPersonForm;
        $this->regPersonFinder = $regPersonFinder;
    }
    public function __invoke(Request $request) : Response
    {
        // Make sure not already registered
        $user = $this->getUser();
        $regPerson = $this->regPersonFinder->findRegPerson($this->project->id,$user->personId);
        if ($regPerson) {
            return $this->redirectToRoute('reg_person_update');
        }
        $formData = [
            'id' => null,
        ];
        $regPersonForm = $this->regPersonForm;
        $regPersonForm->setData($formData);

        return new Response($this->render());
    }
    private function render() : string
    {
        $content = $this->regPersonForm->render();
        return $this->project->pageTemplate->render($content);
    }
}