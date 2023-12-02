<?php

try {
  include("../../server/properties.php");
  include("../../server/class/UserData.php");
  include("../../server/class/ValidationUtil.php");
  include("../../server/class/SQLCruds.php");

  $this_page = "ユーザー新規登録";
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
  <?php echo $fontset; ?>
  <link href="../../static/css/style.css" rel="stylesheet" />
  <link rel="icon" href="../../images/favicon.ico">
</head>

<body class="contents">
  <?php include("../parts/header.php") ?>

  <main class="admin create">
    <div class="admin white-board p-2">
      <div class="container mt-3">
        <h2 class="contents-title p-2 col-lg-5 text-center"><?php echo $this_page ?></h2>
        <br />
        <form id="regist_account" class="needs-validation" novalidate>
          <input type="hidden" id="access_token" name="access_token" value="<?php echo $_SESSION['access_token'] ?>">
          <input type="hidden" id="post_type" name="post_type" value="user_create">
          <div class="mb-3 form-group has-validation">
            <label for="user_name" class="form-label">ユーザー名</label>
            <input type="text" class="form-control" id="user_name" name="user_name" maxlength="30" required placeholder="アカウントのユーザー名を入力してください">
            <div class="invalid-feedback">ユーザー名を入力してください。</div>
          </div>
          <div class="mb-3 form-group has-validation">
            <label for="login_id" class="form-label">ログインID</label>
            <input type="text" class="form-control" id="login_id" name="login_id" pattern="^[a-zA-Z0-9_.\+\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+" required placeholder="アカウントのログインID（メールアドレス）を入力してください">
            <div class="invalid-feedback">ログインIDをメールアドレス形式で入力してください。</div>
          </div>
          <div class="mb-3 form-group has-validation">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" class="form-control" id="password" name="password" pattern="^[a-zA-Z0-9\-_]{8,16}$" required placeholder="パスワード（半角英数字8～16字）を入力してください">
            <div class="invalid-feedback">パスワードを半角英数字8～16字で入力してください。</div>
          </div>
          <div class="mb-3 col-sm-2">
            <label for="authority" class="form-label">権限</label>
            <select class="form-select" id="authority" name="authority">
              <option value="0" checked>一般</option>
              <option value="1">管理者</option>
            </select>
          </div>
          <div class="mb-3 form-group">
            <label for="user_name" class="form-label">個別コメント</label>
            <input type="text" class="form-control" id="comment" name="comment" maxlength="100" placeholder="個別コメントがあれば入力してください（100字以内）">
          </div>
          <br />
          <div class="d-flex justify-content-center align-items-center">
            <button type="button" class="do-btn btn btn-primary">これで登録する</button>
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
      const modal_confirm_title = "ユーザー新規登録 - 確認";
      const modal_confirm_body = "この内容でユーザーを登録しますか？";
      const modal_complete_title = "ユーザー新規登録 - 完了";
      const modal_complete_body = "ユーザーを登録しました。";
      $("#confirmModal").find("#confirmModalLabel").text(modal_confirm_title);
      $("#confirmModal").find(".modal-body").text(modal_confirm_body);
      $("#completeModal").find("#completeModalLabel").text(modal_complete_title);
      $("#completeModal").find(".modal-body").text(modal_complete_body);

      $(document).on('click', ".execute", function() {
        // フォームの各name付き要素をシリアライズして送信できるようにする
        const form_info = $("#regist_account").serialize();

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

      });
    });
  </script>
</body>

</html>