<?php

try {
  include("../../server/properties.php");
  include("../../server/class/UserData.php");
  include("../../server/class/SQLCruds.php");

  $this_page = "登録ノート編集";
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
  if (!isset($_GET['id']) || !isset($_GET['user_id'])) {
    header('Location: ./index.php?req_error=3');
  } else {
    // データベースに接続
    $connection = new PDO('mysql:host=' . $dsn . ';dbname=' . $dbname, $username, $password);

    $posted = array('id' => $_GET['id'], 'user_id' => $_GET['user_id']);
    $isnt_exist = SQLCruds::isntNoteIdOrUserIdExist($connection, $dbname, $posted);
    if ($isnt_exist == 1) header('Location: ./index.php?req_error=4');
    $note_row = SQLCruds::getUserNoteWithIdAndUserId($connection, $dbname, $posted);
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
          <input type="hidden" class="form-control" id="post_type" name="post_type" value="note_edit">
          <input type="hidden" class="form-control" id="note_id" name="id" value="<?php echo $note_row['id'] ?>">
          <input type="hidden" class="form-control" id="created_user_id" name="created_user_id" value="<?php echo $_SESSION['login_user']['id'] ?>">
          <div class="mb-3 form-group has-validation">
            <label for="title">ノートタイトル</label>
            <input type="text" class="form-control" id="title" placeholder="ノートのタイトルを入力してください。" name="title" required maxlength="60" value="<?php echo $note_row['title'] ?>">
            <div class="invalid-feedback">ノートのタイトルが入力されていません。</div>
          </div>
          <div class="mb-3 form-group has-validation">
            <label for="tags">タグ</label>
            <input type="text" class="form-control" id="tags" placeholder="ノートのタグを入力できます。" name="tags" maxlength="30" value="<?php echo $note_row['tags'] ?>">
          </div>
          <div class="mb-3 form-group has-validation">
            <label for="url">URL</label>
            <input type="url" class="form-control" id="url" placeholder="URLを入力できます。" name="url" pattern="^https?://.+" value="<?php echo $note_row['url'] ?>">
            <div class="invalid-feedback">入力内容がURL形式ではありません。</div>
          </div>
          <div class="confirm-hider mb-3 form-group has-validation">
            <label for="note">ノート本文</label>
            <textarea class="form-control" rows="5" id="note" name="note" required><?php echo $note_row['note'] ?></textarea>
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
            <button type="button" class="set-modals do-btn btn btn-primary">これで更新する</button>
            <button type="button" class="set-modals delete-btn btn btn-danger mx-2">削除する</button>
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
      const modal_confirm_title = "ノート更新 - 確認";
      const modal_confirm_body = "この内容でノートを更新しますか？";
      const modal_complete_title = "ノート更新 - 完了";
      const modal_complete_body = "ノートを更新しました。";
      $("#confirmModal").find("#confirmModalLabel").text(modal_confirm_title);
      $("#confirmModal").find(".modal-body").text(modal_confirm_body);
      $("#completeModal").find("#completeModalLabel").text(modal_complete_title);
      $("#completeModal").find(".modal-body").text(modal_complete_body);

      $(document).on('click', ".set-modals", function() {
        if ($(this).hasClass("delete-btn")) {
          const deletemodal_confirm_title = "ノート削除 - 確認";
          const deletemodal_confirm_body = "このノートを削除しますか？";
          const deletemodal_complete_title = "ノート削除 - 完了";
          const deletemodal_complete_body = "ノートを削除しました。";
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

        // post_typeを、ノート削除用設定の「note_delete」に設定する
        $("[name='post_type']").val("note_delete");

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

        // post_typeを、初期設定の「note_edit」に戻す
        $("[name='post_type']").val("note_edit");

        // 対象のボタンからモーダル表示機能を除去するJavaScript
        delete_btn.removeAttribute('data-bs-toggle');
        delete_btn.removeAttribute('data-bs-target');
      })

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