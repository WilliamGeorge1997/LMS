<?php

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

if (!function_exists('jsonResponse')) {
    function jsonResponse(
        bool $status = false,
        ?string $message = null,
        null|array|Collection|JsonResource|Model $data = null,
        string $status_string = "ok"
    ) {
        $response = ['status' => $status, 'message' => $message, 'data' => $data];
        return response()->json($response, getStatusCode($status_string));
    }
}


if (!function_exists('getStatusCode')) {
    function getStatusCode($type = "ok")
    {
        return statusCodes()[strtolower($type)] ?? 200;
    }
}


if (!function_exists('statusCodes')) {
    function statusCodes()
    {
        return [
            "ok" => 200,
            "created" => 201,
            "accepted" => 202,
            "no_content" => 204,
            "moved" => 301,
            "found" => 302,
            "see_other" => 303,
            "not_modified" => 304,
            "temporary_redirect" => 307,
            "bad_request" => 400,
            "unauthorized" => 401,
            "forbidden" => 403,
            "not_found" => 404,
            "method_not_allowed" => 405,
            "not_acceptable" => 406,
            "precondition_failed" => 412,
            "unsupported_media_type" => 415,
            "validation_error" => 422,
            "server_error" => 500,
            "not_implemented" => 501,
        ];
    }
}

if (!function_exists('getCaseCollection')) {
    /**
     * Return paginated or full collection based on request data.
     *
     * @param  mixed  $builder
     * @param  array<string, mixed>  $data
     * @return mixed
     */
    function getCaseCollection($builder, array $data)
    {
        if ($data['paginated'] ?? null) {
            return $builder->paginate($data['paginated'] ?? 20);
        }

        return $builder->get();
    }
}
