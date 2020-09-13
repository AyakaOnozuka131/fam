<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード変更　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');


//================================
// 画面処理
//================================

// 1.DBからユーザーデータを取得する
$userData = getUser($_SESSION['user_id']);

// デバック
debug('取得したユーザー情報'.print_r($userData,true));

// 2.POST送信があるかどうか
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));

  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  // 3.バリデーション

  // 4.未入力チェック
  validRequired($pass_old,'pass_old');
  validRequired($pass_new,'pass_new');
  validRequired($pass_new_re,'pass_new_re');

  if(empty($err_msg)){
    debug('未入力チェックOK。');

    // 5.パスワードバリデーション
    validPass($pass_old,'pass_old');
    validPass($pass_new,'pass_new');

    // 6.DBのパスワードと古いパスワードが合っているかチェック
    if(!password_verify($pass_old, $userData['password'])){
      $err_msg['pass_old'] = MSG12;
    }

    // 7.新しいパスワードが古いパスワードと同じかチェック
    if($pass_old === $pass_new){
      $err_msg['pass_new'] = MSG13;
    }

    // 8.パスワードの再入力チェック
    validMatch($pass_new, $pass_new_re ,'pass_new_re');

    if(empty($err_msg)){
      debug('バリデーションOK');

      try{
        // 9.例外処理・db接続
        $dbh = dbConnect();
        $sql = 'UPDATE users SET password = :pass WHERE id = :id';
        $data = array(':id' => $_SESSION['user_id'], ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));

        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
          debug('クエリが成功しました');
          // 10.メッセージをセッションに詰める
          $_SESSION['msg_success'] = SUC01;

          // 10-1.メール送信
          $username = ($userData['username']) ? $userData['username'] : '名無し';
          $from = 't.yu.camel0302@gmail.com';
          $to = $userData['email'];
          $subject = 'パスワード変更通知|fam';
          $comment = <<<EOT
{$username}さん
パスワードが変更されました。
                      
////////////////////////////////////////
fam
URL  http://fam.com/
E-mail info@fam.com
EOT;
          sendMail($from,$to,$subject,$comment);
          // 11.マイページへ遷移
          debug('マイページへ遷移します。');
          header("Location:mypage.php");
        }

      } catch (Exception $e) {
        error_log('エラー発生' . $e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}


?>
<?php $siteTitle = 'パスワード変更';?>
<?php require('head.php');?>
<body>

<?php require('header.php');?>
 <main class="user_register">
   <div class="l-content-lg">
     <form action="" method="post">
       <h2 class="form_heading">会員登録</h2>
       <div class="form_container">
       <div class="area-msg">
          <?php errMsg('common');?>
        </div>
        <div class="form_item">
          <label for="pass" class="">古いパスワード</label>
          <input type="password" name="pass_old" value="<?php echo getFormData('pass_old');?>">
          <span class="separator"></span>
          <div class="area-msg">
            <?php errMsg('pass_old');?>
          </div>
        </div>
        <div class="form_item">
        <label for="pass" class="">新しいパスワード</label>
          <input type="password" name="pass_new" value="<?php echo getFormData('pass_new');?>">
          <span class="separator"></span>
          <div class="area-msg">
            <?php errMsg('pass_new');?>
          </div>
        </div>
        <div class="form_item">
          <label for="pass_re" class="">新しいパスワード(再入力)</label>
          <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re');?>">
          <span class="separator"></span>
          <div class="area-msg">
            <?php errMsg('pass_new_re');?>
          </div>
        </div>
        <div class="btn-container">
          <input type="submit" class="btn" value="登録する">
        </div>
      </div>
     </form>
   </div>
 </main>
 <?php require('footer.php');?>