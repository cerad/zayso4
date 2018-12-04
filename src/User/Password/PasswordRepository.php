<?php


namespace App\User\Password;

use App\Core\TokenGeneratorTrait;
use App\User\UserConnection;

class PasswordRepository
{
    use TokenGeneratorTrait;

    private $userConnection;

    public function __construct(UserConnection $userConnection)
    {
        $this->userConnection = $userConnection;
    }
    public function findByIdentifier(string $identifier) : ?array
    {
        $sql = 'SELECT id,name,username,email FROM users WHERE username = ? OR email = ?;';
        $stmt = $this->userConnection->executeQuery($sql,[$identifier,$identifier]);
        $row = $stmt->fetch();
        return $row ? $row : null;
    }
    public function findByToken(string $token) : ?array
    {
        $sql = 'SELECT id,name,username,email FROM users WHERE emailToken = ? OR passwordToken = ?;';
        $stmt = $this->userConnection->executeQuery($sql,[$token,$token]);
        $row = $stmt->fetch();
        return $row ? $row : null;
    }
    public function updatePasswordToken(int $id, ?string $passwordToken) : void
    {
        $this->userConnection->update('users',
            ['passwordToken' => $passwordToken],
            ['id' => $id]);
    }
    public function changePassword(int $id, ?string $password) : void
    {
        $this->userConnection->update('users',
            ['password' => $password, 'passwordToken' => null, 'salt' => null],
            ['id' => $id]);
    }
}