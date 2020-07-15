<?php
$siteTitle = 'パスワード再発行メール送信';
require('head.php');
?>
  <body class="page-1colum">

    <?php require('header.php'); ?>

    <p id="js-show-msg" style="display:none;" class="msg-slide">	
    <?php //echo getSessionFlash('msg_success'); ?>	
    </p>
    <!-- メインコンテンツ -->
    <div class="contents site-width" style="margin-top: 106.37px">
      <section id="main">
        <div class="form-container">
          <form action="" method="post" class="form">
            <p>ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送り致します。</p>
            <div class="area-msg">
            </div>
            <label for="">
              メールアドレス
              <input type="text" name="email" value="">
            </label>
            <div class="area-msg">
              <?php ?>
            </div>
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="送信する">
            </div>
          </form>
        </div>
        <a href="login.php">&lt; ログインページへ戻る</a>
      </section>
    </div>
    <!-- <script src="js/vendor/jquery-2.2.2.min.js"></script>
    <script src="script.js"></script> -->
    <?php require('footer.php'); ?>
  </body>