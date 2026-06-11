<?php

namespace App\Domain\User;

use Helix\Database\Repository;

class UserRepository extends Repository
{
    protected string $table = 'users';
    protected string $entityClass = User::class;
}
