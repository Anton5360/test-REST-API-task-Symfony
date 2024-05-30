<?php

namespace App\Services\Authentication\Interfaces;

interface AuthTokenGeneratorServiceInterface
{
    public function generate(int $length): string;
}
