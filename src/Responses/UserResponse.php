<?php

namespace App\Responses;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserResponse extends JsonResponse
{
    public function __construct(User $user)
    {
        parent::__construct([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
            ],
        ]);
    }
}
