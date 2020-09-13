<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　里親募集登録ページ　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// GETデータを格納
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから商品データを取得
$dbFormData = (!empty($p_id)) ? getProduct($_SESSION['user_id'], $p_id) : '';
// 新規登録画面か編集画面か判別用フラグ
$edit_flg = (empty($dbFormData)) ? false : true;
// DBからカテゴリデータを取得
$dbCategoryData = getCategory();
debug('商品ID：'.$p_id);
debug('フォーム用DBデータ：'.print_r($dbFormData,true));
debug('カテゴリデータ：'.print_r($dbCategoryData,true));

// DBからステータスデータを取得
$dbStatusData = getStatus();
debug('ステータスID'.$p_id);
debug('フォーム用DBデータ'.print_r($dbFormData,true));
debug('ステータスデータ'.print_r($dbStatusData,true));

// パラメータ改ざんチェック
//================================
if(!empty($p_id) && empty($dbFormData)){
  debug('GETパラメータの商品IDが違います。マイページへ遷移します。');
  header("Location:mypage.php");
}

// POST送信時処理
//================================
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  //変数にユーザー情報を代入
  $name = $_POST['name'];
  $category = $_POST['category_id'];
  $status = $_POST['status_id'];
  $comment = $_POST['comment'];
  // 画像をアップロードし、パスを格納
  $pic1 = ( !empty($_FILES['pic1']['name']) ) ? uploadImg($_FILES['pic1'],'pic1') : '';
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
  $pic1 = ( empty($pic1) && !empty($dbFormData['pic1'])) ? $dbFormData['pic1'] : $pic1;
  $pic2 = ( !empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'],'pic2') : '';
  $pic2 = ( empty($pic2) && !empty($dbFormData['pic2'])) ? $dbFormData['pic2'] : $pic2;
  $pic3 = ( !empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3'],'pic3') : '';
  $pic3 = ( empty($pic3) && !empty($dbFormData['pic3'])) ? $dbFormData['pic3'] : $pic3;

  // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
  if(empty($dbFormData)){
    debug('新規バリデーションです。');
    // DBに情報が保存されていなかった場合のバリデーション
    // 未入力チェック
    validRequired($name, 'name');
    // 最大文字数チェック
    validMaxLen($name, 'name');
    // セレクトボックスチェック
    validSelect($category, 'category_id');
    // セレクトボックスチェック
    // validSelect($status, 'status_id');
    // 最大文字数チェック
    validMaxLen($comment, 'comment', 500);
  }else{
    debug('既存登録バリデーションです。');
    // DBに情報が保存があった場合のバリデーション
    if($dbFormData['name'] !== $name){
      validRequired($name, 'name');
      validMaxLen($name,'name');
    }
    if($dbFormData['category_id'] !== $category){
      validSelect($category, 'category_id');
    }
    // if($dbFormData['status_id'] !== $status){
    //   validSelect($status, 'status_id');
    // }
    if($dbFormData['comment'] !== $comment){
      validMaxLen($comment, 'comment', 500);
    }
  }
  if(empty($err_msg)){
    debug('バリデーションOKです。');
    try {
      $dbh = dbConnect();
      // 編集画面の場合はUPDATE文、新規登録画面の場合はINSERT文を生成
      if($edit_flg){
        debug('DB更新です。');
        $sql = 'UPDATE pet SET name = :name, category_id = :category, status_id = :status ,comment = :comment, pic1 = :pic1, pic2 = :pic2, pic3 = :pic3 WHERE user_id = :u_id AND id = :p_id';
        $data = array(':name' => $name, ':category' => $category, ':status' => $status,':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
      }else{
        debug('DB更新です');
        $sql = 'INSERT INTO pet (name, category_id, status_id, comment, pic1, pic2, pic3, user_id, create_date ) VALUES (:name, :category, :status, :comment, :pic1, :pic2, :pic3, :u_id, :date)';
        $data = array(':name' => $name, ':category' => $category, ':status' => $status,':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2,':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
      }
      debug('SQL:'.$sql);
      debug('流し込みデータ:'.print_r($data,true));

      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        debug('クエリ成功です。');
        $_SESSION['msg_success'] = SUC04;
        debug('マイページへ遷移します。');
        header("Location:mypage.php");
      }
    } catch (Exception $e) {
      error_log('エラー発生：' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php $siteTitle = (!$edit_flg) ? '里親登録' : 'ペットのプロフィール編集';?>
<?php require('head.php');?>

<body>

<?php require('header.php');?>
  <main class="registPet_contents">
    <h2 class="main_heading"><?php echo (!$edit_flg) ? '里親登録する' : 'プロフィールを編集する'; ?></h2>
    <div class="l-content-sm profEdit_contents_inner">
      <div class="main_column">
      <form action="" method="post" class="form" enctype="multipart/form-data" style="">
        <div class="area-msg">
          <?php errMsg('common');?>
        </div>
        <div class="input_item_block">
          <label>
            お名前
            <input type="text" name="name" value="<?php echo getFormData('name');?>">
          </label>
          <div class="area-msg">
            <?php errMsg('name');?>
          </div>
        </div>
        <div class="input_item_block">
          <label>
          種類
          <div class="input_item_block_select">
            <select name="category_id" id="" class="">
              <option value="0" <?php if(getFormData('category_id') == 0) { echo 'selected'; }?>>選択してください</option>
              <?php
                foreach($dbCategoryData as $key => $val){
              ?>
              <option value="<?php echo $val['id']; ?>" <?php if(getFormData('category_id') == $val['id'] ){ echo 'selected'; }?>>
                <?php echo $val['name']; ?>
              </option>
              <?php
                }
              ?>
            </select>
          </div>
          </label>
          <div class="area-msg">
            <?php errMsg('category_id');?>
          </div>
        </div>
        <div class="input_item_block">
          <label>
          募集状況
          <div class="input_item_block_select">
            <select name="status_id" id="" class="">
              <option value="0" <?php if(getFormData('status_id') == 0) { echo 'selected'; }?>>選択してください</option>
              <?php
                foreach($dbStatusData as $key => $val){
              ?>
              <option value="<?php echo $val['id']?>" <?php if(getFormData('status_id') == $val['id']) { echo 'selected'; }?>>
                <?php echo $val['name']; ?>
              </option>
              <?php
                }
                
              ?>
            </select>
          </div>
          </label>
          <div class="area-msg">
            <?php errMsg('status_id');?>
          </div>
        </div>
        <div class="input_item_block">
          <label>
            自己紹介
            <textarea name="comment" id="js-count" cols="30" rows="10"><?php echo getFormData('comment'); ?></textarea>
          </label>
          <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
          <div class="area-msg">
            <?php errMsg('comment');?>
          </div>
        </div>
        <div class="input_item_block block_row">
          <div class="imgDrop_block">
            <div class="imgDrop-container">
              画像1
              <label class="area_drop">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic1" class="input-file">
              <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none;'?>">
              ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php errMsg('pic1');?>
              </div>
            </div>
          </div>
          <div class="imgDrop_block">
            <div class="imgDrop-container">
              画像2
              <label class="area_drop">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic2" class="input-file">
              <img src="<?php echo getFormData('pic2'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none;'?>">
              ドラッグ＆ドロップ
              </label>
              <div class="area-msg"><?php errMsg('pic2');?></div>
            </div>
          </div>
          <div class="imgDrop_block">
            <div class="imgDrop-container">
              画像3
              <label class="area_drop">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic3" class="input-file">
              <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none;'?>">
              ドラッグ＆ドロップ
              </label>
              <div class="area-msg"><?php errMsg('pic3');?></div>
            </div>
          </div>
        </div>
        <div class="btn_container">
          <input type="submit" value="<?php echo (!$edit_flg) ? '登録する' : '更新する'; ?>">
        </div>
        </form>
      </div>
      <div class="side_column">
        <a href="registPet.php">里親募集登録</a>
        <a href="profEdit.php">プロフィール編集</a>
        <a href="passEdit.php">パスワード変更</a>
        <a href="withdraw.php">退会</a>
      </div>
    </div>
  </main>
  <?php require('footer.php');?>