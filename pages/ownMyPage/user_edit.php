<?php

try {
  include("../../server/properties.php");
  include("../../server/class/UserData.php");
  include("../../server/class/ValidationUtil.php");
  include("../../server/class/SQLCruds.php");

  $this_page = "ユーザー編集";
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

  // login_idがセットされていなければ、インデックス画面にリダイレクトする
  if (!isset($_GET['login_id'])) {
    header('Location: ./index.php?req_error=1');
  } else {
    // データベースに接続
    $connection = new PDO('mysql:host=' . $dsn . ';dbname=' . $dbname, $username, $password);

    $posted = array('id' => $_GET['id'], 'login_id' => $_GET['login_id']);
    $isnt_exist = SQLCruds::isntUserAccountExist($connection, $dbname, $posted);
    if ($isnt_exist == 1) header('Location: ./index.php?req_error=2');
    $user_data = SQLCruds::getUserAccount($connection, $dbname, $posted);
    $edit_for = ($_SESSION['login_user']['login_id'] == $_GET['login_id']) ? "me" : "other";
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
  <?php echo $fontset; ?>
  <link href="../../static/css/style.css" rel="stylesheet" />
</head>

<body class="contents">
  <?php include("../parts/header.php") ?>

  <main class="<?php if ($edit_for == "other") echo 'admin ' ?>edit">
    <div class="<?php if ($edit_for == "other") echo 'admin ' ?>white-board p-2">
      <div class="container mt-3">
        <h2 class="contents-title p-2 col-lg-5 text-center"><?php echo $this_page ?></h2>
        <input type="hidden" id="edit_for" value="<?php echo $edit_for ?>">
        <br />
        <form id="edit_account" class="needs-validation" novalidate>
          <input type="hidden" id="access_token" name="access_token" value="<?php echo $_SESSION['access_token'] ?>">
          <input type="hidden" id="user_id" name="id" value="<?php echo $_GET['id'] ?>">
          <input type="hidden" id="post_type" name="post_type" value="user_edit">
          <?php if ($edit_for == "me") : ?>
            <div class="mb-3 form-group has-validation">
              <label for="user_name" class="form-label">ユーザー名</label>
              <input type="text" class="form-control" id="user_name" name="user_name" maxlength="30" required placeholder="アカウントのユーザー名を入力してください" value="<?php echo $user_data->getUser_name() ?>">
              <div class="invalid-feedback">ユーザー名を入力してください。</div>
            </div>
          <?php endif; ?>
          <div class="mb-3 form-group has-validation">
            <label for="login_id" class="form-label">ログインID</label>
            <input type="text" class="form-control" id="login_id" name="login_id" pattern="[a-zA-Z0-9_.\+\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+" required placeholder="アカウントのログインID（メールアドレス）を入力してください" value="<?php echo $user_data->getLogin_id() ?>" <?php if ($edit_for != "me") echo 'disabled';  ?>>
            <div class="invalid-feedback">ログインIDをメールアドレス形式で入力してください。</div>
          </div>
          <?php if ($edit_for == "me") : ?>
            <div class="mb-3 form-group has-validation">
              <label for="password" class="form-label">新パスワード</label>
              <input type="password" class="form-control" id="password" name="password" pattern="(^$|^[a-zA-Z0-9\-_]{8,16}$)" placeholder="パスワード（半角英数字8～16字）を入力してください">
              <div id="errormessage_pass" class="invalid-feedback">新パスワードは半角英数字8～16字で入力してください。</div>
            </div>
            <div class="mb-3 form-group has-validation">
              <label for="password_again" class="form-label">新パスワード（再入力）</label>
              <input type="password" class="form-control" id="password_again" name="password_again" pattern="(^$|^[a-zA-Z0-9\-_]{8,16}$)" placeholder="パスワード（半角英数字8～16字）を入力してください">
              <div id="errormessage_pass_again" class="invalid-feedback">新パスワードは半角英数字8～16字で入力してください。</div>
            </div>
          <?php endif; ?>
          <?php if ($user_data->getAuthority() != 2 && $edit_for == "other") echo '<div class="d-flex justify-content-center align-items-center">' ?>
          <?php if ($user_data->getAuthority() != 2) : ?>
            <div class="mb-3 col-sm-2">
              <label for="authority" class="form-label">権限</label>
              <select class="form-select" id="authority" name="authority">
                <option value="0" <?php if ($user_data->getAuthority() == 0) echo "selected" ?>>一般</option>
                <option value="1" <?php if ($user_data->getAuthority() == 1) echo "selected" ?>>管理者</option>
              </select>
            </div>
          <?php endif; ?>
          <?php if (($_SESSION['login_user']['authority'] >= 1 && $edit_for == "other") || ($_SESSION['login_user']['authority'] == 1 && $edit_for == "me")) : ?>
            <div class="mb-3 col-sm-2 <?php if ($user_data->getAuthority() != 2 && $edit_for == "other") echo 'mx-3' ?>">
              <label for="is_stopped" class="form-label">利用状態</label>
              <select class="form-select" id="is_stopped" name="is_stopped">
                <option value="0" <?php if ($user_data->getIs_stopped() == 0) echo "selected" ?>>利用</option>
                <option value="1" <?php if ($user_data->getIs_stopped() == 1) echo "selected" ?>>停止</option>
              </select>
            </div>
          <?php endif; ?>
          <?php if ($user_data->getAuthority() != 2 && $edit_for == "other") echo '</div>' ?>
          <?php if ($user_data->getId() == $_SESSION['login_user']['id']) : ?>
            <div class="mb-3 form-group">
              <label for="user_name" class="form-label">個別コメント</label>
              <input type="text" class="form-control" id="comment" name="comment" maxlength="100" placeholder="個別コメントがあれば入力してください（100字以内）" value="<?php echo $user_data->getComment() ?>">
            </div>
          <?php endif; ?>
          <br />
          <div class="d-flex justify-content-center align-items-center">
            <button type="button" class="set-modals do-btn btn btn-primary">これで更新する</button>
            <?php if ($user_data->getIs_stopped() == 1) : ?>
              <button type="button" class="set-modals delete-btn btn btn-danger mx-2">ユーザーを削除</button>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </main>
  <?php include("../parts/modal.html"); ?>
  <?php echo $bootset("foot"); ?>
  <script src="../../static/js/function.js"></script>
  <script>
    $(function() {
      // #confirmModalのタイトルと本文の文言を設定する
      const modal_confirm_title = "ユーザー更新 - 確認";
      const modal_confirm_body = "この内容で更新しますか？";
      const modal_complete_title = "ユーザー更新 - 完了";
      const modal_complete_body = "更新を完了しました。";
      $("#confirmModal").find("#confirmModalLabel").text(modal_confirm_title);
      $("#confirmModal").find(".modal-body").text(modal_confirm_body);
      $("#completeModal").find("#completeModalLabel").text(modal_complete_title);
      $("#completeModal").find(".modal-body").text(modal_complete_body);

      // 画面内に削除ボタンが存在する場合の機能
      if ($(".delete-btn").length > 0) {
        $(document).on('click', ".set-modals", function() {
          if ($(this).hasClass("delete-btn")) {
            const deletemodal_confirm_title = "ユーザー削除 - 確認";
            const deletemodal_confirm_body = "本当にこのユーザーを削除してもよろしいですか？";
            const deletemodal_complete_title = "ユーザー削除 - 完了";
            const deletemodal_complete_body = "ユーザーを削除しました。";
            $("#confirmModal").find("#confirmModalLabel").text(deletemodal_confirm_title);
            $("#confirmModal").find(".modal-body").text(deletemodal_confirm_body);
            $("#completeModal").find("#completeModalLabel").text(deletemodal_complete_title);
            $("#completeModal").find(".modal-body").text(deletemodal_complete_body);
          } else {
            $("#confirmModal").find("#confirmModalLabel").text(modal_confirm_title);
            $("#confirmModal").find(".modal-body").text(modal_confirm_body);
            $("#completeModal").find("#completeModalLabel").text(modal_complete_title);
            $("#completeModal").find(".modal-body").text(modal_complete_body);
          }
        });

        $(document).on('click', ".delete-btn", function() {
          let delete_btn = document.querySelector('.delete-btn');
          // post_typeを、ノート削除用設定の「user_delete」に設定する
          $("[name='post_type']").val("user_delete");
          // 対象のボタンにモーダル表示機能を付与するJavaScript
          delete_btn.setAttribute('data-bs-toggle', "modal");
          delete_btn.setAttribute('data-bs-target', "#confirmModal");

          // モーダルを表示させるJQuery
          var confirmModal = $("#confirmModal");
          confirmModal = new bootstrap.Modal(confirmModal, {
            keyboard: false,
          })

          confirmModal.show();
        });

        $('#confirmModal').on('hidden.bs.modal', function() {
          let delete_btn = document.querySelector('.delete-btn');

          // post_typeを、初期設定の「user_edit」に戻す
          $("[name='post_type']").val("user_edit");
          // 対象のボタンからモーダル表示機能を除去するJavaScript
          delete_btn.removeAttribute('data-bs-toggle');
          delete_btn.removeAttribute('data-bs-target');
        });
      }

      $(document).on('click', ".execute", function() {
        if (judgePassDifference()) {
          // フォームの各name付き要素をシリアライズして送信できるようにする
          const form_info = $("#edit_account").serialize();
          // ajax送信用情報をオブジェクトに取り纏める
          const post_info = {
            type: "POST",
            url: "../../server/api/userinfo.php",
            data: form_info,
            dataType: "json"
          };
          $.ajax(post_info).done(function(data) {
            modalsShowHide();
          }).fail(function(XMLHttpRequest, status, e) {
            alert(e);
          });
        } else {
          $("#confirmModal").modal('hide');
        }
      });

      $(document).on('input', "[name='password'], [name='password_again']", function() {
        if ($("#edit_account").hasClass("was-validated")) {
          judgePassDifference();
        }
      });
    });

    // 更新対象アカウントがログインユーザーだった場合に適用
    function judgePassDifference() {
      let res = true;

      const default_password_txt = "新パスワードは半角英数字8～16字で入力してください。";
      if ($("#edit_for").val() == "me") {
        const pass = $("[name='password']").val();
        const pass_again = $("[name='password_again']").val();
        if (pass != pass_again) {
          const pass_difference_message = "入力されたパスワードが一致していません。";
          $("[name='password']")[0].setCustomValidity(pass_difference_message);
          $("[name='password_again']")[0].setCustomValidity(pass_difference_message);
          $("#errormessage_pass").text("");
          $("#errormessage_pass_again").text(pass_difference_message);
          res = false;
        } else {
          $("[name='password']")[0].setCustomValidity("");
          $("[name='password_again']")[0].setCustomValidity("");
          $("#errormessage_pass").text(default_password_txt);
          $("#errormessage_pass_again").text(default_password_txt);
        }
      }

      return res;
    }
  </script>
</body>

</html>