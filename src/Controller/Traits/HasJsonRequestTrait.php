<?php

namespace App\Controller\Traits;

use App\DataTransferObjects\API\V1\Authentication\RegisterPayloadDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait HasJsonRequestTrait
{
    public function parseJsonPayload(Request $request): array
    {
        return json_decode($request->getContent(), true);
    }

    public function validatePayload(ValidatorInterface $validator, mixed $payload): ?Response
    {
        $errors = $validator->validate($payload);

        return $errors->count() > 1
            ? new Response((string)$errors)
            : null;
    }
}
