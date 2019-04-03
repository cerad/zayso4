<?php
namespace App\Reg\Team;

use App\Model\IRegTeam;
use App\Model\IPoolTeam;

/**
 * @property-read string regTeamId
 * @property-read string projectId
 *
 * @property-read string teamKey
 * @property-read int    teamNumber
 * @property-read string teamName
 * @property-read int    teamPoints // int or null
 *
 * @property-read string orgId
 * @property-read string orgView
 *
 * @property-read string program
 * @property-read string gender
 * @property-read string age
 * @property-read string division
 */
class RegTeam implements IRegTeam
{
    public $regTeamId;
    public $projectId;

    public $teamKey;
    public $teamNumber = -1;
    public $teamName;
    public $teamPoints;

    public $orgId;
    public $orgView;
    
    public $program;
    public $gender;
    public $age;
    public $division;

    /** @var IPoolTeam[] */
    public $poolTeams = [];

    public function getId() : string { return $this->regTeamId; }

    public function addPoolTeam(IPoolTeam $poolTeam)
    {
        $this->poolTeams[$poolTeam->getId()] = $poolTeam;
    }
    // Need this to get around read only stuff
    // isset is false on null
    private function init(array $data)
    {
        // Do this the long way for simplicity?
        if (isset($data['regTeamId'])) $this->regTeamId = $data['regTeamId'];
        if (isset($data['projectId'])) $this->projectId = $data['projectId'];

        if (isset($data['teamKey' ])) $this->teamKey  = $data['teamKey'];
        if (isset($data['teamName'])) $this->teamName = $data['teamKey'];

        if (isset($data['teamNumber'])) $this->teamNumber = (int)$data['teamNumber'];
        if (isset($data['teamPoints'])) $this->teamPoints = (int)$data['teamPoints'];

        if (isset($data['orgId']))   $this->orgId   = $data['orgId'  ];
        if (isset($data['orgView'])) $this->orgView = $data['orgView'];

        if (isset($data['program']))  $this->program  = $data['program' ];
        if (isset($data['gender']))   $this->gender   = $data['gender'  ];
        if (isset($data['age']))      $this->age      = $data['age'     ];
        if (isset($data['division'])) $this->division = $data['division'];

        $this->poolTeams = [];
    }
    static public function create(array $data) : RegTeam
    {
        $team = new self();
        $team->init($data);
        return $team;
    }
}
