<?php
$u_id = $_SESSION['user_id'];
// DBからユーザー情報を取得　これは自分のユーザー情報ですね サイドバーの表示のときに利用するものですねこれは
$useruserData = getUser($u_id);

// DBから店舗データを取得 ユーザーidから自身が登録した店舗を取得してくる サイドバーの表示のときにも活用できますね
$shopshopData = getMyShop($u_id);

debug('ヘッダー内の処理です！'.print_r($useruserData, true));

?>
  
  <header class="page-header">
    <h1 class="header-title"><a href="index.php">関東のテイクアウト情報</a></h1>
    <p class="btn-gnavi">
      <span></span>
      <span></span>
      <span></span>
    </p>
    <span class="header-smartphone header-menu-text">MENU</span>
    <nav id="global-nav">
      <div class="header-smartphone">
        <div class="nav-user">
          <img class="nav-icon" src="<?php echo sanitize(showImg($useruserData['pic'])); ?>" alt="プロフィール画像">
          <p><?php echo sanitize($useruserData['username']); ?></p>
        </div>
      </div>

      <ul class="main-nav">
        <?php
        if(empty($_SESSION['user_id'])) {
        ?>
          <li><a href="index.php" class="">ホーム</a></li>
          <li><a href="login.php" class="">ログイン</a></li>
          <li><a href="signup.php" class="">ユーザー登録</a></li>
        <?php } else { ?>
          <li><a href="index.php" class="">ホーム</a></li>
          <li><a href="mypage.php" class="">マイページ</a></li>
          <?php if((int)$useruserData['shop_owner_flg'] === 1) { ?>
            <li class="header-smartphone">
              <a href="registShop.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&s_id='.$shopshopData['id'] : '?s_id='.$shopshopData['id']; ?>">店舗を登録・編集</a>
            </li>
          <?php } ?>
          <li class="header-smartphone"><a href="profEdit.php">プロフィール編集</a></li>
          <li class="header-smartphone"><a href="passEdit.php">パスワード変更</a></li>
          <li><a href="logout.php" class="">ログアウト</a></li>
          <li class="header-smartphone"><a href="withdraw.php">退会</a></li>
        <?php } ?>

      </ul>
    </nav>
  </header>