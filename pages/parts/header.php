<header>
  <div id="title_area">
    <p id="title" style="margin-top:1rem; margin-bottom:1rem;"><?php echo $_SESSION['login_user']['user_name'] . "'s " . $contents_name ?></p>
  </div>
  <div id="rightside_area">
    <button id="hamburger">&#9776;</button>
    <nav>
      <?php if ($_SESSION['login_user']['authority'] >= 1) : ?><a href="./user_list.php"><span class="for-admin">[管]</span> 登録ユーザー一覧</a><?php endif; ?>
      <?php if ($_SESSION['login_user']['authority'] >= 1) : ?><a href="./user_create.php"><span class="for-admin">[管]</span> ユーザー新規登録</a><?php endif; ?>
      <a href="./user_edit.php?id=<?php echo $_SESSION['login_user']['id'] ?>&login_id=<?php echo $_SESSION['login_user']['login_id'] ?>">ユーザーアカウント編集</a>
      <a href="./index.php">登録ノート一覧照会</a>
      <a href="./note_create.php">ノート新規登録</a>
      <a id="logout" href="../login.php?logout=1">ログアウト</a>
    </nav>
  </div>
</header>