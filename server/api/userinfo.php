<?php
include("../../server/properties.php");
include("../../server/class/UserData.php");
include("../../server/class/ValidationUtil.php");
include("../../server/class/SQLCruds.php");

$res = 0;

// ログインして送信フォーム内にアクセストークンが設定されている場合のみ適用
if (isset($_POST['access_token'])) {
  if (mb_substr($_POST['access_token'], 0, 5) == "mtst_") {
    $connection = new PDO('mysql:host=' . $dsn . ';dbname=' . $dbname, $username, $password);

    if ($_POST['post_type'] == "user_create") {
      $info_array = array();
      $info_array['name'] = $_POST['user_name'];
      $info_array['login_id'] = $_POST['login_id'];
      $info_array['password'] = $_POST['password'];
      $info_array['authority'] = $_POST['authority'];
      $info_array['comment'] = $_POST['comment'];
      $isntExist = SQLCruds::isntUserAccountExist($connection, $dbname, $info_array);
      if ($isntExist == 1) $res = SQLCruds::registUserAccount($connection, $dbname, $info_array);
    }

    if ($_POST['post_type'] == "user_edit") {
      $setcols_array = array();
      if (isset($_POST['login_id'])) array_push($setcols_array, "login_id = '" . $_POST['login_id'] . "'");
      if (isset($_POST['user_name'])) array_push($setcols_array, "name = '" . $_POST['user_name'] . "'");
      if ($_POST['password'] != "") array_push($setcols_array, "password = '" . $_POST['password'] . "'");
      if (isset($_POST['comment'])) array_push($setcols_array, "comment = '" . $_POST['comment'] . "'");
      if (isset($_POST['is_stopped'])) array_push($setcols_array, "is_stopped = " . $_POST['is_stopped']);
      if (isset($_POST['authority'])) array_push($setcols_array, "authority = " . $_POST['authority']);
      array_push($setcols_array, "updated_at = now()");
      $uploader = implode(" , ", $setcols_array);
      $res = SQLCruds::updateUserAccount($connection, $dbname, $uploader, $_POST['id']);
    }

    if ($_POST['post_type'] == "user_delete") {
      $res = SQLCruds::deleteUserAccount($connection, $dbname, $_POST['id']);
    }
  }
}

if (isset($_GET['user_list'])) {
  $connection = new PDO('mysql:host=' . $dsn . ';dbname=' . $dbname, $username, $password);
  if ($_GET['user_list'] == "all") {
    $res = SQLCruds::getAllRegistedUserAccount($connection, $dbname);
  } else {
    $where = "";

    $s_where_array = array();
    if ($_GET['user_name'] != "") array_push($s_where_array, "name LIKE '%" . $_GET['user_name'] . "%'");
    if ($_GET['login_id'] != "") array_push($s_where_array, "login_id LIKE '%" . $_GET['login_id'] . "%'");
    if ($_GET['authority'] != "no") array_push($s_where_array, "authority = " . $_GET['authority']);
    if ($_GET['user_name'] != "" || $_GET['login_id'] != "" || $_GET['authority'] != "no") {
      $joiner = implode(" " . $_GET['which'] . " ", $s_where_array);
      $where = " WHERE " . $joiner;

      $res = SQLCruds::getSearchedUserAccount($connection, $dbname, $where);
    }
  }
}

echo $res;
