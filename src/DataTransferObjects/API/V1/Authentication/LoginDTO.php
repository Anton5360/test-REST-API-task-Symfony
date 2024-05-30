<?php

namespace App\DataTransferObjects\API\V1\Authentication;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\Type;

final readonly class LoginDTO
{
    public function __construct(
        #[NotBlank]
        #[Type('string')]
        #[Email]
        public string $email,
        #[NotBlank]
        #[Type('string')]
        #[PasswordStrength(minScore: PasswordStrength::STRENGTH_WEAK)]
        public string $password,
    ) {

    }
}
