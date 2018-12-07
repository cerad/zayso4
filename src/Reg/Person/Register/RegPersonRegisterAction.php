<?php

namespace App\Reg\Person\Register;

use App\Core\ActionInterface;
use App\Core\AuthenticationTrait;
use App\Core\RouterTrait;
use App\Project\Project;
use App\Project\Projects;
use App\Reg\Person\RegPersonFinder;
use App\Reg\Person\RegPersonForm;
use App\Reg\RegConnection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RegPersonRegisterAction implements ActionInterface
{
    use RouterTrait;
    use AuthenticationTrait;

    private $project;
    private $projects;
    private $regPersonForm;
    private $regPersonFinder;
    private $regConnection;

    public function __construct(
        Project  $project,
        Projects $projects,
        RegPersonFinder $regPersonFinder,
        RegPersonForm $regPersonForm,
        RegConnection $regConnection)
    {
        $this->project  = $project;
        $this->projects = $projects;
        $this->regPersonForm   = $regPersonForm;
        $this->regPersonFinder = $regPersonFinder;
        $this->regConnection   = $regConnection;
    }
    public function __invoke(Request $request) : Response
    {
        // Make sure not already registered
        $user = $this->getUser();
        $regPerson = $this->regPersonFinder->findRegPerson($this->project->id,$user->personId);
        if ($regPerson) {
            //return $this->redirectToRoute('reg_person_update');
        }
        $formData = $this->regPersonForm->getData();
        $formData['regName']  = $user->name; // Make sure it is unique
        $formData['regEmail'] = $user->email;

        $formData = $this->mergePreviousRegistration($formData,$user->personId);

        $this->regPersonForm->setData($formData);

        $this->regPersonForm->handleRequest($request);

        return new Response($this->render());
    }
    private function mergePreviousRegistration(array $formData, string $personId) : array
    {
        $projectId = $this->projects->findLatestRegisteredProjectId($personId);
        if (!$projectId) {
            return $formData;
        }
        $regPerson = $this->regPersonFinder->findRegPerson($projectId,$personId);
        $formData['regPhone'] = $regPerson->phone;
        $formData['fedId']    = $regPerson->fedId;
        $formData['orgId']    = $regPerson->orgId;

        $formData['refereeBadge']     = $regPerson->refereeBadge;
        $formData['refereeBadgeUser'] = $regPerson->refereeBadgeUser;
        $formData['shirtSize']        = $regPerson->shirtSize;

        // Gender dob age ???

        // Copy any certs

        dump($regPerson);
        return $formData;

    }
    private function render() : string
    {
        $content = $this->regPersonForm->render();
        return $this->project->pageTemplate->render($content);
    }
}