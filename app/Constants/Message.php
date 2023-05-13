<?php

namespace App\Constants;

class Message
{
  // Common
  const SUCCESS = 'Success';
  const CREATED = 'Data successfully created';
  const LOGOUT_SUCCESS = 'Logged out successfully';

  const INVALID_REQUEST = 'Invalid request';
  const INVALID_CREDENTIALS = 'Invalid credentials';
  const INTERNAL_ERROR = 'Something went wrong';
  const LOGOUT_FAILED = 'Failed to logout';
  const AUTH_TOKEN_EXPIRED = 'Authentication token expired';
  const AUTH_TOKEN_INVALID = 'Invalid authentication token';
  const AUTH_TOKEN_NOT_ACCEPTED = 'Authentication token not accepted';
}
