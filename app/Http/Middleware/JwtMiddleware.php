<?php

namespace App\Http\Middleware;

use App\Constants\HttpCode;
use App\Constants\Message;
use App\Constants\StatusCode;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = null;
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return apiResponse(HttpCode::UNAUTHORIZED, StatusCode::UNAUTHORIZED, Message::AUTH_TOKEN_INVALID, ['error' => $e->getMessage()]);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return apiResponse(HttpCode::UNAUTHORIZED, StatusCode::UNAUTHORIZED, Message::AUTH_TOKEN_EXPIRED, ['error' => $e->getMessage()]);
            } else {
                return apiResponse(HttpCode::UNAUTHORIZED, StatusCode::UNAUTHORIZED, Message::AUTH_TOKEN_NOT_ACCEPTED, null);
            }
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
