<?php

namespace App\Helpers;

class ApiResponseFormatter {
    protected static $response = [
        'meta' => [
            'message' => null,
            'statusCode' => 200,
            'statusText' => 'OK'
        ],
        'data' => null
    ];

    /**
     * this function is used for successful http response
     * @param any $data
     * @param string|array $message
     * @param int $statusCode default 200
     * @param string $statusText default OK
     * @return \Illuminate\Http\JsonResponse
     * @krismonsemanas
    */
    public static function success($data, $message = null, $statusCode = 200, $statusText = 'OK')
    {
        self::$response['meta']['message'] = $message;
        self::$response['meta']['statusCode'] = $statusCode;
        self::$response['meta']['statusText'] = $statusText;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['meta']['statusCode']);
    }

    /**
     * this function is used for failed http response
     * @param string|array $message
     * @param int $statusCode default 400
     * @param string $statusText default BadRequest
     * @return \Illuminate\Http\JsonResponse
     * @krismonsemanas
    */
    public static function error($message = null, $statusCode = 400, $statusText = 'BadRequest')
    {
        self::$response['meta']['message'] = $message;
        self::$response['meta']['statusCode'] = $statusCode;
        self::$response['meta']['statusText'] = $statusText;

        return response()->json(self::$response, self::$response['meta']['statusCode']);
    }
}
