<?php
namespace App\Reg\Person;


/**
 * @property-read integer id
 * @property-read string  projectId
 * @property-read string  personId
 *
 * @property-read array  roles
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
    public $registered = false;
    public $verified   = false;

    public $name;
    public $email;
    public $phone;
    public $gender;
    public $dob;
    public $age;
    public $shirtSize;

    public $notes;
    public $notesUser;

    public $roles = [];

    public $avail = [];

    public $plans = [];

    // Want to add this to determine latest registration
    public $createdAt;

    static private $map = [
        'id'         => 'autoinc',
        'projectId'  => 'string',
        'personId'   => 'string',
        'orgId'      => 'string',
        'fedId'      => 'string',
        'regYear'    => 'string',
        'registered' => 'bool',
        'verified'   => 'bool',

        'name'      => 'string',
        'email'     => 'string',
        'phone'     => 'string',
        'gender'    => 'string',
        'dob'       => 'date',
        'age'       => 'int',
        'shirtSize' => 'string',

        'notes'     => 'string',
        'notesUser' => 'string',
        'createdAt' => 'datetime',

        'roles' => 'arrayx',
        'avail' => 'array',
        'plans' => 'array',
    ];

    // Provides access for persistence, forms and creation
    // Starting to rediscover why having an array of property names and types is handy
    public function toArray() : array
    {
        $data = [];
        foreach(self::$map as $key => $type) {
            $data[$key] = $this->$key;
        }
        return $data;
        /*
        $data = [
            'id'         => $this->id,
            'projectId'  => $this->projectId,
            'personId'   => $this->personId,
            'orgId'      => $this->orgId,
            'fedId'      => $this->fedId,
            'regYear'    => $this->regYear,
            'registered' => $this->registered,
            'verified'   => $this->verified,

            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'gender'    => $this->gender,
            'dob'       => $this->dob,
            'age'       => $this->age,
            'shirtSize' => $this->shirtSize,

            'notes'     => $this->notes,
            'notesUser' => $this->notesUser,
            'createdAt' => $this->createdAt,

            'roles' => $this->roles,
            'avail' => $this->avail,
            'plans' => $this->plans,
        ];

        return $data; */
    }
    // Do this just to avoid property read only errors
    /*
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

        $this->plans     = $data['plans'];
        $this->avail     = $data['avail'];

        foreach($data['roles'] as $roleData) {
            $role = RegPersonRole::create($roleData);
            $this->roles[$role->role] = $role;
        }

    } */
    // Needs more work, should we even allow updates or require new objects???
    public function fromArray(array $data) : void
    {
        $data = array_merge($this->toArray(),$data);
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
    }
    /**
     * Should we do type conversions here or require calling stuff to do it?
     */
    static public function create($data) : RegPerson
    {
        $item = new self();
        foreach(self::$map as $key => $type) {
            if (array_key_exists($key,$data)) {
                switch($type) {
                    case 'int':     $item->$key = (int )$data[$key]; break;
                    case 'bool':    $item->$key = (bool)$data[$key]; break;
                    case 'autoinc': $item->$key = $data[$key] ? (int)$data[$key] : null; break; // trinary nonsense
                    case 'arrayx':  break;
                    default:
                        $item->$key = $data[$key];
                }
            }
        }
        if (isset($data['roles'])) {
            $rolesProp = 'roles';
            foreach($data['roles'] as $roleData) {
                $role = is_array($roleData) ? RegPersonRole::create($roleData) : $roleData;
                $item->$rolesProp[$role->role] = $role;
            }
        }
        return $item;
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
        return null;
    }

}