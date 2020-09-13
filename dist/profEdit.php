<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール登録　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData,true));

if(!empty($_POST)){
  // 1.POSTされていた場合
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));

  // 2.バリデーションに必要な変数
  $userName = $_POST['username'];
  $tel = $_POST['tel'];
  $zip = (!empty($_POST['zip'])) ? $_POST['zip'] : 0;
  $addr = $_POST['addr'];
  $age = $_POST['age'];
  $email = $_POST['email'];

  // 3.DBの情報と入力情報が異なる場合にバリデーションを行う
  if($dbFormData['username'] !== $userName){
    validMaxLen($userName, 'username');
  }
  if($dbFormData['tel'] !== $tel){
    validTel($tel, 'tel');
  }
  if($dbFormData['addr'] !== $addr){
    //住所の最大文字数チェック
    validMaxLen($addr, 'addr');
  }
  if( (int)$dbFormData['zip'] !== $zip){
    validZip($zip, 'zip');
  }
  if($dbFormData['age'] !== $age){
    validMaxLen($age, 'age');
    validNumber($age, 'age');
  }
  if($dbFormData['email'] !== $email){
    validMaxLen($email, 'email');
    if(empty($err_msg['email'])){
      validEmailDup($email);
    }
    validEmail($email, 'email');
    validRequired($email, 'email');
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');
    // 4.例外処理・DB接続
    try{
      $dbh = dbConnect();
      $sql = 'UPDATE users SET username = :u_name, tel = :tel, zip = :zip, addr = :addr, age = :age, email = :email WHERE id = :u_id';
      $data = array(
        ':u_name' => $userName,
        ':tel' => $tel,
        ':zip' => $zip,
        ':addr' => $addr,
        ':age' => $age,
        ':email' => $email,
        // フォーム入力保持を入れる
        ':u_id' => $dbFormData['id']
      );
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        debug('クエリが成功しました。');
        debug('マイページへ遷移します。');
        header("Location:mypage.php");
      }
      
    }catch (Exception $e){
      error_log('エラー発生:'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php $siteTitle = 'プロフィール登録';?>
<?php require('head.php');?>

<body>

<?php require('header.php');?>
  <main class="profEdit_contents">
    <h2 class="main_heading">プロフィール登録</h2>
    <div class="l-content-sm profEdit_contents_inner">

        <div class="main_column">
        <form action="" method="post">
          <div class="area-msg">
            <?php 
            errMsg('common');
            ?>
          </div>
          <div class="input_item_block">
            <label>
              名前
              <input type="text" name="username" value="<?php echo getFormData('username');?>">
            </label>
            <div class="area-msg">
              <?php 
                errMsg('username');
              ?>
            </div>
          </div>
          <div class="input_item_block">
            <label>
              TEL
              <input type="tel" name="tel" value="<?php echo getFormData('tel'); ?>">
            </label>
            <div class="area-msg">
              <?php 
                errMsg('tel');
              ?>
            </div>
          </div>
          <div class="input_item_block">
            <label>
              郵便番号
              <input type="text" name="zip" value="<?php echo getFormData('zip'); ?>">
            </label>
            <div class="area-msg">
              <?php 
                errMsg('zip');
              ?>
            </div>
          </div>
          <div class="input_item_block">
            <label>
              住所
              <input type="text" name="addr" value="<?php echo getFormData('addr');?>">
            </label>
            <div class="area-msg">
              <?php 
                errMsg('addr');
              ?>
            </div>
          </div>
          <div class="input_item_block">
            <label>
              年齢
              <input type="number" name="age" value="<?php echo getFormData('age');?>">
            </label>
            <div class="area-msg">
              <?php 
                errMsg('age');
              ?>
            </div>
          </div>
          <div class="input_item_block">
            <label>
              Email
              <input type="email" name="email" value="<?php echo getFormData('email');?>">
            </label>
            <div class="area-msg">
              <?php 
                errMsg('email');
              ?>
            </div>
          </div>
          <div class="input_item_block">
            プロフィール画像
            <label>
              <div class="area_drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic">
                <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
                ドラッグ＆ドロップ
              </div>
            </label>
            <div class="area-msg">
              <?php 
                errMsg('email');
              ?>
            </div>
          </div>
          <div class="btn_container">
            <input type="submit" value="変更する">
          </div>
          </form>
        </div>

      <div class="side_column">
        <a href="registPet.php">里親に出す</a>
        <a href="profEdit.php">プロフィール編集</a>
        <a href="passEdit.php">パスワード変更</a>
        <a href="withdraw.php">退会</a>
      </div>
    </div>
  </main>
  <?php require('footer.php');?>