<?php
namespace App\Reg\Person;


/**
 * @property-read integer id
 * @property-read string  projectId
 * @property-read string  personId
 *
 * Virtual
 * @property-read boolean isReferee
 * @property-read string  refereeBadge
 * @property-read string  refereeBadgeUser
 */
class RegPerson
{
    public $id; // autoinc database

    public $projectId;
    public $personId;
    public $orgId;
    public $fedId;
    public $regYear;
    public $registered;
    public $verified;

    public $name;
    public $email;
    public $phone;
    public $gender;
    public $dob;
    public $age;
    public $shirtSize;

    public $notes;
    public $notesUser;

    /** @var RegPersonRole[] */
    public $roles = [];

    public $avail = [];

    // Do this just to avoid property read only errors
    private function init($data) : void
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;

        $this->projectId = $data['projectId'];
        $this->personId  = $data['personId'];

        $this->orgId   = $data['orgId'];
        $this->fedId   = $data['fedId'];
        $this->regYear = $data['regYear'];

        $this->registered = isset($data['registered']) ? (bool)$data['registered'] : false;
        $this->verified   = isset($data['verified'  ]) ? (bool)$data['verified']   : false;

        $this->name      = $data['name'];
        $this->email     = $data['email'];
        $this->phone     = $data['phone'];
        $this->gender    = $data['gender'];
        $this->dob       = $data['dob'];
        $this->age       = isset($data['age']) ? (int)$data['age'] : null;;
        $this->shirtSize = $data['shirtSize'];

        $this->notes     = $data['notes'];
        $this->notesUser = $data['notesUser'];

        $this->avail     = $data['avail'];

        foreach($data['roles'] as $roleData) {
            $role = RegPersonRole::create($roleData);
            $this->roles[$role->role] = $role;
        }

    }
    static public function create($data) : RegPerson
    {
        $regPerson = new self();
        $regPerson->init($data);
        return $regPerson;
    }
    public function addRole(RegPersonRole $role)
    {
        $this->roles[$role->role] = $role;
    }
    public function addCert(RegPersonRole $cert)
    {
        $this->roles[$cert->role] = $cert;
    }
    public function removeRole($roleKey)
    {
        $roleKey = is_object($roleKey) ? $roleKey->role : $roleKey;

        if (isset($this->roles[$roleKey])) {
            unset($this->roles[$roleKey]);
        }
        return $this;
    }
    public function removeCert($certKey)
    {
        $certKey = is_object($certKey) ? $certKey->role : $certKey;

        if (isset($this->roles[$certKey])) {
            unset($this->roles[$certKey]);
        }
        return $this;
    }
    public function hasRole($roleKey)
    {
        return isset($this->roles[$roleKey]) ? true : false;
    }
    public function hasCert($certKey)
    {
        return isset($this->roles[$certKey]) ? true : false;
    }

    /**
     * @param  string $certKey
     * @param  bool   $create
     * @return RegPersonRole|null
     */
    public function getCert($certKey,$create = false)
    {
        if (isset( $this->roles[$certKey])) {
            return $this->roles[$certKey];
        }
        if (!$create) {
            return null;
        }
        $cert = new RegPersonRole();
        $cert->active = false;
        $cert->role = $certKey;
        return $cert;
    }
    /**
     * @param  string $roleKey
     * @param  bool   $create
     * @return RegPersonRole|null
     */
    public function getRole($roleKey,$create = false)
    {
        if (isset( $this->roles[$roleKey])) {
            return $this->roles[$roleKey];
        }
        if (!$create) {
            return null;
        }
        $role = new RegPersonRole();
        $role->role = $roleKey;
        return $role;
    }

    /**
     * @return RegPersonRole[]
     */
    public function getRoles()
    {
        $roles = [];
        foreach($this->roles as $roleKey => $role) {
            if (substr($roleKey,0,5) === 'ROLE_') {
                $roles[$roleKey] = $role;
            }
        }
        return $roles;
    }
    public function __get($name)
    {
        switch ($name) {
            case 'isReferee': 
                return isset($this->roles['ROLE_REFEREE']) ? true : false;
            case 'refereeBadge':
                return isset($this->roles['CERT_REFEREE']) ? $this->roles['CERT_REFEREE']->badge : null;
            case 'refereeBadgeUser':
                return isset($this->roles['CERT_REFEREE']) ? $this->roles['CERT_REFEREE']->badgeUser : null;
        }
    }

            /**
     * @param  array $data
     * @return RegPerson
     */
    static public function createFromArray($data)
    {
        $item = new self();

        $item->loadFromArray($data);
        
        $item->avail = isset($data['avail']) ? unserialize($data['avail']) : [];
        
        return $item;
    }
}