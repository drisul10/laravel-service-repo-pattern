<?php

namespace Tests\Feature;

use App\Constants\HttpCode;
use Tests\TestCase;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\Models\User;
use App\Constants\Message;
use App\Constants\StatusCode;
use App\Services\UserService;
use Mockery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{

  private $payloadRegister;
  private $payloadLogin;
  private $hashPaswd;

  public function setUp(): void
  {
    parent::setUp();
    Eloquent::unguard();
    User::truncate();

    $this->hashPaswd = bcrypt('mY_s3creT');

    $this->payloadRegister = [
      'name' => 'Andri Sul',
      'email' => 'me@andrisul.com',
      'password' => $this->hashPaswd,
    ];

    $this->payloadLogin = [
      'email' => 'me@andrisul.com',
      'password' => 'mY_s3creT',
    ];
  }

  public function test_can_register_with_valid_data()
  {
    $response = $this->json('POST', '/api/auth/register', $this->payloadRegister);

    $response->assertStatus(HttpCode::CREATED);
  }

  public function test_cannot_register_with_invalid_data()
  {
    $this->payloadRegister['email'] = 'andri.com';

    $response = $this->json('POST', '/api/auth/register', $this->payloadRegister);

    $response->assertStatus(HttpCode::BAD_REQUEST);
  }

  public function test_endpoint_register_should_valid_and_return_correct_status_code()
  {
    $response = $this->json('POST', '/api/auth/register', $this->payloadRegister);

    $response->assertStatus(HttpCode::CREATED);

    $response = $this->json('POST', '/api/register', $this->payloadRegister);

    $response->assertStatus(HttpCode::NOT_FOUND);

    $response = $this->json('GET', '/api/auth/register', $this->payloadRegister);

    $response->assertStatus(HttpCode::METHOD_NOT_ALLOWED);
  }

  public function test_can_login_with_valid_data()
  {
    app(UserService::class)->save($this->payloadRegister);

    $userService = Mockery::mock(UserService::class);
    $this->app->instance(UserService::class, $userService);

    $userService->shouldReceive('login')->with($this->payloadLogin)->andReturn(
      new JsonResponse([
        'status' => StatusCode::OK,
        'message' => Message::SUCCESS,
        'data' => [
          'token' => 'valid_jwt_token',
        ],
      ], HttpCode::SUCCESS)
    );

    $response = $this->postJson('/api/auth/login', $this->payloadLogin);

    $response->assertStatus(HttpCode::SUCCESS)
      ->assertJson([
        'status' => StatusCode::OK,
        'message' => Message::SUCCESS,
      ])
      ->assertJsonStructure(['data' => ['token']]);
  }

  public function test_cannot_login_with_invalid_data()
  {
    app(UserService::class)->save($this->payloadRegister);

    $this->payloadLogin['email'] = 'andri.com';

    $userService = Mockery::mock(UserService::class);
    $this->app->instance(UserService::class, $userService);

    $userService->shouldReceive('login')->with($this->payloadLogin)->andReturn(
      new JsonResponse([
        'status' => StatusCode::UNAUTHORIZED,
        'message' => Message::INVALID_CREDENTIALS,
        'data' => [
          'token' => 'valid_jwt_token',
        ],
      ], HttpCode::UNAUTHORIZED)
    );

    $response = $this->postJson('/api/auth/login', $this->payloadLogin);

    $response->assertStatus(HttpCode::UNAUTHORIZED)
      ->assertJson([
        'status' => StatusCode::UNAUTHORIZED,
        'message' => Message::INVALID_CREDENTIALS,
      ]);
  }

  public function test_can_logout_with_valid_token()
  {
    $userService = app(UserService::class);
    $userService->save($this->payloadRegister);
    $user = $userService->getFirst();
    $token = JWTAuth::fromUser($user);

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])->json('POST', '/api/auth/logout');

    $response->assertStatus(HttpCode::SUCCESS)
      ->assertJson([
        'status' => StatusCode::OK,
        'message' => Message::LOGOUT_SUCCESS,
      ]);
  }

  public function test_cannot_logout_with_invalid_token()
  {
    $response = $this->withHeaders([
      'Authorization' => 'Bearer invalid_token',
    ])->json('POST', '/api/auth/logout');

    $response->assertStatus(HttpCode::UNAUTHORIZED)
      ->assertJson([
        'status' => StatusCode::UNAUTHORIZED,
        'message' => Message::AUTH_TOKEN_NOT_ACCEPTED,
      ]);
  }

  public function test_cannot_logout_without_token()
  {
    $response = $this->json('POST', '/api/auth/logout');

    $response->assertStatus(HttpCode::UNAUTHORIZED)
      ->assertJson([
        'status' => StatusCode::UNAUTHORIZED,
        'message' => Message::AUTH_TOKEN_NOT_ACCEPTED,
      ]);
  }
}
