<?php

namespace App\Reg\Team\Listing;

use App\Core\ActionInterface;
use App\Core\AuthorizationTrait;
use App\Core\RouterTrait;
use App\Project\Project;

use App\Reg\Team\RegTeam;
use App\Reg\Team\RegTeamFinder;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegTeamListingAction implements ActionInterface
{
    use RouterTrait;
    use AuthorizationTrait;

    private $restrict = false;

    private $project;
    private $regTeamFinder;

    public function __construct(Project $project, RegTeamFinder $regTeamFinder)
    {
        $this->project = $project;
        $this->regTeamFinder = $regTeamFinder;
    }
    public function __invoke(Request $request)
    {
        $regTeams = $this->regTeamFinder->findRegTeams([
            'projectIds' => [$this->project->id],
            'divisions'  => ['U10B'],
        ]);

        return new Response($this->render($regTeams));
    }
    private function render($regTeams) : string
    {
        $content = <<<EOT
<div class="container no-disc">
<h3>Registered Team Listing</h3>
EOT;
        $content .= $this->renderRegTeams($regTeams);

        $content .=  <<<EOT
</div> <!-- class="container no-disc" -->
<br>
<div class="panel-float-clear"></div>
EOT;
        return $this->project->pageTemplate->render($content);
    }

    /**
     * @param $regTeams RegTeam[]
     */
    private function renderRegTeams($regTeams) : string
    {
        // Would this be any cleaner as a twig template?
        $html = <<<EOT
<table border="1">
  <tr><td>ID</td><td>Name</td></tr>
EOT;
        foreach($regTeams as $regTeam) {
            $html .= <<<EOT
  <tr>
    <td>{$regTeam->regTeamId}</td>
    <td>{$regTeam->teamName}</td>
  </tr>
EOT;

        }
        $html .= <<<EOT
</table>
EOT;
        return $html;
    }
}
