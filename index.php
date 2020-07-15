<?php

//トップページについて
//getの情報を取得して、店舗データとカテゴリーデータを取得してくる

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

debug('getの中身：'.print_r($_GET, true));

// 画面表示用データ習得
//================================
// GETパラメータを習得
//---------------------------------
// カレントページ s_idのようなきがするけど？？？
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;  //デフォルトは１ページめ
// 表示件数
$listSpan = 15;
// カテゴリー  カテゴリーidを取得してきている感じですね。もともとはなかったときには０ではなく空文字を挿入しています ０だとしても問題ないですね
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '0';
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum - 1) * $listSpan);
// DBから店舗データを取得
$dbShopData = getShopList($currentMinNum, $category, $listSpan);
// DBからカテゴリデータを取得 カテゴリー情報をガッと取っていく感じやね
$dbCategoryData = getCategory();

debug('店舗データ：'.print_r($dbShopData, true));

//パラメータに不正な値が入っているかチェック
if(empty($dbShopData['data'])) {
// if(empty($dbShopData['data'])) {
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
  exit;
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
  <meta name="viewport" content="width=device-width,inital-scale=1.0">
  <title>関東のテイクアウト情報 - トップページ</title>
	<meta name="description" content="埼玉県久喜市内にある飲食店のテイクアウト情報を投稿、閲覧できるサイトです。">

	<!-- 正規表現-->
	<link rel="canonical" href="https://kukicoffee.hukupon.com/coupon/">

	<!-- Twitterカードの設定 -->
	<meta name="twitter:card" content="Summary"> <!--①-->
	<meta name="twitter:site" content="@KukiCoffee"> <!--②-->
	<meta property="og:url" content="https://kukitakeout.hukupon.com/index.php"> <!--③-->
	<meta property="og:title" content="久喜市のテイクアウト情報"> <!--④-->
	<meta property="og:description" content="埼玉県久喜市内にある飲食店のテイクアウト情報を投稿、閲覧できるサイトです。"> <!--⑤-->
	<meta property="og:image" content="https://kukitakeout.hukupon.com/img/namayuta.png"> <!--⑥-->



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
    <!-- ヘッダー -->
    <!-- <div id="home" class="big-bg">
      <?php// require('header.php'); ?>
      <div class="home-content site-width">
        <h2 class="header-title">あなたの街のテイクアウト情報を掲載します<br><br><br></h2>
        <p>いらっしゃいませ。ゆっくり御覧ください。</p>
      </div>
    </div> -->
    <?php require('header.php'); ?>
    <!-- メインコンテンツ -->
    <div class="contents site-width" style="margin-top: 106.64px;">
      <!-- サイドバー -->
      <section class="sidebar">
        <form name="" method="get">
          <h1 class="title">地区</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="c_id">
              <option value="0" <?php if(getFormData('c_id', true) == 0){ echo 'selected'; } ?>>選択してください</option>
              <?php
                foreach($dbCategoryData as $key => $val) {
              ?>
                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('c_id', true) == $val['id']) { echo 'selected'; } ?> >
                  <?php echo $val['name']; ?>
                </option>
              <?php
                }
              ?>
              <!-- <option value="0">久喜地区</option>
              <option value="1">鷲宮地区</option>
              <option value="2">栗橋地区</option>
              <option value="3">菖蒲地区</option> -->
            </select>
          </div>
          <input type="submit" value="検索">
        </form>
      </section>
      <!-- </div> -->
      <!-- メインコンテンツ -->
      <section id="main">
        <div class="search-title">
          <div class="search-left">
            <span class="total-num"><?php echo sanitize($dbShopData['total']); ?></span>件の店舗が見つかりました
          </div>
          <div class="search-right">
            <span class="num"><?php echo (!empty($dbShopData['data'])) ? $currentMinNum+1 : 0; ?></span> - <span class="num"><?php echo $currentMinNum+count($dbShopData['data']); ?></span>件 / <span class="num"><?php echo sanitize($dbShopData['total']); ?></span>件中
          </div>
        </div>
        <div class="panel-list">
          <?php
            foreach($dbShopData['data'] as $key => $val):
          ?>
            <a href="shopDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&s_id='.$val['id'] : '?s_id='.$val['id']; ?>" class="panel">
              <div class="panel-head">
                <img src="<?php echo sanitize($val['shop_pic']); ?>" alt="<?php echo sanitize($val['name']); ?>">
              </div>
              <div class="panel-body">
                <p><?php echo sanitize($val['name']); ?></p>
              </div>
            </a>
          <?php
           endforeach;
          ?>
        </div>
        <?php pagination($currentPageNum, $dbShopData['total_page'], '&c_id='.$category); ?>
            <!-- <a href="shopDetail.php" class="panel">
              <div class="panel-head">
                <img src="images/dummy.png" alt="">
                <div class="panel-body">
                  <p>店舗名</p> 
                </div>
              </div>
            </a>
            -->
      </section>
    </div>
    <!-- ページトップへ戻る -->
    <div id="page_top"><a href="#"></a></div>
  </body>
</html>