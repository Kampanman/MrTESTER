<?php

class SQLCruds
{

  public static function isntUserAccountExist($connection, $dbname, $posted)
  {
    $res_num = 0;
    $sql = "SELECT count(id) FROM `" . $dbname . "`.`mt_accounts` "
      . "WHERE login_id = '" . $posted['login_id'] . "'";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->fetchColumn();
    if ($result == 0) $res_num = 1;

    return $res_num;
  }

  public static function getUserAccount($connection, $dbname, $posted)
  {
    $sql = "SELECT id, name, login_id, password, authority, comment, is_stopped, "
      . "DATE_FORMAT(created_at, '%Y-%m-%d') created_at, DATE_FORMAT(updated_at, '%Y-%m-%d') updated_at "
      . "FROM `" . $dbname . "`.`mt_accounts` "
      . "WHERE login_id = '" . $posted['login_id'] . "' LIMIT 1";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $record = $statement->fetch(PDO::FETCH_ASSOC);

    $user_data = new UserData();
    $user_data->setId($record['id']);
    $user_data->setUser_name($record['name']);
    $user_data->setLogin_id($record['login_id']);
    $user_data->setHashed_password($record['password']);
    $user_data->setAuthority($record['authority']);
    $user_data->setComment($record['comment']);
    $user_data->setIs_stopped($record['is_stopped']);
    $user_data->setCreated($record['created_at']);
    $user_data->setUpdated($record['updated_at']);

    return $user_data;
  }

  public static function getAllRegistedUserAccount($connection, $dbname)
  {
    $res = array();

    $sql = "SELECT id, name, login_id, authority, is_stopped, "
      . "DATE_FORMAT(created_at, '%Y-%m-%d') created_at, DATE_FORMAT(updated_at, '%Y-%m-%d') updated_at "
      . "FROM `" . $dbname . "`.`mt_accounts` ORDER BY updated_at DESC";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $got_list = $statement->fetchAll(PDO::FETCH_ASSOC);
    $res = json_encode($got_list);

    return $res;
  }

  public static function getSearchedUserAccount($connection, $dbname, $where)
  {
    $res = array();

    $sql = "SELECT id, name, login_id, authority, is_stopped, "
      . "DATE_FORMAT(created_at, '%Y-%m-%d') created_at, DATE_FORMAT(updated_at, '%Y-%m-%d') updated_at "
      . "FROM `" . $dbname . "`.`mt_accounts`" . $where . " ORDER BY updated_at DESC";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $got_list = $statement->fetchAll(PDO::FETCH_ASSOC);
    $res = json_encode($got_list);

    return $res;
  }

  public static function registUserAccount($connection, $dbname, $posted)
  {
    $res_num = 0;
    $sql = "INSERT INTO `" . $dbname . "`.`mt_accounts` "
      . "(id, name, login_id, password, authority, comment, is_stopped, created_at, updated_at) "
      . "VALUES (?, ?, ?, ?, ?, ?, 0, now(), now())";

    $statement = $connection->prepare($sql);
    $id = UserData::getGeneratedIdStr();
    $statement->bindValue(1, $id);
    $statement->bindValue(2, $posted['name']);
    $statement->bindValue(3, $posted['login_id']);
    $hashed_pass = UserData::getGeneratedHashPass($posted['password']);
    $statement->bindValue(4, $hashed_pass);
    $statement->bindValue(5, $posted['authority']);
    $statement->bindValue(6, $posted['comment']);

    $result = $statement->execute();
    if ($result) $res_num = 1;

    return $res_num;
  }

  public static function updateUserAccount($connection, $dbname, $uploader, $id)
  {
    $res_num = 0;
    $sql = "UPDATE `" . $dbname . "`.`mt_accounts` SET " . $uploader . " WHERE id = '" . $id . "'";

    $statement = $connection->prepare($sql);
    $result = $statement->execute();
    if ($result) $res_num = 1;

    return $res_num;
  }

  public static function deleteUserAccount($connection, $dbname, $user_id)
  {
    $res_num = 0;
    $sql = "DELETE FROM `" . $dbname . "`.`mt_accounts` WHERE id = '" . $user_id . "'";

    $statement = $connection->prepare($sql);
    $result = $statement->execute();
    if ($result) $res_num = 1;

    return $res_num;
  }

  public static function getUsersNoteLast100($connection, $dbname, $user_id)
  {
    $res = array();

    $sql = "SELECT id, title, tags, url, "
      . "DATE_FORMAT(last_viewed_at, '%Y-%m-%d') last_viewed_at, DATEDIFF(NOW(), last_viewed_at) AS elapsed_days, "
      . "DATE_FORMAT(created_at, '%Y-%m-%d') created_at, DATE_FORMAT(updated_at, '%Y-%m-%d') updated_at, created_user_id "
      . "FROM `" . $dbname . "`.`mt_notes` WHERE created_user_id = '" . $user_id . "' "
      . "ORDER BY elapsed_days DESC, updated_at DESC, title, tags LIMIT 100";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $got_list = $statement->fetchAll(PDO::FETCH_ASSOC);
    $res = json_encode($got_list);

    return $res;
  }

