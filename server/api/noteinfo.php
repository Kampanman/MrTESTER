<?php
include("../../server/properties.php");
include("../../server/class/UserData.php");
include("../../server/class/SQLCruds.php");

$res = 0;

// ログインして送信フォーム内にアクセストークンが設定されている場合のみ適用
if (isset($_POST['access_token'])) {
  if (mb_substr($_POST['access_token'], 0, 5) == "mtst_") {
    $connection = new PDO('mysql:host=' . $dsn . ';dbname=' . $dbname, $username, $password);

    if ($_POST['post_type'] == "note_create") {
      $posted = array();
      $posted['title'] = $_POST['title'];
      $posted['tags'] = $_POST['tags'];
      $posted['url'] = $_POST['url'];
      $posted['note'] = $_POST['note'];
      $posted['created_user_id'] = $_POST['created_user_id'];
      $res = SQLCruds::registUsersNote($connection, $dbname, $posted);
    }

    if ($_POST['post_type'] == "note_edit") {
      $setcols_array = array();
      if (isset($_POST['note'])) array_push($setcols_array, "note = '" . $_POST['note'] . "'");
      if (isset($_POST['title'])) array_push($setcols_array, "title = '" . $_POST['title'] . "'");
      if (isset($_POST['tags'])) array_push($setcols_array, "tags = '" . $_POST['tags'] . "'");
      if (isset($_POST['url'])) array_push($setcols_array, "url = '" . $_POST['url'] . "'");
      array_push($setcols_array, "updated_at = now()");
      $uploader = implode(" , ", $setcols_array);
      $res = SQLCruds::updateUsersNote($connection, $dbname, $uploader, $_POST['id']);
    }

    if ($_POST['post_type'] == "note_delete") {
      $res = SQLCruds::deleteUsersNote($connection, $dbname, $_POST['id']);
    }

    if ($_POST['post_type'] == "update_lastview") {
      $setcols_array = array();
      array_push($setcols_array, "last_viewed_at = now()");
      $uploader = implode(" , ", $setcols_array);
      $res = SQLCruds::updateUsersNote($connection, $dbname, $uploader, $_POST['id']);
    }
  }
}

if (isset($_GET['user_list'])) {
  $connection = new PDO('mysql:host=' . $dsn . ';dbname=' . $dbname, $username, $password);

  if ($_GET['user_list'] == 'searched') {
    $where_array = array();

    array_push($where_array, "created_user_id = '" . $_GET['user_id'] . "'");
    if ($_GET['title'] != "") array_push($where_array, "title LIKE '%" . $_GET['title'] . "%'");
    if ($_GET['tags'] != "") array_push($where_array, "tags LIKE '%" . $_GET['tags'] . "%'");
    if ($_GET['keywords'] != "") array_push($where_array, "note LIKE '%" . $_GET['keywords'] . "%'");
    $where = implode(" " . $_GET['which'] . " ", $where_array);
    $res = SQLCruds::getUsersNoteWithSearchWhere($connection, $dbname, $where, $_GET['limit']);
  } else {
    $res = SQLCruds::getUsersNoteLast100($connection, $dbname, $_GET['id']);
  }
}

echo $res;
