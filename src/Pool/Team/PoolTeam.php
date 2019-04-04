<?php
declare(strict_types=1);

namespace App\Pool\Team;

use App\Model\IRegTeam;
use App\Model\IPoolTeam;

/**
 * @property-read string poolTeamId
 * @property-read string projectId
 *
 * @property-read string poolKey
 * @property-read string poolTypeKey
 * @property-read string poolTeamKey
 *
 * @property-read string poolView
 * @property-read string poolSlotView
 * @property-read string poolTypeView
 * @property-read string poolTeamView
 * @property-read string poolTeamSlotView
 *
 * @property-read string sourcePoolKeys
 * @property-read string sourcePoolSlot
 *
 * @property-read string program
 * @property-read string gender
 * @property-read string age
 * @property-read string division
 *
 * @property-read string regTeamId
 * @property-read string regTeamName
 * @property-read ?int   regTeamPoints
 */
class PoolTeam implements IPoolTeam
{
    public $poolTeamId;
    public $projectId;

    public $poolKey;
    public $poolTypeKey;
    public $poolTeamKey;

    public $poolView;
    public $poolSlotView;
    public $poolTypeView;
    public $poolTeamView;
    public $poolTeamSlotView;

    public $sourcePoolKeys; // Not currently used
    public $sourcePoolSlot;

    public $program;
    public $gender;
    public $age;
    public $division;

    public $regTeamId;
    public $regTeamName;
    public $regTeamPoints;

    /** @var IRegTeam[] */
    public $regTeam;

    public function getId() : string { return $this->poolTeamId; }

    // Need this to get around read only stuff
    // isset is false on null
    private function init(array $data)
    {
        // Do this the long way for simplicity?
        if (isset($data['poolTeamId'])) $this->poolTeamId = $data['poolTeamId'];
        if (isset($data['projectId' ])) $this->projectId  = $data['projectId'];

        if (isset($data['poolKey'    ])) $this->poolKey     = $data['poolKey'];
        if (isset($data['poolTypeKey'])) $this->poolTypeKey = $data['poolTypeKey'];
        if (isset($data['poolTeamKey'])) $this->poolTeamKey = $data['poolTeamKey'];

        if (isset($data['poolView'        ])) $this->poolView         = $data['poolView'];
        if (isset($data['poolSlotView'    ])) $this->poolSlotView     = $data['poolSlotView'];
        if (isset($data['poolTypeView'    ])) $this->poolTypeView     = $data['poolTypeView'];
        if (isset($data['poolTeamView'    ])) $this->poolTeamView     = $data['poolTeamView'];
        if (isset($data['poolTeamSlotView'])) $this->poolTeamSlotView = $data['poolTeamSlotView'];

        if (isset($data['sourcePoolKeys'])) $this->sourcePoolKeys = $data['sourcePoolKeys'];
        if (isset($data['sourcePoolSlot'])) $this->sourcePoolSlot = $data['sourcePoolSlot'];

        if (isset($data['program' ])) $this->program  = $data['program' ];
        if (isset($data['gender'  ])) $this->gender   = $data['gender'  ];
        if (isset($data['age'     ])) $this->age      = $data['age'     ];
        if (isset($data['division'])) $this->division = $data['division'];

        if (isset($data['regTeamId']))     $this->regTeamId     = $data['regTeamId'];
        if (isset($data['regTeamName']))   $this->regTeamName   = $data['regTeamName'];
        if (isset($data['regTeamPoints'])) $this->regTeamPoints = $data['regTeamPoints'];
    }
    public function __construct(array $data)
    {
        $this->init($data);
    }
    static public function create(array $data) : PoolTeam
    {
        return new self($data);
    }
}