  public static function getUsersNoteRecentryViewed100($connection, $dbname, $user_id)
  {
    $res = array();

    $sql = "SELECT id, title, tags, url, "
      . "DATE_FORMAT(last_viewed_at, '%Y-%m-%d') last_viewed_at, DATEDIFF(NOW(), last_viewed_at) AS elapsed_days, "
      . "DATE_FORMAT(created_at, '%Y-%m-%d') created_at, DATE_FORMAT(updated_at, '%Y-%m-%d') updated_at, created_user_id "
      . "FROM `" . $dbname . "`.`mt_notes` WHERE created_user_id = '" . $user_id . "' "
      . "ORDER BY elapsed_days, title, tags LIMIT 100";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $got_list = $statement->fetchAll(PDO::FETCH_ASSOC);
    $res = json_encode($got_list);

    return $res;
  }

  public static function getUsersNoteNonUpdatedView100($connection, $dbname, $user_id)
  {
    $res = array();

    $sql = "SELECT id, title, tags, url, "
      . "DATE_FORMAT(last_viewed_at, '%Y-%m-%d') last_viewed_at, DATEDIFF(NOW(), last_viewed_at) AS elapsed_days, "
      . "DATE_FORMAT(created_at, '%Y-%m-%d') created_at, DATE_FORMAT(updated_at, '%Y-%m-%d') updated_at, created_user_id "
      . "FROM `" . $dbname . "`.`mt_notes` WHERE created_user_id = '" . $user_id . "' "
      . "ORDER BY elapsed_days DESC, title, tags LIMIT 100";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $got_list = $statement->fetchAll(PDO::FETCH_ASSOC);
    $res = json_encode($got_list);

    return $res;
  }

  public static function getUsersNoteWithSearchWhere($connection, $dbname, $where, $limit)
  {
    $res = array();

    $sql = "SELECT id, title, tags, url, "
      . "DATE_FORMAT(last_viewed_at, '%Y-%m-%d') last_viewed_at, DATEDIFF(NOW(), last_viewed_at) AS elapsed_days, "
      . "DATE_FORMAT(created_at, '%Y-%m-%d') created_at, DATE_FORMAT(updated_at, '%Y-%m-%d') updated_at, created_user_id "
      . "FROM `" . $dbname . "`.`mt_notes` WHERE " . $where . " "
      . "ORDER BY elapsed_days DESC, updated_at DESC, title, tags LIMIT " . $limit;
    $statement = $connection->prepare($sql);
    $statement->execute();
    $got_list = $statement->fetchAll(PDO::FETCH_ASSOC);
    $res = json_encode($got_list);

    return $res;
  }

  public static function isntNoteIdOrUserIdExist($connection, $dbname, $posted)
  {
    $res_num = 0;
    $sql = "SELECT count(id) FROM `" . $dbname . "`.`mt_notes` "
      . "WHERE id = '" . $posted['id'] . "' AND created_user_id = '" . $posted['user_id'] . "'";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->fetchColumn();
    if ($result == 0) $res_num = 1;

    return $res_num;
  }

  public static function getUserNoteWithIdAndUserId($connection, $dbname, $posted)
  {
    $sql = "SELECT id, title, tags, url, note, DATE_FORMAT(last_viewed_at, '%Y-%m-%d') last_viewed_at "
      . "FROM `" . $dbname . "`.`mt_notes` "
      . "WHERE id = '" . $posted['id'] . "' AND created_user_id = '" . $posted['user_id'] . "'";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $record = $statement->fetch(PDO::FETCH_ASSOC);

    return $record;
  }

  public static function registUsersNote($connection, $dbname, $posted)
  {
    $res_num = 0;
    $sql = "INSERT INTO `" . $dbname . "`.`mt_notes` "
      . "(id, title, tags, url, note, last_viewed_at, created_at, created_user_id, updated_at) "
      . "VALUES (?, ?, ?, ?, ?, now(), now(), ?, now())";
    $statement = $connection->prepare($sql);

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < 16; $i++) {
      $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }

    $id = $random_string;
    $statement->bindValue(1, $id);
    $statement->bindValue(2, $posted['title']);
    $statement->bindValue(3, $posted['tags']);
    $statement->bindValue(4, $posted['url']);
    $statement->bindValue(5, $posted['note']);
    $statement->bindValue(6, $posted['created_user_id']);

    $result = $statement->execute();
    if ($result) $res_num = 1;

    return $res_num;
  }

  public static function updateUsersNote($connection, $dbname, $uploader, $id)
  {
    $res_num = 0;
    $sql = "UPDATE `" . $dbname . "`.`mt_notes` SET " . $uploader . " WHERE id = '" . $id . "'";

    $statement = $connection->prepare($sql);
    $result = $statement->execute();
    if ($result) $res_num = 1;

    return $res_num;
  }

  public static function deleteUsersNote($connection, $dbname, $id)
  {
    $res_num = 0;
    $sql = "DELETE FROM `" . $dbname . "`.`mt_notes` WHERE id = '" . $id . "'";

    $statement = $connection->prepare($sql);
    $result = $statement->execute();
    if ($result) $res_num = 1;

    return $res_num;
  }
}
