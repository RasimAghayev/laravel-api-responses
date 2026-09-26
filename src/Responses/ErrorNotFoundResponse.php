<?php

declare(strict_types=1);

namespace Rasim\LaravelApiResponses\Responses;

class ErrorNotFoundResponse extends ApiErrorResponse
{
    protected function defaultResponseCode(): int
    {
        return 404;
    }
}
