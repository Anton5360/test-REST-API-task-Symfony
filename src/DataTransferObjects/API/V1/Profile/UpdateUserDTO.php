<?php

namespace App\DataTransferObjects\API\V1\Profile;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final readonly class UpdateUserDTO
{
    public function __construct(
        #[NotBlank]
        #[Type('string')]
        #[Length(min: 1, max: 100)]
        public string $name,
    ) {
    }
}
