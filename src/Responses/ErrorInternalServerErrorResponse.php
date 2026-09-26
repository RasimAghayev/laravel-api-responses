<?php

declare(strict_types=1);

namespace RasimAghayev\LaravelApiResponses\Responses;

class ErrorInternalServerErrorResponse extends ApiErrorResponse
{
    protected function defaultResponseCode(): int
    {
        return 500;
    }
}
