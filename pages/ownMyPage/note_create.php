<?php

try {
  include("../../server/properties.php");
  include("../../server/class/UserData.php");
  include("../../server/class/SQLCruds.php");

  $this_page = "ノート新規登録";
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
</head>

<body class="contents">
  <?php include("../parts/header.php") ?>
  <main class="create">
    <div class="white-board p-2">
      <div class="container mt-3">
        <h2 class="contents-title p-2 col-lg-5 text-center"><?php echo $this_page ?></h2>
        <br />
        <form id="note_create" class="needs-validation" novalidate>
          <input type="hidden" class="form-control" id="access_token" name="access_token" value="<?php echo $_SESSION['access_token'] ?>">
          <input type="hidden" class="form-control" id="post_type" name="post_type" value="note_create">
          <input type="hidden" class="form-control" id="created_user_id" name="created_user_id" value="<?php echo $_SESSION['login_user']['id'] ?>">
          <div class="mb-3 form-group has-validation">
            <label for="title">ノートタイトル</label>
            <input type="text" class="form-control" id="title" placeholder="ノートのタイトルを入力してください。" name="title" required maxlength="60">
            <div class="invalid-feedback">ノートのタイトルが入力されていません。</div>
          </div>
          <div class="mb-3 form-group has-validation">
            <label for="tags">タグ</label>
            <input type="text" class="form-control" id="tags" placeholder="ノートのタグを入力できます。" name="tags" maxlength="30">
          </div>
          <div class="mb-3 form-group has-validation">
            <label for="url">URL</label>
            <input type="url" class="form-control" id="url" placeholder="URLを入力できます。" name="url" pattern="^https?://.+">
            <div class="invalid-feedback">入力内容がURL形式ではありません。</div>
          </div>
          <div class="confirm-hider mb-3 form-group has-validation">
            <label for="note">ノート本文</label>
            <textarea class="form-control" rows="5" id="note" name="note" required></textarea>
            <div class="invalid-feedback">ノート本文が入力されていません。</div>
          </div>
          <p align="center">現在の本文総バイト数 ： <span id="byte-count">0</span>バイト</p>
          <div class="confirm-hider d-md-flex justify-content-center align-items-center mb-2">
            <label class="d-none d-md-block mt-2">選択範囲：</label>
            <button type="button" id="btnRedUnitMark" class="btn mark-btn mx-2 mt-2 btn-danger">赤見出しマーク</button>
            <button type="button" id="btnGreenMark" class="btn mark-btn mx-2 mt-2 btn-success">緑色マーク</button>
            <button type="button" id="btnOrangeCheck" class="btn mark-btn mx-2 mt-2 btn-custom-orange">オレンジチェック</button>
          </div>
          <div class="confirm-hider d-flex justify-content-center align-items-center">
            <button id="confirmHide" type="button" class="btn btn-primary">本文の出力内容を確認</button>
          </div>
          <div align="center" class="load-hide invalid-feedback cant-confirm">ノート本文を入力してください。</div>
          <div align="center" class="load-hide invalid-feedback cant-confirm">ノート本文の文字数量が上限を超過しています。</div>
          <div id="confirmation" class="load-hide">
            <br />
            <h3>入力内容確認</h3>
            <p id="confirmedText"></p>
            <div class="d-flex justify-content-center align-items-center">
              <button type="button" class="btn btn-secondary" id="backToInput">入力に戻る</button>
            </div>
          </div>
          <br />
          <div id="do-btn-area" class="d-flex justify-content-center align-items-center">
            <button type="button" class="do-btn btn btn-primary">これで登録する</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <?php include("../parts/modal.html"); ?>
  <?php echo $bootset("foot"); ?>
  <?php echo $datatableset("foot") ?>
  <script src="../../static/js/function.js"></script>
  <script src="../../static/js/note_input.js"></script>
  <script>
    $(function() {

      // #confirmModalのタイトルと本文の文言を設定する
      const modal_confirm_title = "ノート新規登録 - 確認";
      const modal_confirm_body = "この内容でノートを登録しますか？";
      const modal_complete_title = "ノート新規登録 - 完了";
      const modal_complete_body = "ノートを登録しました。";
      $("#confirmModal").find("#confirmModalLabel").text(modal_confirm_title);
      $("#confirmModal").find(".modal-body").text(modal_confirm_body);
      $("#completeModal").find("#completeModalLabel").text(modal_complete_title);
      $("#completeModal").find(".modal-body").text(modal_complete_body);

      $(document).on('click', ".execute", function() {
        // フォームの各name付き要素をシリアライズして送信できるようにする
        const form_info = $("#note_create").serialize();

        // ajax送信用情報をオブジェクトに取り纏める
        const post_info = {
          type: "POST",
          url: "../../server/api/noteinfo.php",
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