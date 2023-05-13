<?php

namespace App\Http\Controllers;

use App\Constants\HttpCode;
use App\Constants\Message;
use App\Constants\StatusCode;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="API endpoints of auth user"
 * ),
 * @OA\SecurityScheme(
 *    type="http",
 *    description="Bearer Token Authentication",
 *    name="Authorization",
 *    in="header",
 *    scheme="bearer",
 *    bearerFormat="JWT",
 *    securityScheme="bearerAuth",
 * ),
 */
class AuthController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Register new user
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Auth"},
     *     @OA\Response(response="200", description="OK"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="Andri Sul"),
     *              @OA\Property(property="email", type="string", format="email", example="me@andrisul.com"),
     *              @OA\Property(property="password", type="string", format="password", example="mY_s3creT")
     *         ),
     *     ),
     * )
     */
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);
            $validatedData['password'] = bcrypt($validatedData['password']);

            $user = $this->service->save($validatedData);

            $token = JWTAuth::fromUser($user);

            return apiResponse(HttpCode::CREATED, StatusCode::CREATED, Message::CREATED, ['token' => $token]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return apiResponse(HttpCode::BAD_REQUEST, StatusCode::BAD_REQUEST, Message::INVALID_REQUEST, ['error' => $e->errors()]);
        } catch (\Exception $e) {
            return apiResponse(HttpCode::INTERNAL_ERROR, StatusCode::INTERNAL_ERROR, Message::INTERNAL_ERROR, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Login user
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     @OA\Response(response="200", description="OK"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="me@andrisul.com"),
     *              @OA\Property(property="password", type="string", format="password", example="mY_s3creT")
     *         ),
     *     ),
     * )
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return apiResponse(HttpCode::UNAUTHORIZED, StatusCode::UNAUTHORIZED, Message::INVALID_CREDENTIALS, null);
            }

            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            return apiResponse(HttpCode::SUCCESS, StatusCode::OK, Message::SUCCESS, ['token' => $token]);
        } catch (\Exception $e) {
            return apiResponse(HttpCode::INTERNAL_ERROR, StatusCode::INTERNAL_ERROR, Message::INTERNAL_ERROR, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Logout user
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Auth"},
     *     @OA\Response(response="200", description="OK"),
     *     security={ {"bearerAuth": {}} }
     *  )
     */
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate($request->bearerToken());
            return apiResponse(HttpCode::SUCCESS, StatusCode::OK, Message::LOGOUT_SUCCESS, null);
        } catch (TokenExpiredException $e) {
            return apiResponse(HttpCode::UNAUTHORIZED, StatusCode::UNAUTHORIZED, Message::AUTH_TOKEN_EXPIRED, ['error' => $e->getMessage()]);
        } catch (TokenInvalidException $e) {
            return apiResponse(HttpCode::UNAUTHORIZED, StatusCode::UNAUTHORIZED, Message::AUTH_TOKEN_INVALID, ['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return apiResponse(HttpCode::INTERNAL_ERROR, StatusCode::INTERNAL_ERROR, Message::LOGOUT_FAILED, ['error' => $e->getMessage()]);
        }
    }
}
