<?php

namespace Tests\Unit;

use Tests\TestCase;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\Models\User;
use App\Repositories\UserRepositoryImpl;

class UserRepositoryTest extends TestCase
{
  private $payloadRegister;
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
  }

  public function test_success_save_user()
  {
    $repository = new UserRepositoryImpl(new User);

    $user = $repository->save($this->payloadRegister);

    $this->assertEquals($user->name, 'Andri Sul');
    $this->assertEquals($user->email, 'me@andrisul.com');
    $this->assertEquals($user->password, $this->hashPaswd);
  }
}
