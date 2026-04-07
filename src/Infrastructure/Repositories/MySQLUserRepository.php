<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Domain\Repositories\IUserRepository;
use App\Infrastructure\Database\PDOConnection;
use PDO;

class MySQLUserRepository implements IUserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function findByCredentials(string $username, string $password): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $row = $stmt->fetch();
        
        if ($row) {
            return new User($row['username'], $row['password'], $row['role'], $row['id']);
        }
        
        return null;
    }
}
