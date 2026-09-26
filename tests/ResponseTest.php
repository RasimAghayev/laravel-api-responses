<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Rasim\LaravelApiResponses\Responses\ApiBaseResponse;
use Rasim\LaravelApiResponses\Responses\SuccessApiResponse;
use Rasim\LaravelApiResponses\Responses\ApiErrorResponse;
use Rasim\LaravelApiResponses\Responses\ErrorApiResponse;

class ResponseTest extends TestCase
{
    public function testSuccessApiResponseExtendsApiBaseResponse(): void
    {
        $response = SuccessApiResponse::make(['test' => 'data'], 200);
        $this->assertInstanceOf(SuccessApiResponse::class, $response);
        $this->assertInstanceOf(ApiBaseResponse::class, $response);
    }

    public function testErrorApiResponseExtendsApiErrorResponse(): void
    {
        $response = ErrorApiResponse::make('error', 500);
        $this->assertInstanceOf(ErrorApiResponse::class, $response);
        $this->assertInstanceOf(ApiErrorResponse::class, $response);
    }

    public function testSuccessApiResponseDefaultCode(): void
    {
        $response = SuccessApiResponse::make(['test' => 'data']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testErrorApiResponseDefaultCode(): void
    {
        $response = ErrorApiResponse::make('error');
        $this->assertEquals(500, $response->getStatusCode());
    }
}
