<?php

declare(strict_types=1);

namespace Rasim\LaravelApiResponses\Responses;

class ErrorMethodNotAllowedResponse extends ApiErrorResponse
{
    protected function defaultResponseCode(): int
    {
        return 405;
    }
}
