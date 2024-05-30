<?php

namespace App\Services;

use App\DataTransferObjects\API\V1\Authentication\LoginDTO;
use App\DataTransferObjects\API\V1\Authentication\RegisterPayloadDTO;
use App\DataTransferObjects\API\V1\Profile\UpdateUserDTO;
use App\Entity\Auth;
use App\Entity\User;
use App\Exceptions\UserWithEmailAlreadyExistsException;
use App\Repository\Interfaces\UserRepositoryInterface;
use App\Services\Authentication\Interfaces\AuthServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AuthServiceInterface    $authService,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function create(RegisterPayloadDTO $payload): void
    {
        if ($this->findByEmail($payload->email)) {
            throw new UserWithEmailAlreadyExistsException();
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            user: new User(),
            plainPassword: $payload->password
        );

        $this->userRepository->create(
            $payload->replacePassword(password: $hashedPassword),
        );
    }

    public function findByAuthToken(string $token): ?User
    {
        return $this->userRepository->findOneByAuthToken($token);
    }

    public function delete(User $user): true
    {
        $this->userRepository->delete($user);

        return true;
    }

    public function createAuthToken(User $user): Auth
    {
        if ($auth = $this->authService->findByUser(user: $user)) {
            return $auth;
        }

        return $this->authService->createForUser(user: $user);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findOneByEmailField(email: $email);
    }

    public function findByCredentials(LoginDTO $credentials): ?User
    {
        if (!$user = $this->findByEmail(email: $credentials->email)) {
            return null;
        }

        $passwordsAreEqual = $this->passwordHasher->isPasswordValid(
            user: $user,
            plainPassword: $credentials->password,
        );

        return $passwordsAreEqual
            ? $user
            : null;
    }

    public function update(User $user, UpdateUserDTO $values): void
    {
        $this->userRepository->update(user: $user, payload: $values);
    }

    public function findById(int $id): ?User
    {
        return $this->userRepository->findOneByIdField(id: $id);
    }
}
