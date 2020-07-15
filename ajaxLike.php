<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================

//postがあり、ユーザーidがあり、ログインしている場合
if(isset($_POST['shopId']) && isset($_SESSION['user_id']) && isLogin()) {
  debug('POST送信があります。');
  $s_id = $_POST['shopId'];
  debug('店舗ID：'.$s_id);
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //レコードがあるか検索
    //likeという単語はLIKE検索というSQLの命令文で使われているため、そのままでは使えないため、`（バッククォート）で囲む
    $sql = 'SELECT * FROM favorite WHERE shop_id = :s_id AND user_id = :u_id';
    $data = array(':s_id' => $s_id, ':u_id' => $_SESSION['user_id']);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug($resultCount);
    //レコードが１件でもある場合
    if(!empty($resultCount)) {
      //レコードを削除する
      $sql = 'DELETE FROM favorite WHERE shop_id = :s_id AND user_id = :u_id';
      $data = array(':s_id' => $s_id, ':u_id' => $_SESSION['user_id']);
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
    } else {
      //レコードを挿入する
      $sql = 'INSERT INTO favorite (shop_id, user_id, create_date) VALUES(:s_id, :u_id, :date)';
      $data = array(':s_id' => $s_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s')) ;
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
    }
  } catch(Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>