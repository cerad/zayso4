<?php
namespace App\Admin;

use App\Core\AbstractForm;

use App\Project\Project;
use App\Reg\Person\RegPersonFinder;
use Symfony\Component\HttpFoundation\Request;

class AdminSwitchUserForm extends AbstractForm
{
    private $project;
    private $regPersonFinder;
    
    public function __construct(
        Project $project,
        RegPersonFinder $regPersonFinder
    ) {
        $this->project = $project;
        $this->regPersonFinder = $regPersonFinder;
    }
    public function handleRequest(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return;
        }
        $this->isPost = true;

        $errors = [];

        $data = $request->request->all();

        $this->formData['username'] = $this->filterString($data,'username');

        $this->formDataErrors = $errors;
    }
    // TODO: Maybe add project selection?
    public function render() : string
    {
        if (!$this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            if (0) return '';
        }
        $projectId = $this->project->id;

        $userChoices = array_merge(
            [null => 'Switch To User'],
            $this->regPersonFinder->findUserChoices($projectId)
        );
        $html = <<<EOD
<form method="post" action="{$this->generateUrl('app_admin')}" class="form-inline" role="form">
  <div class="form-group col-xs-12">
      <label class="form-label" for="username">User</label>
      {$this->renderInputSelect($userChoices,null,'username','username',null)}
  <button type="submit" class="btn btn-sm btn-primary">Switch To User</button>
  </div>
{$this->renderFormErrors()}
</form>
<br>
<br>
EOD;
        return $html;
    }
}
