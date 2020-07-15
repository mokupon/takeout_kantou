<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　商品詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// 店舗IDのGETパラメータを取得
$s_id = (!empty($_GET['s_id'])) ? $_GET['s_id'] : '';
// DBから店舗データを取得 店舗idを用いてshopとcategoryテーブルから店舗情報を取得してくる
$viewData = getShopOne($s_id);
// DBからメッセージデータを取得 店舗IDを用いてメッセージテーブルからメッセージ情報を取得してくる
$viewReviewData = getMsgs($s_id);
// DBからユーザー情報を取得 店舗IDを用いてユーザーテーブルから店舗登録者のユーザー情報(ユーザーid)を取得してくる
$shopUserData = getShopUser($s_id);
// DBからユーザー情報を取得　これは自分のユーザー情報ですね
$userData = getUser($_SESSION['user_id']);

// パラメータに不正な値が入っているかチェック
if(empty($viewData)) {
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php");
  exit;
}
debug('取得したDBデータ：'.print_r($viewData, true));
debug('取得したメッセージデータ：'.print_r($viewReviewData, true));
debug('取得した店舗ユーザーデータ：'.print_r($shopUserData, true));
debug('取得したユーザーデータ：'.print_r($userData, true));

// post送信されていた場合　買う！ボタンを押したときの処理
// 今回のところではレビューを投稿するボタンを押したときの処理
// 押したときにレビュー内容のバリデーションチェックをおこなって、大丈夫であれば、INSERT文でレビュー内容を加える
// if(!empty($))
if(isset($_POST['review_submit'])) {
  debug('POST送信があります。');
  //ログイン認証
  require('auth.php');
  
  //バリデーションチェック
  $msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';
  //未入力チェック
  validRequired($msg, 'msg');
  //最大文字数チェック
  validMaxLen($msg, 'msg', 500);

  if(empty($err_msg)) {
    debug('バリデーションおｋです。');
    //例外処理
    try {
      //dbへ接続
      $dbh = dbConnect();

      //sql文作成
      $sql = 'INSERT INTO message (shop_id, send_date, to_user, from_user, msg, create_date) VALUES (:s_id, :send_date, :to_user, :from_user, :msg, :date)';
      $data = array(':s_id' => $s_id, ':send_date' => date('Y-m-d H:i:s'), ':to_user' => $shopUserData['id'] , ':from_user' => $_SESSION['user_id'], ':msg' => $msg, ':date' => date('Y-m-d H:i:s') );
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt) {
        // //クエリ結果のデータを１レコード返却
        // return $stmt->fetch(PDO::FETCH_ASSOC);
        debug('クエリに成功しました');
        // $_POST = array(); //postをクリア
        debug('もう一度、自ページへ遷移します。');
        // $temp = (!empty(appendGetParam())) ? appendGetParam().'&s_id='.$viewReviewData['id'] : '?s_id='.$viewData['id'];
        // header("Location: " . $_SERVER['PHP_SELF'] .$temp); //自分自身に遷移する
        // exit;
      }
    } catch(Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}


//ログインしているかどうかをチェックします。レビューのところで使用するかな。isloginで結果がtrueだったらユーザーの名前にして、falseだったら匿名の情報にする
// $login_flg = isLogin();

// if($login_flg === true) {
//   debug('ログインされています');
// }
?>

<?php
$siteTitle = '店舗詳細 - 関東のテイクアウト情報';
require('head.php');
?>

  <body class="page-1colum">
    
    <?php require('header.php'); ?>

    <!-- メインコンテンツ -->
    <div class="site-width" style="margin-top: 106.37px">

      <!-- Main -->
      <section id="main">
        <div class="shop_title header-title" style="overflow: hidden">
          <span class="badge"><?php echo sanitize($viewData['category']); ?></span> <!-- 地区名ね -->
          <?php if($_SESSION['user_id']) { ?>
            <i class="fa fa-heart icn-like js-click-like <?php if(isLike($_SESSION['user_id'], $viewData['id'])){ echo 'active'; } ?>" aria-hidden="true" data-shopid="<?php echo sanitize($viewData['id']); ?>" ></i>
          <?php } ?>
          <h2><?php echo $viewData['name']; ?>  <!-- ここは店舗名ね --></h2>
          <!-- 下記の処理はとりあえず記述していまーす -->
          <!-- <i class="fa fa-heart icn-like js-click-like active" area-hidden="true" dataproductid="10"></i> -->
        </div>

        <div class="shop-img-container">
          <h2 class="page-title shop-detail-title">テイクアウト情報</h2>
          <p style="text-align: center"> <!-- テイクアウト情報ね -->
            <?php echo nl2br(sanitize($viewData['takeout_info'])) ?>
          </p>
          <div class="panel-list panel-list-takeout_pic">
              <!-- 下記の処理では、showimgは無くしたほうがいいかもしれないですね。画像が見つからなければそのまま出力するだけで良いかと思われます。dummy画像をわざわざ出力する必要はない！！！ -->
              <?php if(!empty($viewData['takeout_pic1'])) { ?>
                <a href="<?php if(!empty($viewData['takeout_pic1'])) echo sanitize($viewData['takeout_pic1']); ?>" data-lightbox="takeout_pic" data-title="テイクアウト画像">
                  <img src="<?php if(!empty($viewData['takeout_pic1'])) echo sanitize($viewData['takeout_pic1']); ?>" alt="テイクアウト画像1">
                </a>
              <?php } ?>
              <?php if(!empty($viewData['takeout_pic2'])) { ?>
                <a href="<?php if(!empty($viewData['takeout_pic2'])) echo sanitize($viewData['takeout_pic2']); ?>" data-lightbox="takeout_pic" data-title="テイクアウト画像">
                  <img src="<?php if(!empty($viewData['takeout_pic2'])) echo sanitize($viewData['takeout_pic2']); ?>" alt="テイクアウト画像2">
                </a>
              <?php } ?>
              <?php if(!empty($viewData['takeout_pic3'])) { ?>
                <a href="<?php if(!empty($viewData['takeout_pic3'])) echo sanitize($viewData['takeout_pic3']); ?>" data-lightbox="takeout_pic" data-title="テイクアウト画像">
                  <img src="<?php if(!empty($viewData['takeout_pic3'])) echo sanitize($viewData['takeout_pic3']); ?>" alt="テイクアウト画像3">
                </a>
              <?php } ?>
          </div>
        </div>

        <div class="contents" style="margin-bottom: 80px">
          <div class="shop_img">
            <!-- <img src="images/dongricoffee.jpg" alt="メイン画像"> -->
            <a href="<?php echo sanitize(showImg($viewData['shop_pic'])); ?>" data-lightbox="shop_pic" data-title="店舗画像">
              <img src="<?php echo sanitize(showImg($viewData['shop_pic'])); ?>" alt="メイン画像">
            </a>

          </div>
          <div class="info">
            <h2 class="header-title shop-detail-title" style="text-align: center;">基本情報</h2>
            <p style="margin-top: 50px"><!-- 店舗情報ね -->
              <?php echo nl2br(sanitize($viewData['shop_info'])); ?>
            </p>
          </div>
        </div>

        <h2 class="page-title shop-detail-title">レビュー</h2>
        <form action="" method="post" style="overflow: hidden;">
          <textarea name="msg" cols="30" rows="3" class="send-msg"></textarea>
          <label class="" style="float: left">
              <!-- <input type="checkbox" name="check" value=""> -->
              <!-- 匿名でレビューをする場合はチェックを付けてください -->
              投稿するには<a href="login.php">ログイン</a>が必要です。
            </label>
          <input type="submit" value="投稿" name="review_submit" class="btn btn-mid" style="width: 30%; float: right; margin-top: 15px;">
        </form>
        
        <div class="comment-area">
          <?php
            if(!empty($viewReviewData['msg'])) {
              foreach($viewReviewData['msg'] as $key => $val) {
          ?>
                <div class="comment">
                  <div class="comment-head">
                    <div class="avatar">
                      <p><img src="<?php $yourUserData = getUser($val['from_user']); echo sanitize(showImg($yourUserData['pic'])); ?>" alt=""><?php $yourUserData = getUser($val['from_user']); echo sanitize($yourUserData['username']); ?></p>
                    </div>
                  </div>
                  <div class="comment-body" style="overflow: hidden;">
                    <p>
                    <?php echo sanitize($val['msg']); ?>
                    </p>
                    <p><span class="" style="float: right"><?php echo sanitize(date('Y.m.d H:i', strtotime($val['send_date']))); ?></span></p>
                  </div>
                </div>
          <?php
              }
            } elseif($viewData === 1) { ?>
              <p style="text-align:center;line-height:20;">この店舗は消されています。</p>	
     <?php  } else { ?>
              <p style="text-align:center;line-height:20;">レビュー投稿はまだありません</p>
     <?php  }?>




      </section>
    </div>
    	<!-- ページトップへ戻る -->
    <div id="page_top"><a href="#"></a></div>
  </body>