<?php

namespace App\Services\Authentication\Interfaces;

use App\Entity\Auth;
use App\Entity\User;

interface AuthServiceInterface
{
    public function findByUser(User $user): ?Auth;

    public function createForUser(User $user): Auth;
}
