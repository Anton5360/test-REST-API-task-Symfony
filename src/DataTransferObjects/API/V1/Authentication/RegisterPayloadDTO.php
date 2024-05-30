<?php

namespace App\DataTransferObjects\API\V1\Authentication;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Service\Attribute\Required;

readonly class RegisterPayloadDTO
{
    public function __construct(
        #[Required]
        #[NotBlank]
        #[Type('string')]
        #[Email]
        public string $email,
        #[Required]
        #[NotBlank]
        #[Type('string')]
        #[PasswordStrength(minScore: PasswordStrength::STRENGTH_WEAK)]
        public string $password,
        #[NotBlank]
        #[Type('string')]
        #[Length(min: 1, max: 100)]
        public string $name,
    ) {
    }

    public function replacePassword(string $password): self
    {
        return new self(
            email: $this->email,
            password: $password,
            name: $this->name,
        );
    }
}
