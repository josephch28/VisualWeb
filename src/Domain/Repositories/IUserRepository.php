<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;

interface IUserRepository
{
    public function findByCredentials(string $username, string $password): ?User;
}
