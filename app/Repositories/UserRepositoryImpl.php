<?php

namespace App\Repositories;

use App\Models\User;

class UserRepositoryImpl implements UserRepository
{
  protected $model;

  public function __construct(User $model)
  {
    $this->model = $model;
  }

  public function getFirst()
  {
    return $this->model->first();
  }

  public function save(array $users)
  {
    return $this->model->create($users);
  }
}
