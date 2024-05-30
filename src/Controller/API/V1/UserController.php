<?php

namespace App\Controller\API\V1;

use App\Controller\Traits\HasJsonRequestTrait;
use App\DataTransferObjects\API\V1\Authentication\RegisterPayloadDTO;
use App\DataTransferObjects\API\V1\Profile\UpdateUserDTO;
use App\Entity\User;
use App\Exceptions\UserWithEmailAlreadyExistsException;
use App\Responses\UserResponse;
use App\Services\Interfaces\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    use HasJsonRequestTrait;

    public function __construct(
        private readonly UserServiceInterface $userService
    ) {
    }

    #[Route('/api/v1/users/{id}', name: 'api.v1.users.show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(#[CurrentUser] User $authenticatedUser, ?int $id = null): Response
    {
        $user = $authenticatedUser;

        if (!is_null($id)) {
            $this->authorizeAction(user: $user, id: $id);

            $user = $this->userService->findById($id);
        }

        return new UserResponse($user);
    }

    #[Route('/api/v1/users', name: 'api.v1.users.store', methods: ['POST'])]
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        $this->denyUnlessAdmin();

        $payload = $this->parseJsonPayload($request);

        $payload = new RegisterPayloadDTO(
            $payload['email'] ?? '',
            $payload['password'] ?? '',
            $payload['name'] ?? ''
        );

        if ($errorResponse = $this->validatePayload($validator, $payload)) {
            return $errorResponse;
        }

        try {
            $this->userService->create($payload);
        } catch (UserWithEmailAlreadyExistsException) {
            return new Response(
                content: 'User with this email already exists',
                status: Response::HTTP_BAD_REQUEST
            );
        }

        return new UserResponse($this->userService->findByEmail($payload->email));
    }

    #[Route('/api/v1/users/{id}', name: 'api.v1.users.update', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function update(
        #[CurrentUser] User $user,
        int $id,
        Request $request,
        ValidatorInterface $validator
    ): Response {
        $this->authorizeAction(user: $user, id: $id);

        $payload = $this->parseJsonPayload($request);

        $payload = new UpdateUserDTO($payload['name'] ?? '');

        if ($errorResponse = $this->validatePayload($validator, $payload)) {
            return $errorResponse;
        }

        $this->userService->update($user, $payload);

        return new UserResponse($this->userService->findByEmail($user->getEmail()));
    }

    #[Route('/api/v1/users/{id}', name: 'api.v1.users.delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(#[CurrentUser] User $user, int $id): Response
    {
        $this->authorizeAction(user: $user, id: $id);

        $this->userService->delete($user);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    private function authorizeAction(User $user, int $id): void
    {
        if ($user->getId() === $id) {
            return;
        }

        $this->denyUnlessAdmin();
    }

    private function denyUnlessAdmin(): void
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    }
}
