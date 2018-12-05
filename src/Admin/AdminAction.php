<?php

namespace App\Admin;

use App\Core\ActionInterface;
use App\Core\AuthorizationTrait;
use App\Core\RouterTrait;
use App\Project\Project;

use App\Project\ProjectContact;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAction implements ActionInterface
{
    use RouterTrait;
    use AuthorizationTrait;

    private $restrict = false;

    private $project;
    private $switchUserForm;

    public function __construct(Project $project, AdminSwitchUserForm $switchUserForm)
    {
        $this->project = $project;
        $this->switchUserForm = $switchUserForm;
    }
    public function __invoke(Request $request)
    {
        $this->switchUserForm->handleRequest($request);

        if ($this->switchUserForm->isValid()) {
            $formData = $this->switchUserForm->getData();
            $redirect = $formData['username'] ?
                $this->redirectToRoute('app_home',['_switch_user' => $formData['username']]) :
                $this->redirectToRoute('app_admin');
            return $redirect;
        }
        return new Response($this->render());
    }
    private function render() : string
    {
        $content = <<<EOT
<div class="container no-disc">
<h3>Administrative Functions</h3>
EOT;
        $content .= $this->renderMatchReporting();

        $content .= $this->renderScheduleManagement();

        $content .= $this->renderRefereeManagement();

        $content .= $this->renderTeamManagement();

        $content .= $this->renderAccountManagement();

        $content .= $this->renderCommunications();

        $content .=  <<<EOT
</div> <!-- class="container no-disc" -->
<br>
<div class="panel-float-clear"></div>
EOT;
        $content .= $this->switchUserForm->render();
        $content .= $this->renderAdminHelp();

        return $this->project->pageTemplate->render($content);
    }
    private function renderMatchReporting()
    {
        $html = <<<EOT
<div class="panel panel-default panel-float-left">
  <div class="panel-heading">
    <h1>Match Reporting</h1>
  </div>
  <div class="panel-body">
    <ul>
EOT;
        if ($this->isGranted('ROLE_SCORE_ENTRY') || !$this->restrict) {
            $html .= <<<EOT
      <li><a href="{$this->generateUrl('game_report_update',['projectSlug' => $this->project->slug,'gameNumber' => 11001])}">Enter Match Results</a></li>
EOT;
        }
        $html .= <<<EOT
<li><a href="{$this->generateUrl('results_poolplay')}">Pool Play</a></li>
<li><a href="{$this->generateUrl('results_medalround')}">Medal Round</a></li>
<li><a href="{$this->generateUrl('results_sportsmanship')}">Sportsmanship</a></li>
<li><a href="{$this->generateUrl('results_final')}">Final Standings</a></li>
    </ul>
  </div>
</div>

EOT;
        return $html;
    }
    private function renderScheduleManagement()
    {
        $html = <<<EOT
<div class="panel panel-default panel-float-left">
  <div class="panel-heading">
    <h1>Schedule Management</h1>
  </div>
  <div class="panel-body">
    <ul>
        <li><a href="{$this->generateUrl('schedule_game')}">View Game Schedule</a></li>
        <li><a href="{$this->generateUrl('schedule_team')}">View Team Schedule</a></li>
        <li><a href="{$this->generateUrl('schedule_game',['_format' => 'xls'])}">Export Game Schedule</a></li>
        <li><a href="{$this->generateUrl('schedule_medalroundcalc',['_format' => 'xls_qf'])}">Export Quarter-Finals Schedule for review</a></li>
        <li><a href="{$this->generateUrl('schedule_medalroundcalc',['_format' => 'xls_sf'])}">Export Semi-Finals Schedule for review</a></li>
        <li><a href="{$this->generateUrl('schedule_medalroundcalc',['_format' => 'xls_fm'])}">Export Finals Schedule for review</a></li>
        <li><a href="{$this->generateUrl('app_field_map')}" target="_blank">Field Map</a></li>
    </ul>
  </div>
</div>
EOT;
        return $html;
    }
    private function renderRefereeManagement()
    {
        if ($this->isGranted('ROLE_ASSIGNOR') || !$this->restrict) {
            $html = <<<EOT
<div class="panel panel-default panel-float-left">
  <div class="panel-heading">
    <h1>Referee Assignments</h1>
  </div>
  <div class="panel-body">
    <ul>
      <li><a href="{$this->generateUrl('schedule_official')}">View Referee Assignment Requests</a></li>
      <li><a href="{$this->generateUrl('schedule_official',['_format' => 'xls'])}">Export Referee Assignment Requests</a></li>
      <li><a href="{$this->generateUrl('schedule_assignor')}">View Assignor Assignments</a></li>
      <li><a href="{$this->generateUrl('game_official_summary')}">Export Referee Summary</a></li>
      <li><a href="{$this->generateUrl('app_detailed_instructions')}" target="_blank">Referee Self-Assignment Instruction</a></li>
    </ul>
  </div>
</div>
EOT;
        } else {
            $html = "";
        }
        return $html;
    }
    // Why does View Teams point to game_listing?
    private function renderTeamManagement()
    {
        $html = <<<EOT
<div class="panel panel-default panel-float-left">
  <div class="panel-heading">
    <h1>Team Management</h1>
  </div>
  <div class="panel-body">
    <ul>
      <li><a href="{$this->generateUrl('game_listing')}">View Teams</a></li>
      <li><a href="{$this->generateUrl('reg_team_export2')}">Export Teams</a></li>
EOT;
        if ($this->isGranted('ROLE_ADMIN') || !$this->restrict) {
            $html .= <<<EOT
      <li><a href="{$this->generateUrl('reg_team_import2')}">Import/Update Teams</a></li>
EOT;
        }
        $html .= <<<EOT
    </ul>
  </div>
</div>
EOT;
        return $html;
    }
    private function renderAccountManagement()
    {
        $html = <<<EOT
<div class="panel panel-default panel-float-left">
  <div class="panel-heading">
    <h1>Account Management</h1>
  </div>
  <div class="panel-body">
    <ul>
      <li><a href="{$this->generateUrl('reg_person_admin_listing')}">Manage Registered People</a></li>
      <li><a href="{$this->generateUrl('reg_person_admin_listing',['_format' => 'xls'])}">Export Registered People</a></li>
    </ul>
  </div>
</div>
EOT;

        return $html;
    }
    private function renderCommunications()
    {
        $html = <<<EOT
<div class="panel panel-default panel-float-left">
  <div class="panel-heading">
    <h1>Communications</h1>
  </div>
  <div class="panel-body">
    <ul>
      <li><a href="{$this->generateUrl('app_text_alerts')}">RainedOut Messaging</a></li>
      <li><a href="https://www.rainedout.net/admin/login.php?a={$this->project->rainedOutKey}" target="_blank">RainedOut Admin Login</a></li>
    </ul>
  </div>
</div>
EOT;
        return $html;
    }
    private function renderContact(ProjectContact $contact) : string
    {
        return <<<EOT
{$contact->name} 
 at <a href="mailto:{$contact->email}">{$contact->email}</a> 
 or at {$contact->phone}
EOT;
    }
    private function renderAdminHelp()
    {
        return <<<EOT
<legend>Need help?</legend>
<div class="app_help">
  <ul class="cerad-common-help">
    <ul class="ul_bullets">
      <li>For help with Referee Assignments, contact {$this->renderContact($this->project->assignor)}</li>
      <li>For help with Account Management,  contact {$this->renderContact($this->project->support)}</li>
      <li>For help with Schedule Management, contact {$this->renderContact($this->project->scheduler)}</li>
    </ul>
  </ul>
</div>
EOT;
    }
}
