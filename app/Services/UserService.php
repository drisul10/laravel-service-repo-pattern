<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
  protected $repository;

  public function __construct(UserRepository $repository)
  {
    $this->repository = $repository;
  }

  public function getFirst()
  {
    return $this->repository->getFirst();
  }

  public function save(array $users)
  {
    return $this->repository->save($users);
  }
}
