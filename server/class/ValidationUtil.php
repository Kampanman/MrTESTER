<?php

class ValidationUtil
{

  public static function checkInputLoginUser($input)
  {
    $res = true;
    if (!isset($input)) $res = false;
    if (!preg_match('/^[a-zA-Z0-9._+^~-]+@[a-z0-9.-]+$/', $input)) $res = false;

    return $res;
  }

  public static function checkInputLoginPass($input)
  {
    $res = true;
    if (!isset($input)) $res = false;
    if (!preg_match('/^[a-zA-Z0-9_-]{8,16}$/', $input)) $res = false;

    return $res;
  }

  public static function verifyHashPass($input_pass, $hashed_pass)
  {
    $is_verified = password_verify($input_pass, $hashed_pass);
    return $is_verified;
  }
}
