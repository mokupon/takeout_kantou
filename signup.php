<?php

//ユーザー登録ページについて
//ユーザー情報を変数に入れて、バリデーションチェックをおこなう。全ておｋだったらデータベースに登録をおこなう
//userテーブルに格納する パスワードはハッシュ化する
//クエリに成功したらセッション情報を登録して、マイページへ遷移する
//失敗したらエラーメッセージを出力する

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//post送信されていた場合
if(!empty($_POST)) {

  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];
  $shop_owner_flg = (!empty($_POST['shop_owner_flg'])) ? 1 : 0;

  debug('$email：'.$email);
  debug('$pass：'.$pass);
  debug('$pass_re：'.$pass_re);
  debug('$shop_owner_flg：'.$shop_owner_flg);

  //未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');

  if(empty($err_msg)) {
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    //email重複チェック
    validEmailDup($email);

    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');

    //パスワード（再入力）の最大文字数チェック
    validMaxLen($pass_re, 'pass_re');
    //パスワード（再入力）の最小文字数チェック
    validMinLen($pass_re, 'pass_re');

    if(empty($err_msg)) {
      //パスワードとパスワード再入力が合っているかチェック
      validMatch($pass, $pass_re, 'pass_re');

      if(empty($err_msg)) {
        //例外処理
        try {
          //DBへ接続
          $dbh = dbConnect();

          //sql文作成
          $sql = 'INSERT INTO users (email,password,shop_owner_flg,login_time,create_date) VALUES(:email,:pass,:shop_owner_flg,:login_time,:create_date)';
          $data = array(
            ':email' => $email,':pass' => password_hash($pass, PASSWORD_DEFAULT),
            ':shop_owner_flg' => $shop_owner_flg,
            ':login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s')
          );
          //クエリ実行
          $stmt = queryPost($dbh, $sql, $data);

          //クエリ成功の場合
          if($stmt) {
            //ログイン有効期限（デフォルトを１時間とする）
            $sesLimit = 60 * 60;
            //最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            //ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('セッション変数の中身：'.print_r($_SESSION, true));

            header('Location:mypage.php');  //マイページへ
            exit;
          } else {
            debug('クエリに失敗しました。');
          }
        } catch(Exception $e) {
          error_log('エラー発生：'.$e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
}
?>


<!DOCTYPE html>
<html lang="ja">

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

  <title>ユーザー登録 - 関東のテイクアウト情報</title>
  
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
  <body class="page-signup page-1colum">
    <?php require('header.php'); ?>

    <!-- メインコンテンツ -->
    <div class="contents site-width" style="margin-top: 106.37px;">
      <section id="main">
        <div class="form-container">
          <form action="" method="post" class="form">
            <h2 class="title">ユーザー登録</h2>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>

            <label class="" style="margin-bottom: 20px;">
              <input type="checkbox" name="shop_owner_flg">
              <!-- 店舗のオーナーはチェックを付けてください -->
              店舗登録をする方はチェックを付けてください
            </label>
            <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
              メールアドレス
              <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" autocomplete="off">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['email'])) echo $err_msg['email'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
              パスワード <span style="font-size: 12px">※英数字6文字以上</span>
              <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>" autocomplete="off">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['pass'])) echo $err_msg['pass'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
              パスワード（再入力）</span>
              <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>"  autocomplete="off">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
              ?>
            </div>
            



            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="登録する">
            </div>
          </form>
        </div>
      </section>
      
    </div>
  </body>