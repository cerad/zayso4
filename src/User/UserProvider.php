<?php

namespace App\User;

use App\Project\Project;
use App\Reg\RegConnection;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $project;
    private $regConnection;
    private $userConnection;

    public function __construct(
        UserConnection $userConnection,
        RegConnection  $regConnection,
        Project $project)
    {
        $this->project = $project;
        $this->regConnection  = $regConnection;
        $this->userConnection = $userConnection;
    }
    private function loadUserFromDatabase(string $username)
    {
        $qb = $this->userConnection->createQueryBuilder();

        $qb->addSelect([
            'user.id           AS id',
            'user.username     AS username',
            'user.email        AS email',
            'user.salt         AS salt',
            'user.password     AS password',
            'user.roles        AS roles',

            'user.name      AS name',
            'user.enabled   AS enabled',
            'user.locked    AS locked',
            'user.personKey AS personId',
        ]);
        $qb->from('users', 'user');

        $qb->where(('user.username = ? OR user.email = ? OR user.providerKey = ?'));

        $qb->setParameters([$username,$username,$username]);

        $userData = $qb->execute()->fetch();
        if (!$userData) {
            throw new UsernameNotFoundException('User Not Found: ' . $username);
        }
        $userData['registered'] = false;
        $userData['roles'] = explode(',',$userData['roles']);

        // See if registered and with roles
        $sql = <<<EOT
SELECT
    regPerson.registered,
    regPersonRole.role,
    regPersonRole.active
FROM projectPersons AS regPerson
LEFT JOIN projectPersonRoles AS regPersonRole ON regPersonRole.projectPersonId = regPerson.id
WHERE regPerson.personKey = ? AND regPerson.projectKey = ?
ORDER BY role
EOT;
        $stmt = $this->regConnection->executeQuery($sql,[$userData['personId'],$this->project->id]);
        while($row = $stmt->fetch()) {
            $userData['registered'] = $row['registered'] ? true : false;
            if ($row['active']) {
                $role = $row['role'];
                if (!\in_array($role,$userData['roles'])) {
                    $userData['roles'][] = $role;
                }
            }
        }
        $userData['registered'] = false; // testing
        // And create
        $user = User::create($userData);
        return $user;
    }
    public function loadUserByUsername($username) : User
    {
        $user = $this->loadUserFromDatabase($username);
        return $user;
    }
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }
    public function supportsClass($class)
    {
        $userClass = User::class;
        return ($class instanceOf $userClass) ? true: false;
    }

}