<?php /** @noinspection PhpUnusedPrivateFieldInspection */

namespace App\User;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @property-read string $name
 * @property-read string $email
 * @property-read string $username
 * @property-read string $personId
 * @property-read array  $roles
 */
class User implements UserInterface, \Serializable
{
    private $id;

    public $name;
    public $email;
    public $username;
    public $personId;

    private $salt;
    private $password;
    private $passwordToken;

    private $enabled = true;
    private $locked  = false;
    
    public $roles = ['ROLE_USER'];

    // Not used but in the database table
    //private $providerKey;
    //private $projectId;

    // Dynamically set via user provider
    public $registered; // for current project, still need?

    // For the UserInterface
    public function getRoles()
    {
        return $this->roles;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getSalt()
    {
        return $this->salt;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function eraseCredentials()
    {
    }
    public function isEnabled()
    {
        return $this->enabled;
    }
    public function serialize()
    {
        return serialize(array(
            $this->id,         // For refreshing
            $this->salt,
            $this->password,
            $this->username,   // Debugging
        ));
    }
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->id,
            $this->salt,
            $this->password,
            $this->username
            ) = $data;

        return;
    }
    // Try to make these go away
    //public function getProjectId()
    //{
    //    return $this->projectId;
    //}
    public function getPersonId()
    {
        return $this->personId;
    }
    public function getPersonName()
    {
        return $this->name;
    }
    private function init(array $data) : void
    {
        $this->name       = $data['name'];
        $this->username   = $data['username'];
        $this->email      = $data['email'];
        $this->salt       = $data['salt'];
        $this->password   = $data['password'];
        $this->enabled    = $data['enabled'];
        $this->locked     = $data['locked'];
        $this->personId   = $data['personId'];
        $this->roles      = $data['roles'];
        $this->registered = $data['registered'];
    }
    public static function create(array $data) : User
    {
        $user = new self();
        $user->init($data);
        return $user;
    }
}
