<?php

namespace Tests\Unit;

use App\Constants\HttpCode;
use App\Constants\Message;
use App\Constants\StatusCode;
use App\Http\Controllers\VehicleController;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tymon\JWTAuth\Facades\JWTAuth;

class VehicleControllerTest extends TestCase
{
  private $vehiclePayload;
  protected $vehicleServiceMock;
  protected $vehicleRepositoryMock;
  protected $vehicleController;

  public function setUp(): void
  {
    parent::setUp();

    $this->vehiclePayload = Vehicle::factory()->make()->toArray();

    $this->vehicleServiceMock = Mockery::mock(VehicleService::class);
    $this->app->instance(VehicleService::class, $this->vehicleServiceMock);

    $this->app['config']->set('auth.defaults.guard', 'api');
    $this->app['config']->set('jwt.secret', 'hcKLugFj8QRq+nRGqXWXbMWQRW7AwcwIHdy0T/h4G+4=');

    $this->vehicleController = new VehicleController($this->vehicleServiceMock);
  }

  public function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }

  public function test_can_save_vehicle_with_valid_data()
  {
    $this->vehicleServiceMock
      ->shouldReceive('save')
      ->once()
      ->andReturn(
        new JsonResponse([
          'status' => StatusCode::CREATED,
          'message' => Message::CREATED,
        ], HttpCode::CREATED)
      );

    $user = User::factory()->create();

    $token = JWTAuth::fromUser($user);

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])->json('POST', '/api/vehicle', $this->vehiclePayload);

    $response->assertStatus(HttpCode::CREATED);
  }

  public function test_can_retrive_vehicles_with_pagination()
  {
    $request = Request::create('/api/vehicles', 'GET', [
      'page' => 1,
      'perPage' => 10,
      'searchQuery' => null,
      'filters' => [],
      'sortField' => 'created_at',
      'sortDirection' => 'desc'
    ]);

    $this->vehicleServiceMock
      ->shouldReceive('getManyWithPagination')
      ->once()
      ->andReturn(
        new JsonResponse([
          'status' => StatusCode::OK,
          'message' => Message::SUCCESS,
        ], HttpCode::SUCCESS)
      );

    $response = $this->vehicleController->getManyWithPagination($request);

    $this->assertEquals(HttpCode::SUCCESS, $response->getStatusCode());
  }
}
