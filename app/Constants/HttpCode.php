<?php

namespace App\Constants;

class HttpCode
{
  const SUCCESS = 200;
  const CREATED = 201;
  const ACCEPTED = 202;
  const NOT_MODIFIED = 304;
  const TEMPORARY_REDIRECT = 307;
  const BAD_REQUEST = 400;
  const UNAUTHORIZED = 401;
  const FORBIDDEN = 403;
  const NOT_FOUND = 404;
  const METHOD_NOT_ALLOWED = 405;
  const REQUEST_TIMEOUT = 408;
  const TOO_MANY_REQUESTS = 429;
  const INTERNAL_ERROR = 500;
  const BAD_GATEWAY = 502;
  const SERVICE_UNAVAILABLE = 503;
  const GATEWAY_TIMEOUT = 504;
}
