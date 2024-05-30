<?php

namespace App\Services\Authentication;

use App\Services\Authentication\Interfaces\AuthTokenGeneratorServiceInterface;
use Random\Randomizer;

final class RandomizerAuthTokenGeneratorService implements AuthTokenGeneratorServiceInterface
{
    public function generate(int $length): string
    {
        return substr(
            string: bin2hex(string: (new Randomizer())->getBytes(length: 40)),
            offset: 0,
            length: $length,
        );
    }
}
