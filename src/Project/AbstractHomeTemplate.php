<?php

namespace App\Project;

use App\Core\AuthenticationTrait;
use App\Core\EscapeTrait;
use App\Core\RouterTrait;
use App\Reg\Person\RegPerson;
use App\Reg\Person\RegPersonFinder;
use App\Reg\Person\RegPersonViewDecorator;

abstract class AbstractHomeTemplate
{
    use EscapeTrait;
    use RouterTrait;
    use AuthenticationTrait;

    protected $project;

    /** @var RegPerson */
    protected $regPerson;
    protected $regPersonView;
    protected $regPersonFinder;

    public function __construct(
        Project $project,
        RegPersonFinder $regPersonFinder,
        RegPersonViewDecorator $regPersonView)
    {
        $this->project         = $project;
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
{$this->renderAysoInformation()}
{$this->renderAvailability()}
</div><div>
{$this->renderInstructions()}
</div><div>
{$this->renderHotelInformation()}
</div>
EOT;
    }
    abstract protected function renderHotelInformation() : string;
    abstract protected function renderAvailability()     : string;
    abstract protected function renderNotes()            : string;

    protected function renderInstructions() : string
    {
        $personView = $this->regPersonView;

        $isReferee = $personView->getCertBadge('CERT_REFEREE');
        if (!$isReferee) {
            return '';
        }
        return <<<EOT
<div id="clear-fix">
   <legend>Instructions for Referees</legend>
   <ul class="cerad-common-help ul_bullets">
     <li>Click on "<a href="{$this->generateUrl('schedule_official')}">Request Assignments</a>" under the "Referees" menu item above.</li>
     <li>On any open match, click on the position you'd like to request, e.g. REF, AR1, AR2</li>
     <li>Click "Submit" button"</li>
     <li>Check back on your schedule under "
       <a href="{$this->generateUrl('schedule_my')}">My Schedule</a>
       " under the "My Stuff" menu item above to see the assignments.
     </li>
     <li>Detailed instructions for self-assigning are available 
       <a href="{$this->generateUrl('app_detailed_instructions')}" target="_blank">by clicking here</a>.
     </li>
   </ul>
</div>
<hr>
EOT;
    }
    protected function renderAysoInformation()
    {
        $personView = $this->regPersonView;

        $regYearProject = $this->project->regYear;

        return <<<EOD
<table class="tableClass">
  <tr><th colspan="2" style="text-align: center;">AYSO Information</th></tr>
  <tr>
    <td>AYSO ID</td>
    <td>{$personView->fedId}</td>
  </tr><tr>
    <td>Section/Area/Region</td>
    <td class="{$personView->orgIdClass}">{$personView->orgId}</td>
  </tr><tr>
    <td>Membership Year</td>
    <td class="{$personView->getRegYearClass($regYearProject)}">{$personView->getRegYear($regYearProject)}</td>
  </tr><tr>
    <td>Safe Haven</td>
    <td class="{$personView->getCertClass('CERT_SAFE_HAVEN')}">{$personView->getCertBadge('CERT_SAFE_HAVEN')}</td>
  </tr><tr>
    <td>Referee Badge</td>
    <td class="{$personView->getCertClass('CERT_REFEREE')}">{$personView->getCertBadge('CERT_REFEREE')}</td>
  </tr><tr>
    <td>Concussion Aware</td>
    <td class="{$personView->getCertClass('CERT_CONCUSSION')}">{$personView->getCertBadge('CERT_CONCUSSION')}</td>
  </tr>
</table>
EOD;
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
    protected function renderRegistration() : string
    {
        $personView = $this->regPersonView;

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
    protected function renderAccountInformation()
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