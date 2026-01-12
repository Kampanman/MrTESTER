<?php

class UserData
{
  private $id;
  private $login_id;
  private $password;
  private $user_name;
  private $authority;
  private $comment;
  private $is_stopped;
  private $created;
  private $updated;

  public function getId()
  {
    return $this->id;
  }

  public function getLogin_id()
  {
    return $this->login_id;
  }

  public function getHashed_Password()
  {
    return $this->password;
  }

  public function getUser_name()
  {
    return $this->user_name;
  }

  public function getAuthority()
  {
    return $this->authority;
  }

  public function getComment()
  {
    return $this->comment;
  }

  public function getIs_stopped()
  {
    return $this->is_stopped;
  }

  public function getCreated()
  {
    return $this->created;
  }

  public function getUpdated()
  {
    return $this->updated;
  }

  public function setId($input)
  {
    $this->id = $input;
  }

  public function setLogin_id($input)
  {
    $this->login_id = $input;
  }

  public function setHashed_password($input)
  {
    $this->password = $input;
  }

  public function setUser_name($input)
  {
    $this->user_name = $input;
  }

  public function setAuthority($input)
  {
    $this->authority = $input;
  }

  public function setComment($input)
  {
    $this->comment = $input;
  }

  public function setIs_stopped($input)
  {
    $this->is_stopped = $input;
  }

  public function setCreated($input)
  {
    $this->created = $input;
  }

  public function setUpdated($input)
  {
    $this->updated = $input;
  }

  public static function getGeneratedIdStr()
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < 12; $i++) {
      $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $random_string;
  }

  public static function getGeneratedHashPass($input)
  {
    return password_hash($input, PASSWORD_DEFAULT);
  }
}
