<?php

namespace App\Exceptions;

use Exception;
use App\Helpers\ApiResponseFormatter;
use BadMethodCallException;
use ErrorException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use TypeError;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Exception $e, Request $request) {
            if($request->is('api/*') || $request->wantsJson()) {
                if ($e instanceof NotFoundHttpException || $e instanceof RouteNotFoundException) {
                    return ApiResponseFormatter::error('RESOURCE NOT FOUND', 404, 'NOT FOUND');
                }

                if($e instanceof AuthenticationException  ) {
                    return ApiResponseFormatter::error($e->getMessage(),401, 'UNAUTHORIZED' );
                }

                if($e instanceof AccessDeniedHttpException) {
                    return ApiResponseFormatter::error($e->getMessage(),  403, 'FORBIDDEN');
                }

                if($e instanceof BadMethodCallException ) {
                    return ApiResponseFormatter::error($e->getMessage(), 500, 'Internal Server Error');
                }

                if($e instanceof QueryException || $e instanceof ErrorException ) {
                    return ApiResponseFormatter::error($e->getMessage(), 500, 'Internal Server Error');
                }

                if($e instanceof HttpException) {
                    return ApiResponseFormatter::error($e->getMessage(), 419, 'UNKNOWN STATUS');
                }

                if($e instanceof ValidationException) {
                    return ApiResponseFormatter::error(['errors' => $e->errors()]);
                }

                if($e instanceof TypeError ) {
                    return ApiResponseFormatter::error($e->getMessage());
                }

                // if($e instanceof ConnectionException) {
                //     return ApiResponseFormatter::error($e->getMessage());
                // }
            }
        });
    }
}
