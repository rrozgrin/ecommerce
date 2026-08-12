<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function success(JsonResource $resource, ?string $message = null): JsonResponse
    {
        return self::respond($resource, Response::HTTP_OK, $message);
    }

    public static function created(JsonResource $resource, ?string $message = null): JsonResponse
    {
        return self::respond($resource, Response::HTTP_CREATED, $message);
    }

    public static function noContent(): Response
    {
        return response()->noContent();
    }

    private static function respond(JsonResource $resource, int $status, ?string $message): JsonResponse
    {
        if ($message !== null) {
            $resource->additional(['message' => $message]);
        }

        return $resource->response()->setStatusCode($status);
    }
}
