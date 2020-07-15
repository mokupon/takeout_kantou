$(function() {
    // フッターを最下部に固定
    // var $ftr = $('#footer');
    // if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
    //   $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
    // }

    //メッセージ表示
    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length) {
        $jsShowMsg.slideToggle('slow');
        setTimeout(function() { $jsShowMsg.slideToggle('slow'); }, 5000);
    }

    //画像ライブプレビュー
    var $dropArea = $('.area-drop');
    var $fileInput = $('.input-file');
    $dropArea.on('dragover', function(e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', '3px #ccc dashed');
    });
    $dropArea.on('dragleave', function(e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', 'none');
    });
    $fileInput.on('change', function(e) {
        $dropArea.css('border', 'none');
        var file = this.files[0],
            $img = $(this).siblings('.prev-img'),
            fileReader = new FileReader();

        //5.読み込みが完了した際のイベントハンドラ、imgのsrcにデータをセット
        fileReader.onload = function(e) {
            //読み込んだデータをimgに設定
            $img.attr('src', event.target.result).show();
        };

        //6.画像読み込み
        fileReader.readAsDataURL(file);
    });

    //テキストエリアカウント
    var $countUp = $('#js-count'),
        $countView = $('#js-count-view');
$countUp.on('keyup', function(e) {
        $countView.html($(this).val().length);
    });

    var $countUp = $('#js-count2'),
    $countView = $('#js-count-view2');
$countUp.on('keyup', function(e) {
    $countView.html($(this).val().length);
});

    //画像切替
    var $switchImgSubs = $('.js-switch-img-sub'),
        $switchImgMain = $('#js-switch-img-main');
    $switchImgSubs.on('click',function(e){
      $switchImgMain.attr('src',$(this).attr('src'));
    });


    // お気に入り登録・解除
    var $like, likeShopId;
    $like = $('.js-click-like') || null;  //nullというのはnull値という値で、「変数の中身はからですよ」と明示するためにつかう値
    likeShopId = $like.data('shopid') || null;
    // 数値の0はfalseと判定されてしまう。product_idが0の場合もありえるので、0もtrueとする場合にはundefinedとnullを判定する
    if(likeShopId !== undefined && likeShopId !== null) {
        $like.on('click', function() {
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: "ajaxLike.php",
                data: { shopId : likeShopId }
            }).done(function( data ) {
                console.log('Ajax Success');
                //クラス属性をtoggleでつけ外しする
                $this.toggleClass('active');
            }).fail(function( msg ) {
                console.log('Ajax Error');
            });
        });
    }

    //ハンバーガーメニューの実装
    $(".btn-gnavi").on("click", function(){
        // ハンバーガーメニューの位置を設定
        var rightVal = 0;
        if($(this).hasClass("open")) {
            // 位置を移動させメニューを開いた状態にする
            rightVal = -300;
            // メニューを開いたら次回クリック時は閉じた状態になるよう設定
            $(this).removeClass("open");
        } else {
            // メニューを開いたら次回クリック時は閉じた状態になるよう設定
            $(this).addClass("open");
        }
 
        $("#global-nav").stop().animate({
            right: rightVal
        }, 200);
    });


    /* ページ上部へ戻る */
    jQuery(window).on("scroll", function($) {
    if (jQuery(this).scrollTop() > 100) {
        jQuery('#page_top').show();
    } else {
        jQuery('#page_top').hide();
    }
    });

    jQuery('#page_top').click(function () {
    jQuery('body,html').animate({
        scrollTop: 0
    }, 500);
    return false;
    });
    /* /ページ上部へ戻る */
});