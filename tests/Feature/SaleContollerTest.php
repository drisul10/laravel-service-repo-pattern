<?php

namespace Tests\Feature;

use App\Constants\HttpCode;
use App\Constants\Message;
use App\Constants\StatusCode;
use App\Http\Controllers\SaleController;
use App\Models\Sale;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tymon\JWTAuth\Facades\JWTAuth;

class SaleControllerTest extends TestCase
{

  private $salePayload;
  protected $saleServiceMock;
  protected $saleController;

  public function setUp(): void
  {
    parent::setUp();

    $vehicle = Vehicle::factory()->create();

    $this->salePayload = Sale::factory()->make([
      'vehicle_id' => $vehicle->id,
    ])->toArray();

    $this->saleServiceMock = Mockery::mock(SaleService::class);
    $this->app->instance(SaleService::class, $this->saleServiceMock);

    $this->app['config']->set('auth.defaults.guard', 'api');
    $this->app['config']->set('jwt.secret', 'hcKLugFj8QRq+nRGqXWXbMWQRW7AwcwIHdy0T/h4G+4=');

    $this->saleController = new SaleController($this->saleServiceMock);
  }

  public function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }

  public function test_can_save_sale_with_valid_data()
  {
    $this->saleServiceMock
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
    ])->json('POST', '/api/sale', $this->salePayload);

    $response->assertStatus(HttpCode::CREATED);
  }

  public function test_can_retrive_sales_with_pagination()
  {
    $request = Request::create('/api/sales', 'GET', [
      'page' => 1,
      'perPage' => 10,
      'searchQuery' => null,
      'filters' => [],
      'sortField' => 'created_at',
      'sortDirection' => 'desc'
    ]);

    $this->saleServiceMock
      ->shouldReceive('getManyWithPagination')
      ->once()
      ->andReturn(
        new JsonResponse([
          'status' => StatusCode::OK,
          'message' => Message::SUCCESS,
        ], HttpCode::SUCCESS)
      );

    $response = $this->saleController->getManyWithPagination($request);

    $this->assertEquals(HttpCode::SUCCESS, $response->getStatusCode());
  }
}
