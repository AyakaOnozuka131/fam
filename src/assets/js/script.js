// hamburger
(function($) {
  var $header_inner   = $('.header_inner');
  var $btn   = $('.ham');
  var $mask  = $('.ham_mask');
  var open   = 'open'; // class
  // menu open close
  $btn.on( 'click', function() {
    if ( ! $header_inner.hasClass( open ) ) {
      $header_inner.addClass( open );
    } else {
      $header_inner.removeClass( open );
    }
  });
  // mask close
  $mask.on('click', function() {
    $header_inner.removeClass( open );
  });
} )(jQuery);

$(function(){
  // footerを下に固定
  var $ftr = $('.footer');
  if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
    $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
  }
  // メッセージ表示
  var $jsShowMsg = $('#js-show-msg');
  var msg = $jsShowMsg.text();
  if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
    $jsShowMsg.slideToggle('slow');
    setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 5000);
  }
  // 画像ライブプレビュー
  var $dropArea = $('.area_drop');
  var $fileInput = $('.input-file');
  $dropArea.on('dragover', function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '3px #ccc dashed');
  });
  $dropArea.on('dragleave', function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', 'none');
  });
  $fileInput.on('change', function(e){
    $dropArea.css('border', 'none');
    var file = this.files[0],
        $img = $(this).siblings('.prev-img'),
        fileReader = new FileReader();
    
    // 5. 読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
    fileReader.onload = function(event) {
      // 読み込んだデータをimgに設定
      $img.attr('src', event.target.result).show();
    };

    // 6. 画像読み込み
    fileReader.readAsDataURL(file);
  });

  // テキストエリアカウント
  var $countUp = $('#js-count'),
      $countView = $('#js-count-view');
  $countUp.on('keyup', function(e){
    $countView.html($(this).val().length);
  });

  //画像切替
  var $switchImgSubs = $('.js-switch-sub-main'),
      $switchImgMain = $('#js-switch-img-main');
  
  $switchImgSubs.on('click',function(e){
    $switchImgMain.attr('src',$(this).attr('src'));
  });

  // お気に入り登録・削除
  var $like,
      likePetId;
  
  $like = $('.js-click-like') || null;
  likePetId = $like.data('petid') || null;

  if(likePetId !== undefined && likePetId !== null){
    $like.on('click',function(){
      var $this = $(this);
      $.ajax({
        type: "POST",
        url:"isLike.php",
        data: { petId : likePetId }
      }).done(function( data ){
        console.log('Ajax Success');
        $this.toggleClass('active');
      }).fail(function( msg ){
        console.log('Ajax Error')
      });
    });
  }
  
});