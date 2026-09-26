<?php

declare(strict_types=1);

namespace Rasim\LaravelApiResponses\Responses;

class ErrorTooManyAttemptsResponse extends ApiErrorResponse
{
    protected function defaultResponseCode(): int
    {
        return 429;
    }
}
