// ノート新規作成・編集 共通機能
$(function() {

  // ノート本文の入力内容をバイト数に換算
  let byteCount = encodeURI($('[name="note"]').val()).replace(/%../g, "*").length;

  $(document).ready(function() {
    $('#byte-count').text(byteCount);

    const append_resetter = '<div class="d-flex justify-content-center align-items-center">'
      + '<button id="resetMarks" type="button" class="btn btn-primary">本文中のマークをすべて除去</button>'
      + '</div><br />';
    $('.confirm-hider').eq(1).after(append_resetter);
  });

  // ノート本文入力欄で入力がある度にバイト数を計算
  $(document).on('input', '[name="note"]', function() {
    let byteCount_current = encodeURI($(this).val()).replace(/%../g, "*").length;
    $('#byte-count').text(byteCount_current);
  });

  // 各色マークボタン押下時に選択範囲の前後に文字列追加
  $(document).on('click', ".mark-btn", function() {
    const btn_id = $(this).attr('id');
    let inputText = $("[name='note']").val();

    // 入力欄内の選択範囲を指定
    const selectionStart = $("[name='note']")[0].selectionStart;
    const selectionEnd = $("[name='note']")[0].selectionEnd;
    const selectedText = inputText.substring(selectionStart, selectionEnd);

    // 選択範囲が1文字以上ある場合に実行
    if (selectedText.length > 0) {
      let beforeTag = "";
      let afterTag = "";
      if (btn_id == "btnRedUnitMark") {
        beforeTag = "##red##";
        afterTag = "#$red$#";
      } else if (btn_id == "btnGreenMark") {
        beforeTag = "##green##";
        afterTag = "#$green$#";
      } else {
        beforeTag = "##orange##";
        afterTag = "#$orange$#";
      }

      // 選択範囲の文字列の前に、ランダム生成された文字列を挿入
      const randStr = generatedRandomStr();
      const targetText = randStr + selectedText;

      // targetTextの前後にbeforeTagとafterTagを添える
      const modifiedText = beforeTag + targetText + afterTag;

      // inputTextを、一旦下記のように選択範囲を変換したものに置き換える
      inputText = $("[name='note']").val().substring(0, selectionStart)
        + modifiedText.replace(randStr, "")
        + $("[name='note']").val().substring(selectionEnd);
      $("[name='note']").val(inputText);

      // 入力欄内の選択状態を解除
      window.getSelection().removeAllRanges();

      byteCount = $('[name="note"]').val().replace(/[^\x00-\xff]/g, '**').length;
      $('#byte-count').text(byteCount);
    }
  });

  // 本文中で選択・マークした箇所をすべて未マーク状態に戻す
  $(document).on('click', "#resetMarks", function () {
    let inputText = $("[name='note']").val();
    const resetText = inputText.replaceAll(/#(#|\$)(red|green|orange)(#|\$)#/g, "");
    let do_reset = confirm("マークした箇所をすべて未マーク状態に戻します。\nよろしいですか？")
    if(do_reset) $("[name='note']").val(resetText);
  });

  // ノート本文入力欄を一時的に非表示にして、入力内容確認エリアを表示
  $(document).on('click', "#confirmHide", function() {
    let inputText = $("[name='note']").val();

    if (inputText != "" && byteCount < 65000) {
      $("#do-btn-area, .confirm-hider").hide();
      $("#do-btn-area, .confirm-hider").find('button').hide();
      $("#do-btn-area, .confirm-hider").find('label').hide();
      $("#confirmation").fadeIn(500);

      const convertedText = inputText.replaceAll(/\r?\n/g, "<br />")
        .replaceAll("##red##", "<span class='red-mark'>")
        .replaceAll("##green##", "<span class='green-mark'>")
        .replaceAll("##orange##", "<span class='orange-check mx-1'>")
        .replaceAll(/#\$(red|green|orange)\$#/g, "</span>");
      $("#confirmedText").html(convertedText);
    } else {
      // 入力欄未入力か、総バイト数が65000以上だった場合はエラーメッセージを表示
      if (inputText == "") showCantConfirm(0);
      if (byteCount >= 65000) showCantConfirm(1);
    }
  });

  // 入力内容確認エリアを非表示にして、ノート本文入力欄を再度表示する
  $(document).on('click', "#backToInput", function() {
    $("#do-btn-area, .confirm-hider").fadeIn(500);
    $("#do-btn-area, .confirm-hider").find('button').fadeIn(500);
    $("#do-btn-area, .confirm-hider").find('label').fadeIn(500);
    $("#confirmation").hide();
  });
});

function showCantConfirm(eq) {
  $(".cant-confirm").eq(eq).show();
  setTimeout(function() {
    $(".cant-confirm").eq(eq).hide();
  }, 2000);
}

// 半角英数字から、4文字のランダムな文字列を生成して、$を両端に添えて出力する
function generatedRandomStr() {
  const characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
  let result = '';
  for (let i = 0; i < 4; i++) {
    const randomIndex = Math.floor(Math.random() * characters.length);
    result += characters.charAt(randomIndex);
  }
  
  return `\$${result}\$`;
}