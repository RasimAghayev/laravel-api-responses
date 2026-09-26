<?php

declare(strict_types=1);

namespace RasimAghayev\LaravelApiResponses\Responses;

class ErrorNotFoundResponse extends ApiErrorResponse
{
    protected function defaultResponseCode(): int
    {
        return 404;
    }
}
