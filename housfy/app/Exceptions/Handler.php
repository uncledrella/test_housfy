<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use App\Exceptions\ApiItemNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (ApiException $exception, $request) {
            return $this->responseApiError($exception, $request);
        });
        $this->renderable(function (ApiItemNotFoundException $exception, $request) {
            return $this->responseApiError($exception, $request);
        });
        $this->renderable(function (ApiValidationFailedException $exception, $request) {
            return $this->responseApiError($exception, $request);
        });
    }

    private function responseApiError($exception, $request) {
        return response()->json($exception->getError(), $exception->getCode());
    }

}
