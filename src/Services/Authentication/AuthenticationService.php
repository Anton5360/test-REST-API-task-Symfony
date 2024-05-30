<?php

namespace App\Services\Authentication;

use App\Entity\Auth;
use App\Entity\User;
use App\Repository\Authentication\Interfaces\AuthRepositoryInterface;
use App\Services\Authentication\Interfaces\AuthServiceInterface;
use App\Services\Authentication\Interfaces\AuthTokenGeneratorServiceInterface;

final readonly class AuthenticationService implements AuthServiceInterface
{
    public function __construct(
        private AuthRepositoryInterface            $authRepository,
        private AuthTokenGeneratorServiceInterface $authTokenGeneratorService,
    ) {
    }

    public function findByUser(User $user): ?Auth
    {
        return $this->authRepository->findByUser($user);
    }

    public function createForUser(User $user): Auth
    {
        $this->authRepository->create(
            user: $user,
            token: $this->authTokenGeneratorService->generate(length: 40)
        );

        return $this->authRepository->findByUser($user);
    }
}
