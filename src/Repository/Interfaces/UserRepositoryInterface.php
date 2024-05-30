<?php

namespace App\Repository\Interfaces;

use App\DataTransferObjects\API\V1\Authentication\RegisterPayloadDTO;
use App\Entity\User;

interface UserRepositoryInterface
{
    public function create(RegisterPayloadDTO $payload): void;

    public function findOneByAuthToken(string $token): ?User;

    public function findOneByEmailField(string $email): ?User;

    public function findOneByIdField(int $id): ?User;
}
