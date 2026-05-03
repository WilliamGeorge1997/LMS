<?php

namespace Modules\Common\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class ApiResponse
{
    public static function success(
        null|array|Collection|JsonResource|Model $data = null,
        ?string $message = null,
        string $statusString = 'ok'
    ): JsonResponse {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], self::statusCode($statusString));
    }

    public static function error(
        ?string $message = null,
        string $statusString = 'bad_request'
    ): JsonResponse {
        return response()->json([
            'status'  => false,
            'message' => $message,
        ], self::statusCode($statusString));
    }

    public static function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => 'Validation failed',
            'errors'  => $errors,
        ], 422);
    }

    private static function statusCode(string $type): int
    {
        static $codes = null;

        if ($codes === null) {
            $codes = [
                'ok'                  => 200,
                'created'             => 201,
                'accepted'            => 202,
                'no_content'          => 204,
                'moved'               => 301,
                'found'               => 302,
                'see_other'           => 303,
                'not_modified'        => 304,
                'temporary_redirect'  => 307,
                'bad_request'         => 400,
                'unauthorized'        => 401,
                'forbidden'           => 403,
                'not_found'           => 404,
                'method_not_allowed'  => 405,
                'not_acceptable'      => 406,
                'precondition_failed' => 412,
                'unsupported_media'   => 415,
                'validation_error'    => 422,
                'server_error'        => 500,
                'not_implemented'     => 501,
            ];
        }

        return $codes[strtolower($type)] ?? 200;
    }
}
