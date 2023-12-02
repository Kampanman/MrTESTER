<?php

try {
  include("../../server/properties.php");
  include("../../server/class/UserData.php");
  include("../../server/class/ValidationUtil.php");
  include("../../server/class/SQLCruds.php");

  $this_page = "ユーザーの登録ノート一覧照会";
  /* セッション開始 */
  session_start();

  /* ログイン認証プロセス */
  if (!isset($_SESSION['access_token'])) {

    if (!isset($_POST['login_id'])) {
      header('Location: ../login.php?error=1');
      return;
    } else {
      if (!isset($_POST['password'])) {
        header('Location: ../login.php?error=2');
        return;
      } else {
        // データベースに接続
        $connection = new PDO('mysql:host=' . $dsn . ';dbname=' . $dbname, $username, $password);
        // データベースからユーザー情報の有無を判定
        $posted = array(
          'login_id' => $_POST['login_id'],
          'password' => $_POST['password'],
        );
        $isnt_exist = SQLCruds::isntUserAccountExist($connection, $dbname, $posted);
        if ($isnt_exist == 1) {
          header('Location: ../login.php?error=0');
          return;
        } else {
          // ユーザー情報が存在していた場合は取得する
          $user_data = SQLCruds::getUserAccount($connection, $dbname, $posted);
          $login_id = $user_data->getLogin_id();
          $hashed_pass = $user_data->getHashed_Password();
          $pass_correctness = ValidationUtil::verifyHashPass($_POST['password'], $hashed_pass);
          if (!$pass_correctness) {
            header('Location: ../login.php?error=3');
            return;
          } else if ($user_data->getIs_stopped() == 1) {
            header('Location: ../login.php?error=4');
            return;
          } else {
            $access_token = "mtst_" . bin2hex(random_bytes(8));
            $_SESSION['access_token'] = $access_token;
            $_SESSION['login_user']['id'] = $user_data->getId();
            $_SESSION['login_user']['login_id'] = $login_id;
            $_SESSION['login_user']['user_name'] = $user_data->getUser_name();
            $_SESSION['login_user']['authority'] = $user_data->getAuthority();
          }
        }
      }
    }
  }
  /* ログイン認証プロセス - ここまで */

  // 当サービス用のアクセストークンがセッションに登録されていない場合は、ログイン画面に遷移させる
  if (!isset($_SESSION['access_token'])) {
    header('Location: ../login.php');
  } else {
    if (mb_substr($_SESSION['access_token'], 0, 5) != "mtst_") {
      header('Location: ../login.php');
    }
  }
} catch (Exception $e) {
  echo $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $_SESSION['login_user']['user_name'] . "'s " . $contents_name . " ｜ " . $this_page ?></title>
  <?php echo $bootset("head"); ?>
  <?php echo $datatableset("head"); ?>
  <?php echo $fontset; ?>
  <link href="../../static/css/style.css" rel="stylesheet" />
  <link rel="icon" href="../../images/favicon.ico">
</head>

<body class="contents">
  <?php include("../parts/header.php") ?>
  <?php if (isset($_GET['req_error'])) : ?>
    <div class="d-flex justify-content-center align-items-center mx-3">
      <p class="message-red"><br />
        <?php if ($_GET['req_error'] == 1) : ?>
          <b>リクエストの設定を取得できなかった為、リダイレクトしました。</b>
        <?php endif; ?>
        <?php if ($_GET['req_error'] == 2) : ?>
          <b>リクエストされたログインIDは登録されていない為、リダイレクトしました。</b>
        <?php endif; ?>
        <?php if ($_GET['req_error'] == 3) : ?>
          <b>リクエストの設定を取得できなかった為、リダイレクトしました。</b>
        <?php endif; ?>
        <?php if ($_GET['req_error'] == 4) : ?>
          <b>リクエストされたノートIDまたは作成者IDは登録されていない為、リダイレクトしました。</b>
        <?php endif; ?>
      </p>
    </div>
  <?php endif; ?>
  <main class="index">
    <div class="white-board p-2">
      <div class="container mt-3">
        <h2 class="contents-title p-2 col-lg-6 text-center"><?php echo $this_page ?></h2>
        <br />
        <form id="search_note">
          <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['login_user']['id'] ?>">
          <div class="mb-3 form-group d-lg-flex align-items-center col-lg-8">
            <label for="title" class="form-label col-lg-2 col-md-3">タイトル</label>
            <input type="text" class="form-control" id="title" name="title" maxlength="60" placeholder="ノートのタイトルを入力してください">
          </div>
          <div class="mb-3 form-group d-lg-flex align-items-center col-lg-8">
            <label for="tags" class="form-label col-lg-2 col-md-3">タグ</label>
            <input type="text" class="form-control" id="tags" name="tags" maxlength="30" placeholder="ノートのタグを入力してください">
          </div>
          <div class="mb-3 form-group d-lg-flex align-items-center col-lg-8">
            <label for="keywords" class="form-label col-lg-2 col-md-3">キーワード</label>
            <input type="text" class="form-control" id="keywords" name="keywords" placeholder="ノート本文内のキーワードを入力してください">
          </div>
          <div class="mb-3 form-group d-lg-flex align-items-center col-lg-8">
            <label for="which" class="form-label col-lg-2 col-md-3">AND / OR</label>
            <select class="form-select" id="which" name="which">
              <option value="AND" checked>AND</option>
              <option value="OR">OR</option>
            </select>
          </div>
          <div class="mb-3 form-group d-lg-flex align-items-center col-lg-8">
            <label for="which" class="form-label col-lg-2 col-md-3">取得件数</label>
            <select class="form-select" id="limit" name="limit">
              <option value="100" checked>100</option>
              <option value="200">200</option>
              <option value="500">500</option>
              <option value="999">999</option>
            </select>
          </div>
          <div class="d-flex justify-content-center align-items-center mt-3">
            <button type="button" class="search do-btn btn btn-primary">この条件で検索</button>
          </div>
          <br />
        </form>
        <table id="noteTable" class="table table-bordered">
          <thead>
            <tr>
              <th></th>
              <th class="note-title">タイトル</th>
              <th>タグ</th>
              <th>作成</th>
              <th>最終更新日</th>
              <th>最終閲覧日<br />更新からの<br />経過日数</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </main>

  <?php echo $bootset("foot"); ?>
  <?php echo $datatableset("foot") ?>
  <script src="../../static/js/function.js"></script>
  <script>
    let data_table;
    $(document).ready(function() {
      let send_url = "../../server/api/noteinfo.php?id=" + $("#user_id").val() + "&user_list=all";
      data_table = $('#noteTable').DataTable(getDt_settings(send_url));
      resetTableOverflowX();
    });

    $(function() {
      $(document).on('click', ".search", function() {
        let search_param = $("#search_note").serialize();
        let send_url = "../../server/api/noteinfo.php?user_list=searched&" + search_param;

        // 画面遷移後にDatatablesが初期化されているのを破棄。検索後の状態に再構築できるようにする。
        if ($.fn.dataTable.isDataTable('#noteTable')) {
          data_table.destroy();
          data_table = $('#noteTable').DataTable(getDt_settings(send_url));
          resetTableOverflowX();
        }
      });
    });

    function getDt_settings(send_url) {
      let res_object = {
        ajax: {
          url: send_url,
          dataSrc: '',
        },
        columns: [{
            data: 'id',
            render: function(data, type, row) {
              const view_btn = '<button type="button" class="btn btn-success" ' +
                'id="view_' + data + '" data-note_id="' + row.id + '" data-created_user_id="' + row.created_user_id + '"' +
                'onclick="jumpForView()">view</button>';
              const edit_btn = '<button type="button" class="btn btn-primary mx-2" ' +
                'id="edit_' + data + '" data-note_id="' + row.id + '" data-created_user_id="' + row.created_user_id + '"' +
                'onclick="jumpForEdit()">edit</button>';
              return view_btn + edit_btn;
            },
            sortable: false,
          },
          {
            data: 'title',
            render: function(data, type, row) {
              const render = (row.url) ? '<a target="_blank" href="' + row.url + '" style="text-decoration:none;">' + data + '</a>' : data;
              return render;
            }
          },
          {
            data: 'tags'
          },
          {
            data: 'created_at'
          },
          {
            data: 'updated_at'
          },
          {
            data: 'elapsed_days',
            render: function(data, type, row) {
              let render = "";
              if (row.elapsed_days >= 10 && row.elapsed_days < 30) {
                render = '<span class="over10"><b>10日以上</b></span>';
              } else if (row.elapsed_days >= 30) {
                render = '<span class="over30"><b>30日以上</b></span>';
              } else {
                render = data + " 日";
              }

              return render;
            }
          },
        ],
        info: false,
        searching: false,
      }

      return res_object;
    }

    function jumpForView() {
      const note_id = event.target.dataset.note_id;
      const user_id = event.target.dataset.created_user_id;
      location.href = "./note_view.php?id=" + note_id + "&user_id=" + user_id;
    }

    function jumpForEdit() {
      const note_id = event.target.dataset.note_id;
      const user_id = event.target.dataset.created_user_id;
      location.href = "./note_edit.php?id=" + note_id + "&user_id=" + user_id;
    }

    function resetTableOverflowX() {
      $("#search_note").next('div').children().css('overflow-x', 'auto');
    }
  </script>
</body>

</html>