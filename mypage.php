<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
//ログイン認証
require('auth.php');

// 画面表示用データ取得
$u_id = $_SESSION['user_id'];
// DBから店舗データを取得 ユーザーidから自身が登録した店舗を取得してくる サイドバーの表示のときにも活用できますね
$shopData = getMyShop($u_id);
// DBから連絡掲示板データを取得 DBから自分が送信したメッセージを取得してくる(ユーザーバージョン)
$myMessageData = getUserMsgs($u_id);
// DBから自分が登録した店舗宛に送られてきたメッセージを取得してくる(店舗バージョン)
$shopMessageData = getShopMsgs($u_id);

// DBからユーザー情報を取得　これは自分のユーザー情報ですね サイドバーの表示のときに利用するものですねこれは
$userData = getUser($u_id);


// DBからお気に入りデータを取得
$likeData = getMyLike($u_id);

// dbから店舗データを取得 サイドバーで店舗登録・編集のリンクを表示するかどうか確認するためだけに使用するものです
// $shopData = getMyShop($_SESSION['user_id']);


// DBからきちんとデータがすべて取れているかのチェックは行わず、取れなければ何も表示しないこととする

debug('取得した店舗データ：'.print_r($shopData, true));
debug('取得した自分が投稿したレビューデータ：'.print_r($myMessageData, true));
debug('取得した自分が登録した店舗宛に届いたレビューデータ：'.print_r($shopMessageData, true));

debug('取得した自分のユーザーデータ：'.print_r($userData, true));
debug('取得したお気に入りデータ：'.print_r($likeData, true));

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

  <title>マイページ - 関東のテイクアウト情報</title>
  
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

  <body class="page-1colum">

    <!-- メニュー -->
    <?php require('header.php') ?>

    <!-- メインコンテンツ -->
    <h1 class="page-title" style="width: 100%; text-align: center; margin-top: 136.67px;">マイページ</h1>
    <div class="contents site-width">

      <!-- サイドバー -->
      <section class="sidebar sidebar_prof">
        <div class="icon">
          <img src="<?php echo sanitize(showImg($userData['pic'])); ?>" alt="プロフィール画像">
          <p><?php echo sanitize($userData['username']); ?></p>
        </div>
        <a href="profEdit.php">プロフィール編集</a>
        <?php if($userData['shop_owner_flg'] == 1) { ?>
            <a href="registShop.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&s_id='.$shopData['id'] : '?s_id='.$shopData['id']; ?>" class="panel">
            店舗を登録・編集
            </a>
        <?php } ?>
        <a href="passEdit.php">パスワード変更</a>
        <a href="withdraw.php">退会</a>
      </section>

      <!-- コンテンツ -->
      <section id="main">          
            <?php if(!empty($shopMessageData['msg'])) { ?>
            <section class="list list-table">
              <h2 class="header-title mypage-title" style="margin: 0">あなたのお店に寄せられた投稿</h2>
              <table class="table">
                <thead>
                  <tr>
                    <th>最新送信日時</th>
                    <th>取引相手</th>
                    <th>メッセージ</th>
                  </tr>
                </thead>
              <?php foreach($shopMessageData['msg'] as $key => $val) { 
                      if($val['from_user'] !== $u_id) {
                        if(!empty($val['msg'])) {  
              ?>
                          <tbody>
                            <tr>
                              <td><?php echo sanitize(date('Y.m.d H:i', strtotime($val['send_date']))); ?></td> <!-- 送信日時ね -->
                              <td><?php $yourUserData = getUser($val['from_user']); echo sanitize($yourUserData['username']); ?></td> <!-- 送信したユーザー名ね -->
                              <td>
                                <a href="shopDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&s_id='.$val['shop_id'] : '?s_id='.$val['shop_id']; ?>"><?php echo mb_substr(sanitize($val['msg']),0,40); ?><?php if(strlen($val['msg'] > 40)) echo '...'; ?></a>
                              </td>
                            </tr>
                          </tbody>
              <?php  
                        } else { ?>
                          <tbody>
                          <tr>
                              <!-- <td><?php// echo sanitize($val['send_date']); ?></td> -->
                              <td><?php echo sanitize(date('Y.m.d H:i', strtotime($msg['send_date']))); ?></td>
                              <td><?php $yourUserData = getUser($val['from_user']); echo sanitize($yourUserData['username']); ?></td>
                              <td><a href="">まだメッセージはありません</a></td>
                          </tr>
                          </tbody>
              <?php
                        }
                      }
                    }?>
              </table>
            </section>
            <?php    } ?>

        <section class="list list-table">
          <h2 class="header-title mypage-title" style="margin: 0">投稿履歴</h2>
          <table class="table">


            <?php if(!empty($myMessageData['msg'])) { ?>
              <thead>
              <tr>
                <th>最新送信日時</th>
                <th>取引相手</th>
                <th>メッセージ</th>
              </tr>
            </thead>
              <?php foreach($myMessageData['msg'] as $key => $val) { ?>
                <?php if($val['from_user'] === $u_id) {
                        if(!empty($val['msg'])) { ?>
                          <tbody>
                            <tr>
                              <td><?php echo sanitize(date('Y.m.d H:i', strtotime($val['send_date'])));?></td>
                              <td><?php $yourUserData = getUser($val['from_user']); echo sanitize($yourUserData['username']); ?></td>
                              <td>
                                <a href="shopDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&s_id='.$val['shop_id'] : '?s_id='.$val['shop_id']; ?>"><?php echo mb_substr(sanitize($val['msg']),0,40); ?><?php if(strlen($val['msg'] > 40)) echo '...'; ?></a>
                              </td>
                            </tr>
                          </tbody>
            <?php
                        } else { ?>

            <?php             
                        }
            ?>
            <?php
                      }
                    }
                  } else { ?>
                    <p style="text-align: center; line-height: 3;">まだ投稿していません</p>
            <?php
                  }
            ?>

          </table>
        </section>

        <h2 class="header-title mypage-title">
            お気に入り一覧
        </h2>
        <section class="list panel-list">
          <?php
            if(!empty($likeData)) {
              foreach($likeData as $key => $val) {
          ?>
                <a href="shopDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&s_id='.$val['id'] : '?s_id='.$val['id']; ?>" class="panel">
                  <div class="panel-head">
                    <img src="<?php echo sanitize(showImg($val['shop_pic'])); ?>" alt="店舗画像">
                  </div>
                  <div class="panel-body">
                    <p class="panel-title"><span> <?php echo sanitize($val['name']); //店舗名ね ?></span></p>
                  </div>
                </a>
          <?php
              }
            } else { ?>
              <p style="text-align: center; line-height: 3;">まだ、お気に入り登録していません</p>

          <?php
            }
          ?>
        </section>

      </section>
    </div>
    <!-- ページトップへ戻る -->
    <div id="page_top"><a href="#"></a></div>
  </body>