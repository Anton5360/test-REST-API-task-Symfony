<?php

namespace App\Services\Interfaces;

use App\DataTransferObjects\API\V1\Authentication\LoginDTO;
use App\DataTransferObjects\API\V1\Authentication\RegisterPayloadDTO;
use App\DataTransferObjects\API\V1\Profile\UpdateUserDTO;
use App\Entity\Auth;
use App\Entity\User;

interface UserServiceInterface
{
    public function create(RegisterPayloadDTO $payload): void;

    public function findByAuthToken(string $token): ?User;

    public function delete(User $user): true;

    public function createAuthToken(User $user): Auth;

    public function findByCredentials(LoginDTO $credentials): ?User;

    public function update(User $user, UpdateUserDTO $values): void;

    public function findByEmail(string $email): ?User;
    public function findById(int $id): ?User;
}
