<?php

try {
  include("../../server/properties.php");
  include("../../server/class/UserData.php");
  include("../../server/class/SQLCruds.php");

  $this_page = "ユーザーの登録ノート";
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
        <h3 class="topix-index p-1 col-lg-4 load-hide">タイトル・最終閲覧日</h3>
        <p id="note_title" class="load-hide mx-2">
          <?php
          $last_viewed_txt = " （最終閲覧日：" . $note_row['last_viewed_at'] . "）";
          if ($note_row['url'] != "") {
            echo '<a target="_blank" href="' . $note_row['url'] . '" style="text-decoration:none;">' . $note_row['title'] . '</a>' . $last_viewed_txt;
          } else {
            echo $note_row['title'] . $last_viewed_txt;
          }
          ?>
        </p>
        <br />
        <h3 class="topix-index p-1 col-lg-4 load-hide">タグ</h3>
        <p id="note_tags" class="load-hide mx-2"><?php echo ($note_row['tags'] == '' || $note_row['tags'] == null) ? 'タグは登録されていません' : $note_row['tags'] ?></p>
        <br />
        <h3 class="topix-index p-1 col-lg-4 load-hide">ノートテキスト</h3>
        <p id="note_text" class="load-hide mx-2"><?php echo $note_row['note'] ?></p>
        <div id="extracted-btn-area" class="d-flex justify-content-center align-items-center">
          <button type="button" class="btn btn-success">マーク・チェックを抽出</button>
        </div>
        <div id="extracted-area" style="display:none;"></div>
        <div id="extracted-reset-btn-area" class="d-flex justify-content-center align-items-center" style="display:none;">
          <button type="button" class="btn btn-secondary" style="display:none;">元に戻す</button>
        </div>
        <br />
        <div id="do-btn-area" class="d-flex justify-content-center align-items-center">
          <form id="update_lastviewed" class="needs-validation">
            <input type="hidden" class="form-control" id="access_token" name="access_token" value="<?php echo $_SESSION['access_token'] ?>">
            <input type="hidden" class="form-control" name="id" value="<?php echo $_GET['id'] ?>">
            <input type="hidden" class="form-control" name="post_type" value="update_lastview">
            <button type="button" class="do-btn btn btn-primary">閲覧日を更新する</button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <?php include("../parts/modal.html"); ?>
  <?php echo $bootset("foot"); ?>
  <?php echo $datatableset("foot") ?>
  <script src="../../static/js/function.js"></script>
  <script>
    $(function() {
      // 選択したノートの登録内容をフェードインで表示
      $(document).ready(function() {
        let noteText = $("#note_text").text();
        const convertedText = noteText
          .replaceAll(/\r?\n/g, "<br />")
          .replaceAll("##red##", "<span class='note-marked red-mark'>")
          .replaceAll("##green##", "<span class='note-marked green-mark'>")
          .replaceAll("##orange##", "<span class='note-marked orange-check blind-clicker mx-1'>")
          .replaceAll(/#\$(red|green|orange)\$#/g, "</span>");
        $("#note_text").html(convertedText);
        $(".load-hide").fadeIn(500);

        let marked_objects_array = [];
        let randomString = Math.random().toString(36).substring(2, 12);

        // ノートテキスト内でマークされている文字列を抽出して纏める
        $(".note-marked").each(function() {
          let this_color = "";
          const this_class = $(this).attr('class');
          if (this_class.indexOf('red') > -1) {
            this_color = "red";
            randomString = Math.random().toString(36).substring(2, 12);
          } else if (this_class.indexOf('green') > -1) {
            this_color = "green";
          } else {
            this_color = "orange";
          }
          marked_objects_array.push({
            "id": randomString,
            "color": this_color,
            "text": $(this).text(),
          });
        });

        // marked_objects_array を'id'でグループ化したオブジェクト配列を生成する
        let grouped_array = [];
        let groupedObjects = {};
        marked_objects_array.forEach(obj => {
          const propValue = obj['id'];
          if (!groupedObjects[propValue]) {
            groupedObjects[propValue] = [];
          }
          groupedObjects[propValue].push({
            "color": obj.color,
            "text": obj.text,
          });
        });
        grouped_array = Object.keys(groupedObjects).map(key => ({
          'id': key,
          'objects': groupedObjects[key]
        }));

        let add_html = '';
        grouped_array.forEach(function(row) {
          let inner_span = "";
          row.objects.forEach(function(inner, i) {
            if (i == 0 && inner.color == 'red') {
              inner_span = "<span class='note-marked red-mark'>" + inner.text + "</span>";
              add_html += '<p class="extracted-red-index note-marked red-mark p-2" href="#' + row.id + '">' + inner_span + '</p>' +
                '<div id="' + row.id + '" class="extracted-others"><ul style="display:none;">';
            } else if (i == 0 && inner.color != 'red') {
              inner_span = "<span class='note-marked red-mark'>First Unit</span>";
              add_html += '<p class="extracted-red-index note-marked red-mark p-2" href="#' + row.id + '">' + inner_span + '</p>' +
                '<div id="' + row.id + '" class="extracted-others"><ul style="display:none;">';
            } else {
              if (inner.color == 'green') inner_span = "<span class='note-marked green-mark'>" + inner.text + "</span>";
              if (inner.color == 'orange') inner_span = "<span class='note-marked orange-check blind-clicker mx-1'>" + inner.text + "</span>";
              add_html += '<li class="extracted-rows">' + inner_span + '</li>';
            }
            if (i == row.objects.length - 1) add_html += '</ul></div>';
          });
        });
        $("#extracted-area").html(add_html);

        // 「マーク・チェックを抽出」ボタン押下時機能
        const extract_btn = $("#extracted-btn-area").find('button');
        extract_btn.on('click', function() {
          // 「元に戻す」ボタンがあるエリアと「#extracted-area」エリアをフェードインで表示する
          $("#extracted-area").fadeIn(500);
          $("#extracted-reset-btn-area").fadeIn(500);
          $("#extracted-reset-btn-area").find('button').fadeIn(500);
          $(".topix-index").eq(2).text("マークした文字を抽出");

          // 「#note_text」「#extracted-btn-area」の構成要素は非表示にする
          $("#note_text").fadeOut(500);
          $("#extracted-btn-area").hide();
          $("#extracted-btn-area").find('button').hide();
        });

        // 「元に戻す」ボタン押下時機能
        const extract_reset_btn = $("#extracted-reset-btn-area").find('button');
        extract_reset_btn.on('click', function() {
          // 「元に戻す」ボタンがあるエリアと「#extracted-area」エリアを非表示にする
          $("#extracted-area").hide();
          $("#extracted-reset-btn-area").hide();
          $("#extracted-reset-btn-area").find('button').hide();

          $(".topix-index").eq(2).text("ノートテキスト");
          $("#extracted-area").html(add_html);

          // 「#note_text」「#extracted-btn-area」の構成要素はフェードインで再表示にする
          $("#note_text").fadeIn(500);
          $("#extracted-btn-area").fadeIn(500);
          $("#extracted-btn-area").find('button').fadeIn(500);
        });

        // 抽出したマーク文字群の見出し（赤文字）押下時トグル機能
        $(document).on('click', ".extracted-red-index", function() {
          const for_id = $(this).attr('href');
          if ($(this).hasClass('spread')) {
            $(for_id).hide();
            $(for_id).find('*').hide();
            $(this).removeClass('spread');
          } else {
            $(this).addClass('spread');
            $(for_id).fadeIn(500);
            $(for_id).find('*').fadeIn(500);
          }
        });
      });

      // #confirmModalのタイトルと本文の文言を設定する
      const modal_confirm_title = "ノート更新 - 確認";
      const modal_confirm_body = "このノートの閲覧日を更新しますか？";
      const modal_complete_title = "ノート更新 - 完了";
      const modal_complete_body = "ノートの閲覧日を更新しました。";
      $("#confirmModal").find("#confirmModalLabel").text(modal_confirm_title);
      $("#confirmModal").find(".modal-body").text(modal_confirm_body);
      $("#completeModal").find("#completeModalLabel").text(modal_complete_title);
      $("#completeModal").find(".modal-body").text(modal_complete_body);

      $(document).on('click', ".execute", function() {
        // フォームの各name付き要素をシリアライズして送信できるようにする
        const form_info = $("#update_lastviewed").serialize();

        // ajax送信用情報をオブジェクトに取り纏める
        const post_info = {
          type: "POST",
          url: "../../server/api/noteinfo.php",
          data: form_info,
          dataType: "json"
        };
        $.ajax(post_info).done(function(data) {
          modalsShowHide();
          console.log(data);
        }).fail(function(XMLHttpRequest, status, e) {
          alert(e);
        });
      });

      // 非表示になっている.orange-checkを表示する（表示されている.orange-checkは非表示化する）
      $(document).on('click', ".orange-check", function() {
        if ($(this).hasClass("blind-clicked")) {
          $(this).removeClass("blind-clicked");
          $(this).addClass("blind-clicker");
        } else {
          $(this).removeClass("blind-clicker");
          $(this).addClass("blind-clicked");
        }
      });

    });
  </script>
</body>

</html>