<?php
include('../server/properties.php');
$page_name = "ログインページ";
/* セッション開始 */
session_start();
if ($_GET['logout'] == 1) {
  //セッションの中身をすべて削除して空にする
  $_SESSION = array();
  // リロード時にセッションを破棄
  session_destroy();
} else {
  if (mb_substr($_SESSION['access_token'], 0, 5) == "mtst_") {
    header('Location: ./ownMyPage/index.php');
    return;
  }
}

if ($_SERVER["REQUEST_SCHEME"] != 'https') {
  header('Location: https://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?php echo $contents_name ?> | ログインページ" />
  <meta name="keywords" content="" />
  <title><?php echo $contents_name ?>｜ログインフォーム</title>
  <link href="../static/css/style.css" rel="stylesheet" />
</head>

<body class="login_body">
  <main>
    <form name="login_form" id="login_form" action="./ownMyPage/index.php" method="post">
      <div class="login_form_top">
        <h1><?php echo $contents_name; ?> ログイン</h1>
        <p>ユーザーID（メールアドレス）、パスワードをご入力の上、「ログイン」を押して下さい。</p>
      </div>
      <div class="login_form_btm">
        <input type="text" name="login_id" placeholder="ユーザーID（メールアドレス）">
        <input type="password" name="password" placeholder="パスワード（半角英数8～16字）">
        <?php
        if (isset($_GET['error'])) {
          if ($_GET['error'] == 0) echo "<p class='ms message-red'>" . "登録されているログインIDではないようです。" . "</p>";
          if ($_GET['error'] == 1) echo "<p class='ms message-red'>" . "ログインIDが入力されていません。" . "</p>";
          if ($_GET['error'] == 2) echo "<p class='ms message-red'>" . "パスワードが入力されていません。" . "</p>";
          if ($_GET['error'] == 3) echo "<p class='ms message-red'>" . "登録されているパスワードではないようです。" . "</p>";
          if ($_GET['error'] == 4) echo "<p class='ms message-red'>" . "アカウントが停止中のようです。管理者にお問い合わせ下さい。" . "</p>";
        }
        if ($_GET['logout'] == 1) echo "<p class='ms message-blue'>" . "ログアウトしました。" . "</p>";
        ?>
        <input type="submit" name="login_button" value="ログイン">
      </div>
    </form>
  </main>
</body>

</html>