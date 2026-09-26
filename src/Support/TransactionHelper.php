<?php

declare(strict_types=1);

namespace Rasim\LaravelApiResponses\Support;

use Illuminate\Support\Facades\DB;
use Rasim\LaravelApiResponses\Responses\SuccessApiResponse;
use Rasim\LaravelApiResponses\Responses\ErrorApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class TransactionHelper
{
    public static function handleWithTransaction(
        callable $callback,
        int $successStatus = 200
    ): SuccessApiResponse|ErrorApiResponse {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();
            return SuccessApiResponse::make($result, $successStatus);
        } catch (ValidationException $e) {
            DB::rollBack();
            return ErrorApiResponse::make($e->errors(), 422);
        } catch (AuthorizationException $e) {
            DB::rollBack();
            return ErrorApiResponse::make('Authorization failed: ' . $e->getMessage(), 403);
        } catch (ModelNotFoundException | NotFoundHttpException $e) {
            DB::rollBack();
            return ErrorApiResponse::make('Resource not found: ' . $e->getMessage(), 404);
        } catch (Throwable $e) {
            DB::rollBack();
            return ErrorApiResponse::make('An error occurred: ' . $e->getMessage(), 500);
        }
    }
}
