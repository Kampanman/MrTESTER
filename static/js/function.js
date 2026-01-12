
$(function () {
  // mainエリアの画面内での縦の長さを設定
  $("main").height($(window).innerHeight());

  // ハンバーガーボタン押下時のメニュー表示機能
  $(document).on('click', "#hamburger", function() {
    if ($(this).prop('class').indexOf("clicked") == -1) {
      $(this).addClass("clicked");
      $("#rightside_area nav").addClass("show");
    } else {
      $(this).removeClass("clicked");
      $("#rightside_area nav").removeClass("show");
    }
  });
});

$(function () {
  'use strict'
  // バリデーションが必要なフォームを定義する
  var forms = document.querySelectorAll('.needs-validation');

  // 画面内に設置されているバリデーションを要するすべてのフォームで実行
  Array.prototype.slice.call(forms)
    .forEach(function (form, i) {
      let do_btn = form.querySelector('.do-btn');
      do_btn.addEventListener('click', function (event) {
        if (form.checkValidity()) {
          if (i == 0) {
            // 対象のボタンにモーダル表示機能を付与するJavaScript
            do_btn.setAttribute('data-bs-toggle', "modal");
            do_btn.setAttribute('data-bs-target', "#confirmModal");
            // モーダルを表示させるJQuery
            var confirmModal = $("#confirmModal");
            confirmModal = new bootstrap.Modal(confirmModal, {
              keyboard: false,
            })
            confirmModal.show();
          }
        }
        form.classList.add('was-validated');
      }, false)
    });
  
  $('#confirmModal').on('hidden.bs.modal', function () {
    Array.prototype.slice.call(forms)
      .forEach(function (form, i) {
        let do_btn = form.querySelector('.do-btn');
        // 対象のボタンからモーダル表示機能を除去するJavaScript
        do_btn.removeAttribute('data-bs-toggle');
        do_btn.removeAttribute('data-bs-target');
      });
  })
});

function modalsShowHide() {
  $("#confirmModal").modal('hide');
  
  setTimeout(function () {
    $("#completeModal").find(".shut").attr('onclick', "jumpForIndex()");
    $("#completeModal").find(".btn-close").attr('onclick', "jumpForIndex()");
    $("#completeModal").modal('show');
  }, 1000);
}

function jumpForIndex() {
  setTimeout(function () {
    location.href = "./index.php";
  }, 1000);
}