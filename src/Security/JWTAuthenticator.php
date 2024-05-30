<?php

namespace App\Security;

use App\Services\Interfaces\UserServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class JWTAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly UserServiceInterface $userService,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return !str_contains($request->get('_route'), 'api.v1.auth.');
    }

    public function authenticate(Request $request): Passport
    {
        sscanf($request->headers->get('Authorization', ''), 'Bearer %s', $token);

        if (empty($token)) {
            throw new CustomUserMessageAuthenticationException('Token not provided');
        }

        $user = $this->userService->findByAuthToken($token);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Token is invalid');
        }

        return new SelfValidatingPassport(new UserBadge($user->getEmail()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}
