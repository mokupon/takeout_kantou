<?php

//退会機能について
//それぞれのテーブルのユーザーIDを削除する
//クエリに成功したらトップページへ遷移させて、失敗したらエラーメッセージを吐くようにする。

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// post送信されていた場合
if(!empty($_POST)) {
  debug('post送信があります。');
  //例外処理
  try {
    //dbへ接続
    $dbh = dbConnect();
    //sql文作成
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id  = :us_id';
    $sql2 = 'UPDATE shop SET delete_flg = 1 WHERE user_id = :us_id';
    $sql3 = 'UPDATE `like` SET delete_flg = 1 WHERE user_id = :us_id';
    //データ流し込み
    $data = array(':us_id' => $_SESSION['user_id']);
    //クエリ実行
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);
    $stmt3 = queryPost($dbh, $sql3, $data);

    debug('$stmt1：'.print_r($stmt1, true));
    debug('$stmt2：'.print_r($stmt1, true));
    debug('$stmt3：'.print_r($stmt1, true));

    // クエリ実行成功の場合（最悪userテーブルのみ削除成功していれば良しとする）
    if($stmt1 && $stmt2 && $stmt3) {
      //セッション変数を全て削除する
      $_SESSION = array();
      //セッションを切断するにはセッションクッキーも削除する。
      //Note: セッション情報だけでなくセッションを破壊する。
      if(isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
      }

      //最終的に、セッションを破壊する
      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION, true));
      debug('トップページへ遷移します。');
      header("Location:index.php");
      exit;
    } else {
      debug('クエリが失敗しました。');
      $err_msg['common'] = MSG07;
    }
  } catch(Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}

?>



<?php
$siteTitle = '退会 - 関東のテイクアウト情報';
require('head.php');
?>
  <body class="page-1colum">
    
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <div class="site-widht" style="margin-top: 106.67px;">
      <section id="main" style="margin-top: 20%;">
        <div class="form-container">
          <form action="" method="post" class="form">
            <h2 class="title">退会</h2>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="退会する" name="submit">
            </div>
          </form>
        </div>
        <a href="mypage.php">&lt; マイページへ戻る</a>
      </section>
    </div>
  </body>
</html>