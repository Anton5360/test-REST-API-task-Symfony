<?php

namespace App\Repository\Authentication\Interfaces;

use App\Entity\Auth;
use App\Entity\User;

interface AuthRepositoryInterface
{
    public function findByUser(User $user): ?Auth;

    public function create(User $user, string $token): void;
}
