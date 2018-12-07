<?php

namespace App\Reg\Person\Update;

use App\Core\ActionInterface;
use App\Core\AuthenticationTrait;
use App\Core\EscapeTrait;
use App\Core\RouterTrait;
use App\Project\Project;
use App\Reg\Person\RegPersonFinder;
use App\Reg\Person\RegPersonForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RegPersonUpdateAction implements ActionInterface
{
    use EscapeTrait;
    use RouterTrait;
    use AuthenticationTrait;

    private $project;
    private $regPersonForm;
    private $regPersonFinder;

    public function __construct(
        Project         $project,
        RegPersonForm   $regPersonForm,
        RegPersonFinder $regPersonFinder)
    {
        $this->project         = $project;
        $this->regPersonForm   = $regPersonForm;
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
        // Merge in person data
        $form = $this->regPersonForm;
        $formData = array_merge($form->getData(),$regPerson->toArray());
        $form->setData($formData);

        // And process
        $form->handleRequest($request);
        if ($form->isValid()) {

        }
        return new Response($this->render());
    }
    private function render() : string
    {
        $content = <<<EOD
<legend>Register for {$this->escape($this->project->title)}</legend><br/>
{$this->regPersonForm->render()}
EOD;
        return $this->project->pageTemplate->render($content);
    }
}