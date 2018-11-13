<?php

namespace App\Project;

use App\Core\AuthenticationTrait;
use App\Core\AuthorizationTrait;
use App\Core\EscapeTrait;
use App\Core\RouterTrait;
use App\Reg\Person\RegPerson;
use App\Reg\Person\RegPersonViewDecorator;

abstract class HomeTemplate
{
    use EscapeTrait;
    use RouterTrait;
    use AuthenticationTrait;

    protected $project;
    protected $personView;

    public function __construct(Project $project, RegPersonViewDecorator $personView)
    {
        $this->project    = $project;
        $this->personView = $personView;
    }
    public function render(RegPerson $regPerson) : string
    {
        dump($regPerson);
        $this->personView->setRegPerson($regPerson);

        return <<<EOT
{$this->renderNotes()}<br />
<div class="account-person-list">
{$this->renderAccountInformation()}
{$this->renderRegistration()}
{$this->renderCrewInformation()}
{$this->renderTeamInformation()}
</div>
EOT;
    }
    protected function renderCrewInformation()
    {
        return '<div>Crew</div><br />';
    }
    protected function renderTeamInformation()
    {
        return '<div>Team</div><br />';
    }
    protected function renderRegistration()
    {
        $personView = $this->personView;

        return <<<EOD
<table class="tableClass">
  <tr><th colspan="2" style="text-align: center;">Registration Information</th></tr>
  <tr><td>Registration Name </td><td>{$this->escape($personView->name) }</td></tr>
  <tr><td>Registration Email</td><td>{$this->escape($personView->email)}</td></tr>
  <tr><td>Registration Phone</td><td>{$this->escape($personView->phone)}</td></tr>
  <tr><td>Will Referee  </td><td>{$personView->willRefereeBadge}</td></tr>
  <tr><td>Will Volunteer</td><td>{$personView->willVolunteer}   </td></tr>
  <tr><td>Will Coach    </td><td>{$personView->willCoach}       </td></tr>
  <tr class="trAction"><td class="text-center" colspan="2">
    <a href="{$this->generateUrl('reg_person_update')}">
        Update My Plans or Availability
    </a>
  </td></tr>
</table>
EOD;
    }
    protected function renderNotes() : string
    {
        return '';
    }
    private function renderAccountInformation()
    {
        $user = $this->getUser();

        return <<<EOD
<table class="tableClass" >
  <tr><th colspan="2" style="text-align: center;">zAYSO Account Information</th></tr>
  <tr><td>Account Name </td><td>{$user->name}</td></tr>
  <tr><td>Account User </td><td>{$user->username}</td></tr>
  <tr><td>Account Email</td><td>{$user->email}</td></tr>
</table>
EOD;
    }
}