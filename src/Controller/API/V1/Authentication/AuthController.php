<?php

namespace App\Controller\API\V1\Authentication;

use App\Controller\Traits\HasJsonRequestTrait;
use App\DataTransferObjects\API\V1\Authentication\LoginDTO;
use App\DataTransferObjects\API\V1\Authentication\RegisterPayloadDTO;
use App\Exceptions\UserWithEmailAlreadyExistsException;
use App\Services\Interfaces\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AuthController extends AbstractController
{
    use HasJsonRequestTrait;

    public function __construct(
        private readonly UserServiceInterface $userService,
    ) {
    }

    #[Route('/api/v1/auth/register', name: 'api.v1.auth.register', methods: ['POST'])]
    public function register(Request $request, ValidatorInterface $validator): Response
    {
        $payload = $this->parseJsonPayload($request);

        $payload = new RegisterPayloadDTO(
            $payload['email'] ?? '',
            $payload['password'] ?? '',
            $payload['name'] ?? '',
        );

        if ($errorResponse = $this->validatePayload($validator, $payload)) {
            return $errorResponse;
        }

        try {
            $this->userService->create($payload);
        } catch (UserWithEmailAlreadyExistsException) {
            return new Response(content: 'Email is already used', status: Response::HTTP_BAD_REQUEST);
        }

        return new Response(content: 'Registered successfully');
    }

    #[Route('/api/v1/auth/login', name: 'api.v1.auth.login', methods: ['POST'])]
    public function login(Request $request, ValidatorInterface $validator): Response
    {
        $payload = $this->parseJsonPayload($request);

        $payload = new LoginDTO($payload['email'] ?? '', $payload['password'] ?? '');

        if ($errorResponse = $this->validatePayload($validator, $payload)) {
            return $errorResponse;
        }

        if ($user = $this->userService->findByCredentials($payload)) {
            return new Response('Credentials do not match', Response::HTTP_UNAUTHORIZED);
        }

        $auth = $this->userService->createAuthToken($user);

        return new JsonResponse([
            'user' => $user->getEmail(),
            'token' => $auth->getToken(),
        ]);
    }
}
