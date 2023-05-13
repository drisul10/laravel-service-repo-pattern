<?php

namespace App\Http\Responses;

class ApiResponse
{
  public int $httpCode;
  public string $status;
  public ?string $message;
  public $data;

  public function __construct(int $httpCode, string $status, ?string $message = null, $data = null)
  {
    $this->httpCode = $httpCode;
    $this->status = $status;
    $this->message = $message;
    $this->data = $data;
  }
}
