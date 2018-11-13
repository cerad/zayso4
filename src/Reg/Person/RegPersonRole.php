<?php
namespace App\Reg\Person;

class RegPersonRole
{
    public $id;          // autoinc
    public $regPersonId; // autoinc

    public $projectId; // Not in database
    public $personId;  // Not in database
    
    public $role;
    public $roleDate;

    public $badge;
    public $badgeUser;
    public $badgeDate;
    public $badgeExpires;

    public $active   = true;
    public $approved = false;
    public $verified = false;
    public $ready    = true;

    public $misc;
    public $notes;

    private function init(array $data) : void
    {
        $this->role     = $data['role'];
        $this->roleDate = $data['roleDate'];;

        $this->badge        = $data['badge'];;
        $this->badgeUser    = $data['badgeUser'];;
        $this->badgeDate    = $data['badgeDate'];;
        $this->badgeExpires = $data['badgeExpires'];;
    }
    static public function create(array $data) : RegPersonRole
    {
        $regPersonRole = new self();
        $regPersonRole->init($data);
        return $regPersonRole;
    }
}