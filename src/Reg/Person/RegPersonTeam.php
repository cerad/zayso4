<?php

namespace App\Reg\Person;

/**
 * @property-read string $role
 * @property-read string $projectId
 * @property-read string $managerId
 * @property-read string $teamId
 * @property-read string $teamName
 */
class RegPersonTeam
{
    public $projectId;

    public $role = 'Family';

    public $managerId;

    public $teamId;
    public $teamName;

    private function init(array $data) : void
    {
        $this->role      = $data['role'];
        $this->projectId = $data['projectId'];
        $this->managerId = $data['managerId'];
        $this->teamId    = $data['teamId'];
        $this->teamName  = $data['teamName'];
    }
    static public function create(array $data) : RegPersonTeam
    {
        $item = new self();
        $item->init($data);
        return $item;
    }
}