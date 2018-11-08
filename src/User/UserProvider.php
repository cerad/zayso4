<?php

namespace App\User;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $userConnection;

    public function __construct(UserConnection $userConnection)
    {
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
        $userData['roles'] = explode(',',$userData['roles']);
        $user = User::create($userData);
        return $user;
        //dump($userData);die();


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