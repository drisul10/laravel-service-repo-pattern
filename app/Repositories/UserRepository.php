<?php

namespace App\Repositories;

interface UserRepository
{
  public function getFirst();
  public function save(array $users);
}
