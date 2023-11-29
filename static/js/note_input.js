// ノート新規作成・編集 共通機能
$(function() {

  // ノート本文の入力内容をバイト数に換算
  let byteCount = $('[name="note"]').val().replace(/[^\x00-\xff]/g, '**').length;

  $(document).ready(function() {
    $('#byte-count').text(byteCount);
  });

  // ノート本文入力欄で入力がある度にバイト数を計算
  $(document).on('input', '[name="note"]', function() {
    let byteCount_current = $(this).val().replace(/[^\x00-\xff]/g, '**').length;
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

      // 選択範囲の前後にbeforeTagとafterTagを追加
      const modifiedText = beforeTag + selectedText + afterTag;

      // 既にマークされている選択範囲のキーワードは、一時的にダミーの文字列に変換する
      const escapeKeywords = "#" + selectedText + "#";
      const escapedVal = inputText.replaceAll(escapeKeywords, "__escaped__");

      // 入力欄内の入力内容をmodifiedTextに更新する
      let renewedText = escapedVal.replace(selectedText, modifiedText);

      // 更新したら、ダミーの文字列を元の文字列に戻す
      renewedText = renewedText.replaceAll("__escaped__", escapeKeywords);
      $("[name='note']").val(renewedText);

      // 入力欄内の選択状態を解除
      window.getSelection().removeAllRanges();

      byteCount = $('[name="note"]').val().replace(/[^\x00-\xff]/g, '**').length;
      $('#byte-count').text(byteCount);
    }

  });

  // ノート本文入力欄を一時的に非表示にして、入力内容確認エリアを表示
  $(document).on('click', "#confirmHide", function() {
    let inputText = $("[name='note']").val();

    if (inputText != "" && byteCount < 65000) {
      $("#do-btn-area, .confirm-hider").hide();
      $("#do-btn-area, .confirm-hider").find('button').hide();
      $("#do-btn-area, .confirm-hider").find('label').hide();
      $("#confirmation").fadeIn(500);

      const convertedText = inputText
        .replaceAll(/\r?\n/g, "<br />")
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