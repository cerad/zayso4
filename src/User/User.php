<?php /** @noinspection PhpUnusedPrivateFieldInspection */

namespace App\User;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, \Serializable
{
    private $id;
    private $name;
    private $email;
    private $username;
    private $personId;

    private $salt;
    private $password;
    private $passwordToken;

    private $enabled = true;
    private $locked  = false;
    
    private $roles = ['ROLE_USER'];

    // Not used but in the database table
    private $providerKey;

    // Dynamically set via user provider
    private $projectId;
    private $registered;

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
    public function getProjectId()
    {
        return $this->projectId;
    }
    public function getPersonId()
    {
        return $this->personId;
    }
    public function getPersonName()
    {
        return $this->name;
    }
    public function getRegPersonId()
    {
        return $this->projectId . ':' . $this->personId;
    }
    public static function create(array $data) : User
    {
        $user = new self();

        $user->name     = $data['name'];
        $user->username = $data['username'];
        $user->email    = $data['email'];
        $user->salt     = $data['salt'];
        $user->password = $data['password'];
        $user->enabled  = $data['enabled'];
        $user->locked   = $data['locked'];
        $user->personId = $data['personId'];
        $user->roles    = $data['roles'];

        return $user;
    }
}
