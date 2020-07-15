<?php
$siteTitle = 'パスワード再発行認証';
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
            <p>ご指定のメールアドレスお送りした【パスワード再発行認証】メール内にある「認証キー」をご入力ください。</p>
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
              <input type="submit" class="btn btn-mid" value="再発行する">
            </div>
          </form>
        </div>
        <a href="passRemindSend.php">&lt; パスワード再発行メールを再度送信する</a>
      </section>
    </div>
    <!-- <script src="js/vendor/jquery-2.2.2.min.js"></script>
    <script src="script.js"></script> -->
    <?php require('footer.php'); ?>
  </body>