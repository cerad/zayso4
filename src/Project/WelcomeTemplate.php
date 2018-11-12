<?php

namespace App\Project;

use App\Core\EscapeTrait;
use App\Core\RouterTrait;

abstract class WelcomeTemplate
{
    use EscapeTrait;
    use RouterTrait;

    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }
    public function render(string $loginForm) : string
    {
        $content = <<<EOT
  <div id="welcome">
    <legend>{$this->escape($this->project->welcomeMessage)}</legend>
  </div>
  {$this->renderNotes()}      
  {$this->renderUser($loginForm)}
  {$this->renderHelp()}
EOT;
        return $content;
    }
    protected function renderNotes() : string
    {
        return 'notes';
    }
    protected function renderUser($loginForm) : string
    {
        return <<<EOD
<legend>Sign In to Your zAYSO Account</legend>
{$loginForm}
EOD;
    }
    protected function renderHelp() : string
    {
        return <<<EOT
<div class="app_help">
  <legend>Need Help?</legend>
  <ul class="cerad-common-help">
    <li>
      Forgot your zAYSO account password?  
      <a href="{$this->generateUrl('user_password_reset_request')}">
        Click here to recover your zAYSO password.
      </a>
    </li>
    <li>
      Need to create an account? 
      <a href="{$this->generateUrl('user_create')}">
        Click here to create a new zAYSO account
      </a> .
    </li>
    <li>
      Once you create an account, you will be able to modify your information and availability.
    </li>
    <li>
      If you have comments or suggestions, please submit them by 
      <a href="mailto:web.ng2019@gmail.com?subject=Registration %20Feedback" target="_top">
        clicking here
      </a>.  
      Thank you for your support.
    </li>
  </ul>
</div>
EOT;
    }
}
