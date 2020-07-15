<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　店舗登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
debug('getの中身：'.print_r($_GET, true));

//getデータを取得
$s_id = (!empty($_GET['s_id'])) ? $_GET['s_id'] : '';
//dbから店舗データを取得　ユーザーidと店舗idを持っていって、店舗情報を取得してくる　あったら編集画面で、なかったら新規登録画面にする
//dbFormDataには、店舗情報が格納される
$dbFormData = (!empty($s_id)) ? getShop($_SESSION['user_id'], $s_id) : '';
// 新規登録画面か編集画面か判別用フラグ
$edit_flg = (empty($dbFormData)) ? false: true;
// dbからカテゴリデータを取得
$dbCategoryData = getCategory();
// dbからユーザーデータを取得　サイドバーのところで店舗登録・編集を表示するかどうかで、shop_owner_flgの値を確認するためだけに用意した処理です()
$userData = getUser($_SESSION['user_id']);
debug('店舗ID：'.$s_id);
debug('フォーム用DBデータ：'.print_r($dbFormData, true));
debug('カテゴリデータ：'.print_r($dbCategoryData, true));

// パラメータ改ざんチェック
//================================
// GETパラメータはあるが、改ざんされている（URLをいじくった）場合、正しい商品データが取れないのでマイページへ遷移させる
if(!empty($s_id) && empty($dbFormData)) {
  debug('GETパラメータの店舗IDが違います。マイページへ遷移します。');
  header("Location:mypage.php");  //マイページへ
  exit;
}

