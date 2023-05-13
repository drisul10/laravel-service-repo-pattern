<?php

namespace App\Exceptions;

use App\Constants\HttpCode;
use App\Constants\Message;
use App\Constants\StatusCode;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof UnauthorizedHttpException) {
            return apiResponse(HttpCode::UNAUTHORIZED, StatusCode::UNAUTHORIZED, Message::AUTH_TOKEN_NOT_ACCEPTED, ['error' => $e->getMessage()]);
        } elseif ($e instanceof TokenInvalidException) {
            return apiResponse(HttpCode::UNAUTHORIZED, StatusCode::UNAUTHORIZED, Message::AUTH_TOKEN_INVALID, ['error' => $e->getMessage()]);
        } elseif ($e instanceof TokenExpiredException) {
            return apiResponse(HttpCode::UNAUTHORIZED, StatusCode::UNAUTHORIZED, Message::AUTH_TOKEN_EXPIRED, ['error' => $e->getMessage()]);
        }

        return parent::render($request, $e);
    }
}
