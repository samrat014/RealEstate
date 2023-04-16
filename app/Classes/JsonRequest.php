<?php

namespace App\Classes;

use Illuminate\Http\JsonResponse;

trait JsonRequest
{
    /**
     * It returns a json response with a 200 status code.
     *
     * @param data The data you want to return.
     * @param key The key to use for the data. If null, the data will be returned as an array.
     *
     * @return JsonResponse A JsonResponse object with a 200 status code.
     */
    protected function success($data, ?string $key = null): JsonResponse
    {
        return response()->json($this->body($data, $key), 200);
    }

    /**
     * It returns a JSON response with a 201 status code
     *
     * @param data The data you want to return.
     * @param key The key to use for the data. If null, the key will be the name of the resource.
     *
     * @return JsonResponse A JsonResponse object with a 201 status code.
     */
    protected function created($data, ?string $key = null) : JsonResponse
    {
        return response()->json($this->body($data, $key), 201);
    }

    /**
     * It returns a JSON response with a 422 status code.
     *
     * @param data The data you want to return.
     * @param key The key of the error message.
     *
     * @return JsonResponse A JsonResponse object with a 422 status code.
     */
    protected function invalid($data, ?string $key = null): JsonResponse
    {
        return response()->json($this->body($data, $key), 422);
    }

    /**
     * > This function returns a JSON response with a 404 status code
     *
     * @param data The data you want to return.
     * @param key The key to use for the data. If null, the data will be returned as the root of the
     * response.
     *
     * @return JsonResponse A JsonResponse object with a 404 status code.
     */
    protected function notFound($data, ?string $key = null) : JsonResponse
    {
        return response()->json($this->body($data, $key), 404);
    }

    /**
     * The request successfully deleted the resource
     *
     * @return Illuminate\Http\JsonResponse
     */
    protected function delete($data, ?string $key = null) : JsonResponse
    {
        return response()->json($this->body($data, $key), 204);
    }

    protected function unauthorized($data, ?string $key = null) : JsonResponse
    {
        return response()->json($this->body($data, $key), 401);
    }

    protected function body($data, ?string $key = null)
    {
        return is_null($key) ? $data : [$key => $data];
    }
}
