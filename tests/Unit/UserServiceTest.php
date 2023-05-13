<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Mockery;

class UserServiceTest extends TestCase
{

  private $payloadRegister;
  private $hashPaswd;

  public function setUp(): void
  {
    parent::setUp();

    $this->hashPaswd = bcrypt('mY_s3creT');

    $this->payloadRegister = [
      'name' => 'Andri Sul',
      'email' => 'me@andrisul.com',
      'password' => $this->hashPaswd,
    ];
  }

  public function tearDown(): void
  {
    Mockery::close();
  }

  public function test_success_save_user()
  {
    $repository = Mockery::mock(UserRepository::class);
    $service = new UserService($repository);


    $repository->shouldReceive('save')->once()->andReturn((object) $this->payloadRegister);

    $user = $service->save($this->payloadRegister);

    $this->assertEquals($user->name, 'Andri Sul');
    $this->assertEquals($user->email, 'me@andrisul.com');
    $this->assertEquals($user->password, $this->hashPaswd);
  }
}
