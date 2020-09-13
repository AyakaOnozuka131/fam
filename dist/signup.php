<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//post送信されていた場合
if(!empty($_POST)){
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');

  if(empty($err_msg)){

    validEmail($email, 'email');
    validMaxLen($email, 'email');
    validEmailDup($email);

    validHalf($pass, 'pass');
    validMaxLen($pass, 'pass');
    validMinLen($pass, 'pass');

    validMaxLen($pass_re, 'pass_re');
    validMinLen($pass_re, 'pass_re');

    if(empty($err_msg)){
      validMatch($pass, $pass_re, 'pass_re');

    if(empty($err_msg)){

      try {
        $dbh = dbConnect();
        $sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)';
        $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                      ':login_time' => date('Y-m-d H:i:s'),
                      ':create_date' => date('Y-m-d H:i:s'));
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        
        // クエリ成功の場合
        if($stmt){
          $sesLimit = 60*60;
          $_SESSION['login_date'] = time();
          $_SESSION['login_limit'] = $sesLimit;
          $_SESSION['user_id'] = $dbh->lastInsertId();

          debug('セッション変数の中身：'.print_r($_SESSION,true));
          header("Location:mypage.php");
        }

      } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG07;
      }

    }
    }
  }
}
?>
<?php require('head.php');?>
<body>

 <?php require('header.php');?>
 <main class="user_register">
   <div class="l-content-lg">
     <form action="" method="post">
       <h2 class="form_heading">会員登録</h2>
       <div class="form_container">
        <div class="form_item">
          <div class="area-msg">
            <?php 
            if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <label for="email" class="">Email</label>
          <input type="email" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">
          <span class="separator"> </span>
          <div class="area-msg">
            <?php errMsg('email');?>
          </div>
        </div>
        <div class="form_item">
        <div class="area-msg">
            <?php errMsg('pass');?>
          </div>
          <label for="pass" class="">Password</label>
          <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>">
          <span class="separator"> </span>
          <div class="area-msg">
            <?php errMsg('pass');?>
          </div>
        </div>
        <div class="form_item">
          <div class="area-msg">
            <?php errMsg('pass_re');?>
          </div>
          <label for="pass_re" class="">Password(再入力)</label>
          <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re'];?>">
          <span class="separator"> </span>
          <div class="area-msg">
            <?php errMsg('pass_re');?>
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
</body>
