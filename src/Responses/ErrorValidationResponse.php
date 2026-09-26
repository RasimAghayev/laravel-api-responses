<?php

declare(strict_types=1);

namespace Rasim\LaravelApiResponses\Responses;

class ErrorValidationResponse extends ApiErrorResponse
{
    protected function defaultResponseCode(): int
    {
        return 422;
    }
}