// POST送信時処理 今回のプログラムでは、登録・更新ボタンを押したときに起こされる処理にしています
//================================
if(isset($_POST['shop_registration'])) {
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST, true));
  debug('FILE情報：'.print_r($_FILES, true));

  //変数にユーザー情報を代入
  $name = $_POST['shop-name']; //店舗名
  $category = $_POST['category_id'];  //地区
  //$takeout_info = nl2br($_POST['takeout_info']); //テイクアウト情報 n12brというものは改行も出力するものです
  $takeout_info = $_POST['takeout_info']; //テイクアウト情報 n12brというものは改行も出力するものです

  //画像をアップロードし、パスを格納
  $takeout_pic1 = (!empty($_FILES['takeout_pic1']['name'])) ? uploadImg($_FILES['takeout_pic1'], 'takeout_pic1') : '';
  //画像をpostしていない（登録していない）が既にdbに登録されている場合、dbのパスを入れる（POSTには反映されないので）
  $takeout_pic1 = (empty($takeout_pic1) && !empty($dbFormData['takeout_pic1'])) ? $dbFormData['takeout_pic1'] : $takeout_pic1;

  $takeout_pic2 = (!empty($_FILES['takeout_pic2']['name'])) ? uploadImg($_FILES['takeout_pic2'], 'takeout_pic2') : '';
  $takeout_pic2 = (empty($takeout_pic2) && !empty($dbFormData['takeout_pic2'])) ? $dbFormData['takeout_pic2'] : $takeout_pic2;

  $takeout_pic3 = (!empty($_FILES['takeout_pic3']['name'])) ? uploadImg($_FILES['takeout_pic3'], 'takeout_pic3') : '';
  $takeout_pic3 = (empty($takeout_pic3) && !empty($dbFormData['takeout_pic3'])) ? $dbFormData['takeout_pic3'] : $takeout_pic3;

  $shop_pic = (!empty($_FILES['shop_pic']['name'])) ? uploadImg($_FILES['shop_pic'], 'shop_pic') : ''; 
  $shop_pic = (empty($shop_pic) && !empty($dbFormData['shop_pic'])) ? $dbFormData['shop_pic'] : $shop_pic;

  //$shop_info = nl2br($_POST['shop_info']); //店舗の詳細
  $shop_info = $_POST['shop_info']; //店舗の詳細

  //更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
  if(empty($dbFormData)) {
    //未入力チェック
    validRequired($name, 'name');
    //最大文字数チェック
    validMaxLen($name, 'name');
    //セレクトボックスチェック
    validSelect($category, 'category_id');
    //最大文字数チェック
    validMaxLen($takeout_info, 'takeout_info', 500); //テイクアウト情報
    //最大文字数チェック
    validMaxLen($shop_info, 'shop_info', 500);  //店舗の詳細
    //もう少しバリデーションチェックをしてもいいかもしれないです。未入力チェックを追加するといいかもしれないということね
  } else {  //更新時の処理ね。ここは。
    if($dbFormData['name'] !== $name) {
      //未入力チェック
      validRequired($name, 'name');
      //最大文字数チェック
      validMaxLen($name, 'name');
      //店舗重複チェック
      // shopDup($name);
    }
    if($dbFormData['category_id'] !== $category) {
      //セレクトボックスチェック
      validSelect($category, 'category_id');
    }
    if($dbFormData['takeout_info'] !== $takeout_info) {
      //未入力チェック
      // validRequired($takeout_info, 'takeout_info');
      //最大文字数チェック
      validMaxLen($takeout_info, 'takeout_info', 500);
    }
    if($dbFormData['shop_info'] !== $shop_info) {
      //最大文字数チェック
      validMaxLen($shop_info, 'shop_info', 500);
    }
  }

  //画像を削除するチェックボックスが押されていたら、そこの画像のパスを空にする
  $takeout_pic1 = (!empty($_POST['delete_takeout_pic1'])) ? null : $takeout_pic1;
  $takeout_pic2 = (!empty($_POST['delete_takeout_pic2'])) ? null : $takeout_pic2;
  $takeout_pic3 = (!empty($_POST['delete_takeout_pic3'])) ? null : $takeout_pic3;


  if(empty($err_msg)) {
    debug('バリデーションOKです。');

    //例外処理
    try {
      //dbへ接続
      $dbh = dbConnect();
      //sql文作成
      if($edit_flg) {
        debug('db更新です。');
        $sql = 'UPDATE shop SET name = :name, category_id = :category, takeout_info = :takeout_info, 
        takeout_pic1 = :takeout_pic1, takeout_pic2 = :takeout_pic2, takeout_pic3 = :takeout_pic3,
        shop_pic = :shop_pic, shop_info = :shop_info WHERE user_id = :u_id AND id = :s_id';
        $data = array(
          ':name' => $name, ':category' => $category, ':takeout_info' => $takeout_info, 'takeout_pic1' => $takeout_pic1,
          ':takeout_pic2' => $takeout_pic2, ':takeout_pic3' => $takeout_pic3, ':shop_pic' => $shop_pic, ':shop_info' => $shop_info , ':u_id' => $_SESSION['user_id'], ':s_id' => $s_id);
      } else {
        debug('db新規登録です。');
        //下記のsql文での店舗IDをつけてあげる　いわゆるp_id(s_id)ていうやつですね
        $sql = 'INSERT INTO shop(name, category_id, takeout_info, takeout_pic1, takeout_pic2, takeout_pic3, shop_pic, shop_info, user_id, create_date) values(:name, :category, :takeout_info, :takeout_pic1, :takeout_pic2, :takeout_pic3, :shop_pic, :shop_info, :u_id, :date);';
        $data = array(
          ':name' => $name, ':category' => $category, ':takeout_info' => $takeout_info,
          ':takeout_pic1' => $takeout_pic1, ':takeout_pic2' => $takeout_pic2, ':takeout_pic3' => $takeout_pic3, ':shop_pic' => $shop_pic, ':shop_info' => $shop_info,
          ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s')
        );
      }
      debug('SQL：'.$sql);
      debug('流し込みデータ：'.print_r($data, true));
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      //クエリ成功の場合
      if($stmt) {
        $_SESSION['msg_success'] = SUC04;
        debug('マイページへ遷移します。');
        header("Location:mypage.php");  //マイページ
        exit;
      }
    } catch(Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
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

  <title>店舗登録 - 関東のテイクアウト情報</title>
  
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
  <body class="page-2colum">
    
    <!-- メニュー -->
    <?php require('header.php') ?>

    <!-- メインコンテンツ -->
    <h1 class="page-title" style="width: 100%; text-align: center; margin-top: 136.67px;">
    <?php echo (!$edit_flg) ? '店舗を登録する' : '店舗を編集する'; ?>
    </h1>
    <div class="contents site-width">

      <!-- サイドバー -->
      <section class="sidebar sp-sidebar">
        <div class="icon">
          <img src="<?php echo sanitize(showImg($userData['pic'])); ?>" alt="プロフィール画像">
          <p><?php echo sanitize($userData['username']); ?></p>
        </div>

        <a href="profEdit.php">プロフィール編集</a>
        <?php if($userData['shop_owner_flg'] == 1) { ?>
            <a href="registShop.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&s_id='.$dbFormData['id'] : '?s_id='.$dbFormData['id']; ?>" class="panel">
            店舗を登録・編集
            </a>
        <?php } ?>
        <a href="passEdit.php">パスワード変更</a>
        <a href="withdraw.php">退会</a>
      </section>

      <!-- コンテンツ -->
      <section id="main">
        <div class="form-container">
          <form action="" method="post" class="form shop_form" enctype="multipart/form-data">
            <div class="area-msg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['takeout_info'])) echo 'err'; ?>">
              テイクアウト情報
              <textarea cols="30" rows="3" id="js-count" name="takeout_info"><?php echo getFormData('takeout_info'); ?></textarea>
              <!-- <p class="counter-text"><span id="js-count-view">0</span>/500文字</p> -->
              <div class="area-msg">
                <?php
                if(!empty($err_msg['takeout_info'])) echo $err_msg['takeout_info'];
                ?>
              </div>
            </label>
            
            <label class="">
              テイクアウト情報(メニュー表の画像など)※画像が見切れて表示される可能性がございますが、大丈夫です。

              <div class="takeout-images">
                <div class="imgDrop-container">
                  画像1
                  <label class="area-drop area-drop-shop <?php if(!empty($err_msg['takeout_pic1'])) echo 'err'; ?>">
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="takeout_pic1" class="input-file">
                    <img src="<?php echo getFormData('takeout_pic1') ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('takeout_pic1'))) echo 'display: none;' ?>">
                      ドラッグ＆ドロップ
                  </label>
                  <label>
                    <input type="checkbox" name="delete_takeout_pic1">画像を削除する
                  </label>
                  <div class="area-msg">
                    <?php
                    if(!empty($err_msg['takeout_pic1'])) echo $err_msg['takeout_pic1'];
                    ?>
                  </div>
                </div>



                <div class="imgDrop-container">
                  画像2
                  <label class="area-drop area-drop-shop <?php if(!empty($err_msg['takeout_pic2'])) echo 'err'; ?>">
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="takeout_pic2" class="input-file">
                    <img src="<?php echo getFormData('takeout_pic2') ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('takeout_pic2'))) echo 'display: none;' ?>">
                      ドラッグ＆ドロップ
                  </label>
                  <label>
                    <input type="checkbox" name="delete_takeout_pic2">画像を削除する
                  </label>
                  <div class="area-msg">
                  <?php
                  if(!empty($err_msg['takeout_pic2'])) echo $err_msg['takeout_pic2'];
                  ?>
                  </div>
                </div>


                <div class="imgDrop-container">
                  画像3
                  <label class="area-drop area-drop-shop <?php if(!empty($err_msg['takeout_pic3'])) echo 'err'; ?>">
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="takeout_pic3" class="input-file">
                    <img src="<?php echo getFormData('takeout_pic3') ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('takeout_pic3'))) echo 'display: none;' ?>">
                      ドラッグ＆ドロップ
                  </label>
                  <label>
                    <input type="checkbox" name="delete_takeout_pic3">画像を削除する
                  </label>
                  <div class="area-msg">
                    <?php
                    if(!empty($err_msg['takeout_pic3'])) echo $err_msg['takeout_pic3'];
                    ?>
                  </div>
                </div>
              </div>
            </label>

            <label>
            <div class="imgDrop-container">
                  店舗画像
                  <label class="area-drop <?php if(!empty($err_msg['shop_pic'])) echo 'err'; ?>" style="overflow: hidden" >
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="shop_pic" class="input-file">
                    <img src="<?php echo getFormData('shop_pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('shop_pic'))) echo 'display: none;' ?>">
                      ドラッグ＆ドロップ
                  </label>
                  <div class="area-msg">
                    <?php
                    if(!empty($err_msg['shop_pic'])) echo $err_msg['shop_pic'];
                    ?>
                  </div>
                </div>
            </label>

            <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
              店舗名<span class="label-require">必須</span>
              <input type="text" name="shop-name" value="<?php echo getFormData('name'); ?>" autocomplete="off">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['name'])) echo $err_msg['name'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['category_id'])) echo 'err'; ?>">
              地区<span class="label-require">必須</span>
              <select name="category_id">
                <option value="0" <?php if(getFormData('category_id') === 0) { echo 'selected'; } ?>>選択してくだい</option>
                <?php
                  foreach($dbCategoryData as $key => $val) {
                ?>
                  <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category_id') == $val['id']) { echo 'selected'; } ?>>
                    <?php echo $val['name']; ?>
                  </option>
                <?php
                  }
                ?>
              </select>
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['category_id'])) echo $err_msg['category_id'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['shop_info'])) echo 'err'; ?>">
              店舗の詳細
              <textarea cols="30" rows="3" id="js-count2" name="shop_info"><?php echo getFormData('shop_info'); ?></textarea>
              <p class="counter-text"><span id="js-count-view2">0</span>/500文字</p>
              <div class="area-msg">
                <?php
                if(!empty($err_msg['shop_info'])) echo $err_msg['shop_info'];
                ?>
              </div>
            </label>

            <div class="btn-container" style="overflow: hidden">
              <?php if($edit_flg) { ?>
                <!-- <input type="submit" name="shop_delete" class="btn btn-mid" value="削除する" style="width: 45%;float: left"> -->
              <?php } ?>
              <input type="submit" name="shop_registration" class="btn btn-mid" value="<?php echo (!$edit_flg) ? '登録する' : '更新する'; ?>" style="width: 45%;float: right">
            </div>
            <!-- <div class="btn-container" style="overflow: hidden"> -->
            </div>
          </form>
        </div>
      </section>
    </div>
    <!-- ページトップへ戻る -->
    <div id="page_top"><a href="#"></a></div>
  </body>

