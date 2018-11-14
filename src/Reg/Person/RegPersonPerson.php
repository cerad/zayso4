<?php
namespace App\Reg\Person;

/**
 * @property-read string $role
 * @property-read string $projectId
 * @property-read string $managerId
 * @property-read string $managerName
 * @property-read string $memberId
 * @property-read string $memberName
 */
final class RegPersonPerson
{
    public $projectId;
    
    public $role;

    public $managerId;
    public $managerName;
    
    public $memberId;
    public $memberName;

    private function init(array $data) : void
    {
        $this->role        = $data['role'];
        $this->projectId   = $data['projectId'];
        $this->managerId   = $data['managerId'];
        $this->managerName = $data['managerName'];
        $this->memberId    = $data['memberId'];
        $this->memberName  = $data['memberName'];
    }
    static public function create(array $data) : RegPersonPerson
    {
        $item = new self();
        $item->init($data);
        return $item;
    }
}