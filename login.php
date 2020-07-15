<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// ログイン画面処理
//================================
// post送信されていた場合
if(!empty($_POST)) {
  debug('post送信があります。');

  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  //未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');

  if(empty($err_msg)) {
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');

    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');
    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');

    if(empty($err_msg)) {
      debug('バリデーションOKです。');

      //例外処理
      try {
        //dbへ接続
        $dbh = dbConnect();
        //sql文作成
        $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(
          ':email' => $email
        );
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        //クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        debug('クエリ結果の中身：'.print_r($result, true));

        //パスワード照合
        if(!empty($result) && password_verify($pass, array_shift($result))) {
          debug('パスワードがマッチしました。');

          //ログイン有効期限（デフォルトを１時間とする）
          $sesLimit = 60 * 60;
          //最終ログイン日時を現在日時に
          $_SESSION['login_date'] = time();
          //ログイン保持にチェックがある場合
          if($pass_save) {
            debug('ログイン保持にチェックがあります。');
            //ログイン有効期限を３０日にしてセット
            $_SESSION['login_limit'] = $sesLimit * 24 * 30;
          } else {
            debug('ログイン保持にチェックがありません。');
            //次回からログイン保持をしないので、ログイン有効期限を１時間後にセット
            $_SESSION['login_limit'] = $sesLimit;
          }
          //ユーザーidを格納
          $_SESSION['user_id'] = $result['id'];

          debug('セッション変数の中身：'.print_r($_SESSION, true));
          debug('マイページへ遷移します。');
          header("Location:mypage.php");
          exit;
        } else {
          debug('パスワードがアンマッチです。');
          $err_msg['common'] = MSG09;
        }
      } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

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

  <title>ログイン - 関東のテイクアウト情報</title>
  
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
  <body class="page-login page-1colum">
    <?php require('header.php'); ?>
    
    <p id="js-show-msg" style="display:none;" class="msg-slide">	
    <?php //echo getSessionFlash('msg_success'); ?>	
    </p>
    <!-- メインコンテンツ -->
    <div class="contents site-width" style="margin-top: 106.37px;">
      <section id="main">
        <div class="form-container">
          <form action="" method="post" class="form" autocomplete="off">
            <h2 class="title">ログイン</h2>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common']; 
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
              メールアドレス
              <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['email'])) echo $err_msg['email'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
              パスワード
              <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['pass'])) echo $err_msg['pass'];
              ?>
            </div>

            <label>
              <input type="checkbox" name="pass_save">次回ログインを省略する
            </label>
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="ログイン">
            </div>
            <!-- パスワードを忘れた方は<a href="passRemindSend.php">コチラ</a> -->
            ユーザー登録をしていない方は<a href="signup.php">コチラ</a>

          </form>
        </div>
      </section>
    </div>
    <!-- <script src="js/vendor/jquery-2.2.2.min.js"></script>
    <script src="script.js"></script> -->
  </body>