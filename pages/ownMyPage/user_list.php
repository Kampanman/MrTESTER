<?php

try {
  include("../../server/properties.php");
  include("../../server/class/UserData.php");
  include("../../server/class/ValidationUtil.php");
  include("../../server/class/SQLCruds.php");

  $this_page = "登録ユーザー一覧";
  /* セッション開始 */
  session_start();
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

  <main class="admin user-list">
    <div class="admin white-board p-2">
      <div class="container mt-3">
        <h2 class="contents-title p-2 col-lg-5 text-center"><?php echo $this_page ?></h2>
        <br />
        <form id="search_account">
          <div class="mb-3 form-group d-lg-flex align-items-center col-lg-8">
            <label for="user_name" class="form-label col-lg-2 col-md-3">ユーザー名</label>
            <input type="text" class="form-control" id="user_name" name="user_name" maxlength="30" placeholder="ユーザー名を入力してください">
          </div>
          <div class="mb-3 form-group d-lg-flex align-items-center col-lg-8">
            <label for="login_id" class="form-label col-lg-2 col-md-3">ログインID</label>
            <input type="text" class="form-control" id="login_id" name="login_id" placeholder="ログインID（メールアドレス）を入力してください">
          </div>
          <div class="mb-3 form-group d-lg-flex align-items-center">
            <div class="mb-3 col-lg-3 col-md-4">
              <label for="authority" class="form-label">権限</label>
              <select class="form-select" id="authority" name="authority">
                <option value="no" checked>指定しない</option>
                <option value="0">一般</option>
                <option value="1">管理者</option>
                <option value="2">統括者</option>
              </select>
            </div>
            <div class="mb-3 col-lg-3 col-md-4 mx-3">
              <label for="which" class="form-label">AND / OR</label>
              <select class="form-select" id="which" name="which">
                <option value="AND" checked>AND</option>
                <option value="OR">OR</option>
              </select>
            </div>
            <div class="d-flex justify-content-center align-items-center mt-3">
              <button type="button" class="search do-btn btn btn-primary">この条件で検索</button>
            </div>
          </div>
          <br />
        </form>

        <table id="userTable" class="table table-bordered">
          <thead>
            <tr>
              <th></th>
              <th>ユーザー名</th>
              <th>登録メールアドレス</th>
              <th>権限</th>
              <th>利用状態</th>
              <th>登録日</th>
              <th>更新日</th>
            </tr>
          </thead>
          <tbody>
            <!-- ユーザー情報をここに表示 -->
          </tbody>
        </table>

      </div>
    </div>
  </main>
  <?php include("../parts/modal.html"); ?>
  <?php echo $bootset("foot"); ?>
  <?php echo $datatableset("foot") ?>
  <script src="../../static/js/function.js"></script>
  <script>
    let data_table;
    $(document).ready(function() {
      let send_url = "../../server/api/userinfo.php?user_list=all";
      data_table = $('#userTable').DataTable(getDt_settings(send_url));
      resetTableOverflowX();
    });

    $(function() {
      $(document).on('click', ".search", function() {
        let search_param = $("#search_account").serialize();
        let send_url = "../../server/api/userinfo.php?user_list=searched&" + search_param;

        // 画面遷移後にDatatablesが初期化されているのを破棄。検索後の状態に再構築できるようにする。
        if ($.fn.dataTable.isDataTable('#userTable')) {
          data_table.destroy();
          data_table = $('#userTable').DataTable(getDt_settings(send_url));
          resetTableOverflowX();
        }
      });
    });

    function getDt_settings(send_url) {
      let res_object = {
        ajax: {
          url: send_url,
          dataSrc: ''
        },
        columns: [{
            data: 'id',
            title: '',
            render: function(data, type, full) {
              return '<button type="button" class="btn btn-primary" ' +
                'id="edit_' + data + '" data-user_id="' + full.id + '" data-login_id="' + full.login_id + '"' +
                'onclick="jumpForEdit()">edit</button>';
            },
            sortable: false,
          },
          {
            data: 'name',
            title: 'ユーザー名'
          },
          {
            data: 'login_id',
            title: 'ログインID'
          },
          {
            data: 'authority',
            title: '権限',
            render: function(data, type, full) {
              let auth_name = '';
              switch (data) {
                case 2:
                  auth_name = '統括者';
                  break;
                case 1:
                  auth_name = '管理者';
                  break;
                default:
                  auth_name = '一般';
                  break;
              }
              return auth_name;
            }
          },
          {
            data: 'is_stopped',
            title: '利用状態',
            render: function(data, type, full) {
              let condition = (data == 1) ? '停止中' : '';
              return condition;
            }
          },
          {
            data: 'created_at',
            title: '登録日'
          },
          {
            data: 'updated_at',
            title: '更新日'
          },
        ],
        info: false,
        searching: false,
      }

      return res_object;
    }

    function jumpForEdit() {
      const user_id = event.target.dataset.user_id;
      const login_id = event.target.dataset.login_id;
      location.href = "./user_edit.php?id=" + user_id + "&login_id=" + login_id;
    }

    function resetTableOverflowX() {
      $("#search_account").next('div').children().css('overflow-x', 'auto');
    }
  </script>
</body>

</html>