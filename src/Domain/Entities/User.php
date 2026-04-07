<?php

namespace App\Domain\Entities;

class User
{
    private ?int $id;
    private string $username;
    private string $password;
    private string $role;

    public function __construct(string $username, string $password, string $role, ?int $id = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
