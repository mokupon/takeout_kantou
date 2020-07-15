<?php

//プロフィール編集機能について
//まずフォームから入力された情報を取得して、バリデーションチェックをおこなう
//問題が無ければ、dbに接続する

//dbFormDataという変数は、ユーザー情報を格納している変数のことである

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得 ユーザーIDからその人の情報（データ）を全て取得
$dbFormData = getUser($_SESSION['user_id']);
// dbから店舗データを取得 サイドバーで店舗登録・編集のリンクを表示するかどうか確認するためだけに使用するものです
$shopData = getMyShop($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($dbFormData, true));

//post送信されていた場合　今回のところでは編集ボタンを押したときのみに下記の処理をおこなうようにする
if(isset($_POST['prof_registration'])) {
  debug('POST情報があります。');
  debug('POST情報：'.print_r($_POST, true));
  debug('FILE情報：'.print_r($_FILES, true));

  //変数にユーザー情報を代入
  $username = $_POST['username'];
  $email = $_POST['email'];
  //画像をアップロードし、パスを格納
  $pic = ( !empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
  //画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
  $pic = ( empty($pic) && !empty($dbFormData['pic']) ) ? $dbFormData['pic'] : $pic;

  //DBの情報と入力情報が異なる場合にバリデーションをおこなう
  if($dbFormData['username'] !== $username) {
    //名前の最大文字数チェック
    validMaxLen($username, 'username');
  }

  if($dbFormData['email'] !== $email) {
    //emailの未入力チェック
    validRequired($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    //emailの重複チェック
    if(empty($err_msg['email'])) {
      validEmailDup($email);
    }
    //emailの形式チェック
    validEmail($email, 'email');
  }

  if(empty($err_msg)) {
    debug('バリデーションOKです。');

    //例外処理
    try {
      //DBへ接続
      $dbh = dbConnect();
      //SQL文作成
      $sql = 'UPDATE users SET username= :u_name, email = :email, pic = :pic WHERE id = :u_id';
      $data = array(
        ':u_name' => $username, ':email' => $email,
        ':pic' => $pic, ':u_id' => $dbFormData['id']
      );
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      debug('stmt：：：'.print_r($stmt, true));
      //クエリ成功の場合
      if($stmt) {
        $_SESSION['msg_success'] = SUC02;
        debug('マイページへ遷移します。');
        header("Location:mypage.php");  //マイページへ
        exit;
      }
    } catch(Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
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

  <title>プロフィール編集 - 関東のテイクアウト情報</title>
  
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
  <body class="profEdit page-2colum">
    <!-- メニュー -->
    <?php require('header.php'); ?>

    <!-- メインコンテンツ -->
    <h1 class="page-title" style="width: 100%; text-align: center; margin-top: 136.67px;">プロフィール編集</h1>
    <div class="contents site-width">

      <!-- サイドバー -->
      <section class="sidebar sidebar_prof">
        <div class="icon">
          <img src="<?php echo sanitize(showImg($dbFormData['pic'])); ?>" alt="プロフィール画像">
          <p><?php echo sanitize($dbFormData['username']); ?></p>
        </div>
        <a href="profEdit.php">プロフィール編集</a>
        <?php if($dbFormData['shop_owner_flg'] == 1) { ?>
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
          <form action="" method="post" class="form" enctype="multipart/form-data">

            <div class="area-msg">
              <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>


              プロフィール画像
              <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>" style="overflow: hidden; padding-top: 25%;padding-bottom: 25%; width: 50%">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728" >
                <input type="file" name="pic" class="input-file" style="height: 100%">
                <img src="<?php echo getFormData('pic'); ?>" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display: none'; ?>">
                  ドラッグ＆ドロップ
              </label>

            <div class="area-msg">
              <?php
              if(!empty($err_msg['pic'])) echo $err_msg['pic'];
              ?>
            </div>




            <!-- <label class="">
              プロフィール画像
              <input type="text" name="prof_img" value="">
            </label>
            <div class="area-msg"></div> -->

            <label class="<?php if(!empty($err_msg['username'])) echo 'err' ?>">
              ユーザー名
              <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['username'])) echo $err_msg['username'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
              メールアドレス
              <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['email'])) echo $err_msg['email'];
              ?>
            </div>

            <div class="btn-container">
              <input type="submit" name="prof_registration" class="btn btn-mid" value="変更する">
            </div>
          </form>
        </div>
      </section>
    </div>
    <!-- ページトップへ戻る -->
    <div id="page_top"><a href="#"></a></div>
  </body>