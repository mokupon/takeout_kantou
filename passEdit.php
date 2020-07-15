<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード変更ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// dbからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($userData, true));

// dbから店舗データを取得 サイドバーで店舗登録・編集のリンクを表示するかどうか確認するためだけに使用するものです
$shopData = getMyShop($_SESSION['user_id']);

//post送信されていた場合
if(!empty($_POST)) {
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST, true));

  //変数にユーザー情報を代入
  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  //未入力チェック
  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');

  if(empty($err_msg)) {
    debug('未入力チェックOK。');

    //古いパスワードのチェック
    validPass($pass_old, 'pass_old');
    //新しいパスワードのチェック
    validPass($pass_new, 'pass_new');

    //古いパスワードとdbパスワードを照合（dbに入っているデータと同じであれば、半角英数字チェックや最大文字チェックは行わなくても問題ない）
    if(!password_verify($pass_old, $userData['password'])) {
      $err_msg['pass_old'] = MSG10;
    }
    //新しいパスワードと古いパスワードが同じかチェック
    if($pass_old === $pass_new) {
      $err_msg['pass_new'] = MSG11;
    }
    //パスワードとパスワード再入力が合っているかチェック
    validMatch($pass_new, $pass_new_re, 'pass_new_re');

    if(empty($err_msg)) {
      debug('バリデーションOK。');

      //例外処理
      try {
        //dbへ接続
        $dbh = dbConnect();
        //sql文作成
        $sql = 'UPDATE users SET password = :pass WHERE id = :id';
        $data = array(':id' => $_SESSION['user_id'], ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        //クエリ成功の場合
        if($stmt) {
          debug('クエリ成功。');
          $_SESSION['msg_success'] = SUC01;

          header("Location:mypage.php");  //マイページへ
          exit;
        }
      } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
    
  }
}
?>


<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-158704555-4"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-158704555-4');
  </script>

  <meta charset="utf-8">
  <!-- 室の低いコンテンツをクロールをさせない -->
	<meta name="robots" content="noindex">
  <!-- <meta name="viewport" content="width=device-width, inital-scale=1.0"> -->
	<meta name="viewport" content="width=device-width,initial-scale=1">

  <title>パスワード変更 - 関東のテイクアウト情報</title>
  
  <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
  <link href="https://fonts.googleapis.com/css2?family=Sawarabi+Mincho&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/css/lightbox.min.css" rel="stylesheet">

  <!-- フォントアイコン -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.4/css/all.css">
  <link rel="stylesheet" type="text/css" href="style.css">


  <script
  src="https://code.jquery.com/jquery-3.5.0.min.js"
  integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ="
  crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/js/lightbox.min.js"></script>


   <script src="script.js"></script>
</head>

  <body class="passEdit page-2colum">
    <style></style>

    <!-- メニュー -->
    <?php require('header.php'); ?>


    <!-- メインコンテンツ -->
    <h1 class="page-title" style="width: 100%; text-align: center; margin-top: 136.67px;">パスワード変更</h1>
    <div class="contents site-width">

      <!-- サイドバー -->
      <section class="sidebar sidebar_prof">
        <div class="icon">
          <img src="<?php echo sanitize(showImg($userData['pic'])); ?>" alt="プロフィール画像">
          <p><?php echo sanitize($userData['username']); ?></p>
        </div>
        <a href="profEdit.php">プロフィール編集</a>
        <?php if($userData['shop_owner_flg'] == 1) { ?>
            <!-- <a href="registShop.php">店舗を登録・編集</a> -->
            <a href="registShop.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&s_id='.$shopData['id'] : '?s_id='.$shopData['id']; ?>" class="panel">
            店舗を登録・編集
            </a>
        <?php } ?>
        <a href="passEdit.php">パスワード変更</a>
        <a href="withdraw.php">退会</a>
      </section>

      <!-- コンテンツ -->
      <section id="main">
        <div class="form-container">
          <form action="" method="post" class="form">
            <div class="area-msg">
              <?php
              echo getErrMsg('common');
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">
              古いパスワード
              <input type="password" name="pass_old" value="<?php echo getFormData('pass_old'); ?>">
            </label>
            <div class="area-msg">
              <?php
              echo getErrMsg('pass_old');
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">
              新しいパスワード
              <input type="password" name="pass_new" value="<?php echo getFormData('pass_new'); ?>">
            </label>
            <div class="area-msg">
              <?php
              echo getErrMsg('pass_new');
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
              新しいパスワード（再入力）
              <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re'); ?>">
            </label>
            <div class="area-msg">
              <?php
              echo getErrMsg('pass_new_re');
              ?>
            </div>
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="変更する">
            </div>

          </form>
        </div>
      </section>

      
    </div>
    <!-- ページトップへ戻る -->
    <div id="page_top"><a href="#"></a></div>
  </body>