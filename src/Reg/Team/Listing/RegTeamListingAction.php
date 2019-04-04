<?php
declare(strict_types=1);

namespace App\Reg\Team\Listing;

use App\Core\ActionInterface;
use App\Core\AuthorizationTrait;
use App\Core\EscapeTrait;
use App\Core\RouterTrait;

use App\Project\Project;

use App\Pool\Team\PoolTeamFinder;
use App\Pool\Team\PoolTeams;

use App\Reg\Team\RegTeamFinder;
use App\Reg\Team\RegTeams;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegTeamListingAction implements ActionInterface
{
    use EscapeTrait;
    use RouterTrait;
    use AuthorizationTrait;

    private $project;
    private $regTeamFinder;
    private $poolTeamFinder;

    public function __construct(
        Project $project,
        RegTeamFinder  $regTeamFinder,
        PoolTeamFinder $poolTeamFinder
    )
    {
        $this->project = $project;
        $this->regTeamFinder  = $regTeamFinder;
        $this->poolTeamFinder = $poolTeamFinder;
    }
    public function __invoke(Request $request)
    {
        $criteria = [
            'projectIds' => [$this->project->id],
            'divisions'  => ['U10B']
        ];
        $regTeams  = $this->regTeamFinder->findRegTeams($criteria);
        $poolTeams = $this->poolTeamFinder->findPoolTeams($criteria);

        return new Response($this->render($regTeams,$poolTeams));
    }
    private function render(RegTeams $regTeams,PoolTeams $poolTeams) : string
    {
        $content = <<<EOT
<div class="container no-disc">
EOT;
        $content .= $this->renderRegTeams ($regTeams);
        $content .= $this->renderPoolTeams($poolTeams);

        $content .=  <<<EOT
</div> <!-- class="container no-disc" -->
<br>
<div class="panel-float-clear"></div>
EOT;
        return $this->project->pageTemplate->render($content);
    }
    private function renderRegTeams(RegTeams $regTeams) : string
    {
        // Would this be any cleaner as a twig template?
        $html = <<<EOT
<h3>Registered Team Listing</h3>
<table border="1">
  <tr><td>ID</td><td>Key</td><td>Number</td><td>Name</td><td>Org</td></tr>
EOT;
        foreach($regTeams as $regTeam) {
            $html .= <<<EOT
  <tr>
    <td>{$regTeam->regTeamId}</td>
    <td>{$regTeam->teamKey}</td>
    <td>{$regTeam->teamNumber}</td>
    <td>{$regTeam->teamName}</td>
    <td>{$regTeam->orgView}</td>
  </tr>
EOT;
        }
        $html .= <<<EOT
</table>
EOT;
        return $html;
    }
    private function renderPoolTeams(PoolTeams $poolTeams) : string
    {
        // Would this be any cleaner as a twig template?
        $html = <<<EOT
<h3>Pool Team Listing</h3>
<table border="1">
  <tr><td>ID</td><td>Pool</td><td>Type</td><td>Team</td><td>Slot</td><td>Reg Team</td></tr>
EOT;
        // Really should be escaping these or wrap in a view
        foreach($poolTeams as $poolTeam) {

            $regTeamName = $this->escape($poolTeam->regTeamName);

            $html .= <<<EOT
  <tr>
    <td>{$this->escape($poolTeam->poolTeamId)  }</td>
    <td>{$poolTeam->poolKey     }<br />{$poolTeam->poolView}</td>
    <td>{$poolTeam->poolTypeKey }<br />{$poolTeam->poolTypeView}</td>
    <td>{$poolTeam->poolTeamKey }<br />{$poolTeam->poolTeamView}</td>
    <td>{$poolTeam->poolSlotView}<br />{$poolTeam->poolTeamSlotView}</td>
    <td>{$regTeamName}</td>
  </tr>
EOT;
        }
        $html .= <<<EOT
</table>
EOT;
        return $html;
    }
}
