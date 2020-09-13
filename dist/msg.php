<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　掲示板ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
$partnerUserId = '';
$partnerUserInfo = '';
$myUserInfo = '';

// 画面表示用データ取得
//================================
// GETパラメータを取得
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
// DBから掲示板とメッセージデータを取得
$viewData = getMsgsAndBord($m_id);

debug('取得したDBデータ：'.print_r($viewData,true));
// パラメータに不正な値が入っているかチェック
if(empty($viewData)){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:mypage.php");
}
// viewDataから相手のユーザーIDを取り出す
$dealUserIds[] = $viewData[0]['sale_user'];
$dealUserIds[] = $viewData[0]['buy_user'];
if(($key = array_search($_SESSION['user_id'], $dealUserIds) !== false)){
  unset($dealUserIds[$key]);
}
$partnerUserId = array_shift($dealUserIds);
debug('取得した相手のユーザーID：'.$partnerUserId);
// DBから取引相手のユーザー情報を取得
if(isset($partnerUserId)){
  $partnerUserInfo = getUser($partnerUserId);
}
// 相手のユーザー情報が取れたかチェック
if(empty($partnerUserInfo)){
  error_log('エラー発生:相手のユーザー情報が取得できませんでした');
  header("Location:mypage.php");
}
// DBから自分のユーザー情報を取得
$myUserInfo = getUser($_SESSION['user_id']);
debug('取得したユーザデータ：'.print_r($partnerUserInfo,true));
// 自分のユーザー情報が取れたかチェック
if(empty($myUserInfo)){
  error_log('エラー発生:自分のユーザー情報が取得できませんでした');
  header("Location:mypage.php");
}

// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');

  //ログイン認証
  require('auth.php');

  // バリデーション
  $msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';

  validMaxLen($msg, 'msg', 500);
  validRequired($msg, 'msg');

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    try{

      $dbh = dbConnect();
      $sql = 'INSERT INTO message (bord_id, send_date, to_user, from_user, msg, create_date) VALUES (:b_id, :send_date, :to_user, :from_user, :msg, :date)';
      $data = array(':b_id' => $m_id, ':send_date' => date('Y-m-d H:i:s'), ':to_user' => $partnerUserId, ':from_user' => $_SESSION['user_id'], ':msg' => $msg, ':date' => date('Y-m-d H:i:s'));

      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $_POST = array();
        debug('連絡掲示板へ遷移します。');
        header("Location: " .$_SERVER['PHP_SELF'] . '?m_id='.$m_id); //自分自身に遷移する
      }

    } catch (Exception $e){
      error_log('エラー発生：' . $e-> getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php $siteTitle = '掲示板ページ';?>
<?php require('head.php');?>
<body>

<?php require('header.php');?>
  <p id="js-show-msg" style="display:none;" class="msg-slide">
    <?php echo getSessionFlash('msg_success');?>
  </p>
    <main class="msg_contents">
        <div class="l-content-lg">
          <h2 class="main_heading">掲示板</h2>
          <div class="section">

              
              <?php
              if(!empty($viewData)){
                foreach($viewData as $key => $val){
                  if(!empty($val['form_user']) && $val['form_user'] == $partnerUserId){
              ?>
                <div class="oneArea">
                  <div class="onebox onebox-l">
                    <div class="imgArea">
                      <img src="<?php echo sanitize(showImg($partnerUserInfo['pic']));?>" alt="">
                    </div>
                    <div class="fukiArea"><div class="fukidasi"><?php echo sanitize($val['msg']);?></div></div>
                  </div>
                </div>
                <?php
                  }else{
                ?>
              <div class="oneArea">
                <div class="onebox onebox-r">
                  <div class="imgArea"><img src="<?php echo sanitize(showImg($partnerUserInfo['pic']));?>" alt=""></div>
                  <div class="fukiArea"><div class="fukidasi"><?php echo sanitize($val['msg']);?></div></div>
                </div>
              </div>
              <?php
                    }
                  }
                }else{
              ?>
                <p style="text-align:center;">メッセージ投稿はまだありません</p>
              <?php
                }
              ?>
            </div>
            <form action="" method="post">
              <div class="area-send-msg">
              <textarea name="msg" id="" cols="30" rows="3"></textarea>
              <div class="btn_wrap">
                <input type="submit" value="送信" class="btn btn-send">
              </div>
            </form>
          </div>
        </div>
    </main>
    <?php require('footer.php');?>