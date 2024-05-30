<?php

namespace App\Tests\Services;

use App\DataTransferObjects\API\V1\Authentication\LoginDTO;
use App\DataTransferObjects\API\V1\Authentication\RegisterPayloadDTO;
use App\Entity\User;
use App\Repository\Interfaces\UserRepositoryInterface;
use App\Services\Authentication\Interfaces\AuthServiceInterface;
use App\Services\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    private readonly MockObject $userRepositoryMock;

    private readonly MockObject $authServiceMock;

    private readonly MockObject $passwordHasherMock;

    private readonly UserService $userService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->authServiceMock = $this->createMock(AuthServiceInterface::class);
        $this->passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);

        $this->userService = new UserService(
            userRepository: $this->userRepositoryMock,
            authService: $this->authServiceMock,
            passwordHasher: $this->passwordHasherMock
        );
    }

    public function test_can_create_user_from_payload(): void
    {
        $payload = new RegisterPayloadDTO(
            email: 'test@test.com',
            password: 'plain_password',
            name: 'Name'
        );

        $this->passwordHasherMock->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed_password');

        $this->userRepositoryMock->expects($this->once())
            ->method('create')
            ->with($payload->replacePassword('hashed_password'))
            ->willReturnCallback(function (RegisterPayloadDTO $payloadDTO) use ($payload) {
                $this->assertNotSame($payloadDTO, $payload);

                $this->assertSame($payloadDTO->email, $payloadDTO->email);
                $this->assertSame($payloadDTO->name, $payloadDTO->name);
                $this->assertNotSame($payloadDTO->password, $payload->password);
                $this->assertSame('hashed_password', $payloadDTO->password);

                return $payloadDTO;
            });

        $this->userService->create($payload);
    }

    public function test_find_by_credentials_returns_null_if_there_is_no_user_with_given_email()
    {
        $loginPayload = $this->getTestLoginDTO();

        $this->userRepositoryMock->expects($this->once())
            ->method('findOneByEmailField')
            ->with($loginPayload->email)
            ->willReturn(null);

        $this->passwordHasherMock->expects($this->never())
            ->method('isPasswordValid');

        $this->assertNull($this->userService->findByCredentials($loginPayload));
    }

    public function test_find_by_credentials_returns_null_if_user_was_found_by_email_but_passwords_do_not_match()
    {
        $loginPayload = $this->getTestLoginDTO();

        $this->userRepositoryMock->expects($this->once())
            ->method('findOneByEmailField')
            ->with($loginPayload->email)
            ->willReturn($user = new User());

        $this->passwordHasherMock->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, $loginPayload->password)
            ->willReturn(false);

        $this->assertNull($this->userService->findByCredentials($loginPayload));
    }

    public function test_find_by_credentials_returns_user_if_email_and_password_match()
    {
        $loginPayload = $this->getTestLoginDTO();

        $this->userRepositoryMock->expects($this->once())
            ->method('findOneByEmailField')
            ->with($loginPayload->email)
            ->willReturn($user = new User());

        $this->passwordHasherMock->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, $loginPayload->password)
            ->willReturn(true);

        $this->assertSame($user, $this->userService->findByCredentials($loginPayload));
    }

    private function getTestLoginDTO(): LoginDTO
    {
        return new LoginDTO(
            email: 'test@test.com',
            password: 'plain_password',
        );
    }
}
