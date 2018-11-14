<?php

namespace App\Project;

use App\Core\AuthenticationTrait;
use App\Core\EscapeTrait;
use App\Core\RouterTrait;
use App\Reg\Person\RegPerson;
use App\Reg\Person\RegPersonFinder;
use App\Reg\Person\RegPersonViewDecorator;

abstract class HomeTemplate
{
    use EscapeTrait;
    use RouterTrait;
    use AuthenticationTrait;

    //protected $project;
    /** @var RegPerson */
    protected $regPerson;
    protected $regPersonView;
    protected $regPersonFinder;

    public function __construct(
        //Project $project,
        RegPersonFinder $regPersonFinder,
        RegPersonViewDecorator $regPersonView)
    {
        //$this->project         = $project;
        $this->regPersonView   = $regPersonView;
        $this->regPersonFinder = $regPersonFinder;
    }
    public function render(RegPerson $regPerson) : string
    {
        $this->regPerson = $regPerson;
        $this->regPersonView->setRegPerson($regPerson);

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
    protected function renderCrewInformation() : string
    {
        $regPersonPersons = $this->regPersonFinder->findRegPersonPersons(
            $this->regPerson->projectId,
            $this->regPerson->personId);

        //dump($regPersonPersons);

        $html = <<<EOD
<table class="tableClass" >
  <tr><th colspan="2" style="text-align: center;">My Crew</th></tr>
EOD;
        foreach ($regPersonPersons as $regPersonPerson) {
            $html .= <<<EOD
<tr><td>{$regPersonPerson->role}</td><td>{$this->escape($regPersonPerson->memberName)}</td></tr>
EOD;
        }
        $html .= <<<EOD
<tr class="trAction"><td style="text-align: center;" colspan="2">
  <a href="{$this->generateUrl('reg_person_persons_update')}">
    Add/Remove People
  </a>
</td></tr>
</table>
EOD;

        return $html;
    }
    protected function renderTeamInformation() : string
    {
        $regPersonTeams = $this->regPersonFinder->findRegPersonTeams(
            $this->regPerson->projectId,
            $this->regPerson->personId);
        //dump($regPersonTeams);

        $html = <<<EOD
<table class="tableClass" >
  <tr><th colspan="2" style="text-align: center;">My Teams</th></tr>
EOD;

        foreach($regPersonTeams as $regPersonTeam) {
            $html .= <<<EOD
  <tr><td>{$regPersonTeam->role}</td><td>{$regPersonTeam->teamName}</td></tr>
EOD;
        }
        $html .= <<<EOD
  <tr class="trAction"><td style="text-align: center;" colspan="2">
    <a href="{$this->generateUrl('reg_person_teams_update')}">
        Add/Remove Teams
    </a>
  </td></tr>
</table>
EOD;
        return $html;
    }
    protected function renderRegistration()
    {
        $regPersonView = $this->regPersonView;

        return <<<EOD
<table class="tableClass">
  <tr><th colspan="2" style="text-align: center;">Registration Information</th></tr>
  <tr><td>Registration Name </td><td>{$this->escape($regPersonView->name) }</td></tr>
  <tr><td>Registration Email</td><td>{$this->escape($regPersonView->email)}</td></tr>
  <tr><td>Registration Phone</td><td>{$this->escape($regPersonView->phone)}</td></tr>
  <tr><td>Will Referee  </td><td>{$regPersonView->willRefereeBadge}</td></tr>
  <tr><td>Will Volunteer</td><td>{$regPersonView->willVolunteer}   </td></tr>
  <tr><td>Will Coach    </td><td>{$regPersonView->willCoach}       </td></tr>
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