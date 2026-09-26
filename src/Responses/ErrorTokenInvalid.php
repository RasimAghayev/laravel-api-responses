<?php

declare(strict_types=1);

namespace Rasim\LaravelApiResponses\Responses;

class ErrorTokenInvalid extends ApiErrorResponse
{
    protected function defaultResponseCode(): int
    {
        return 401;
    }

    protected function defaultErrorMessage(): string
    {
        return 'The token is invalid.';
    }
}
