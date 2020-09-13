<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ペット詳細ページ　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// 商品IDのGETパラメータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから商品データを取得
$viewData = getProductOne($p_id);
// パラメータに不正な値が入っているかチェック
if(empty($viewData)){
	error_log('エラー発生:指定ページに不正な値が入りました');
	header("Location:index.php");
}
debug('取得したDBデータ：'.print_r($viewData,true));

// post送信されていた場合
if(!empty($_POST['submit'])){
	debug('POST送信があります。');

  //ログイン認証
  require('auth.php');
	try {

		$dbh = dbConnect();
		$sql = 'INSERT INTO bord (sale_user, buy_user, pets_id, create_date) VALUES (:s_uid, :b_uid, :p_id, :date)';
		$data = array(':s_uid' => $viewData['user_id'], ':b_uid' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));

		$stmt = queryPost($dbh, $sql, $data);

		if($stmt){
			$_SESSION['msg_success'] = SUC05;
			debug('連絡掲示板へ遷移します。');
			header("Location:msg.php?m_id=".$dbh->lastInsertID()); //連絡掲示板へ
		}

	} catch (Exception $e) {
		error_log('エラー発生:' . $e->getMessage());
		$err_msg['common'] = MSG07;
	}
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>
<?php $siteTitle = 'ペット詳細ページ';?>
<?php require('head.php');?>


<body>

<?php require('header.php');?>
	<main class="petDetail_contents">
		<div class="l-content-lg petDetail_contents_inner">
			<div class="photo_container">
					<div class="main_img">
						<img src="<?php echo showImg(sanitize($viewData['pic1']));?>" alt="" id="js-switch-img-main">
					</div>
					<div class="sub_img">
						<div class="img"><img src="<?php echo showImg(sanitize($viewData['pic1']));?>" alt="<?php echo sanitize($viewData['name'])?>" class="js-switch-sub-main"></div>
						<div class="img"><img src="<?php echo showImg(sanitize($viewData['pic2']));?>" alt="<?php echo sanitize($viewData['name'])?>" class="js-switch-sub-main"></div>
						<div class="img"><img src="<?php echo showImg(sanitize($viewData['pic3']));?>" alt="<?php echo sanitize($viewData['name'])?>" class="js-switch-sub-main"></div>
					</div>
			</div>
			<div class="title">
				<div class="tag <?php if($viewData['status'] == '里親決定'){echo 'finish';}elseif($viewData['status'] == '里親募集中'){echo 'recruiting';}else{echo 'decision';}?>">
					<?php echo sanitize($viewData['status']);?>
				</div>
				<i class="fas fa-heart js-click-like <?php if(isLike($_SESSION['user_id'],$viewData['id'])){ echo 'active'; } ?>"  aria-hidden="true" data-petid="<?php echo sanitize($viewData['id']); ?>"></i>
			</div>
			<h2 class="heading"><?php echo sanitize($viewData['name'])?></h2>
			<div class="comments">
				<h3 class="comments_heading">自己紹介</h3>
				<p><?php echo sanitize($viewData['comment'])?></p>
			</div>
			<div class="entry_area">
				<div class="back_link">
						<a href="index.php<?php echo appendGetParam(array('p_id'));?>">&lt;&lt;一覧画面へ戻る</a>
				</div>
				<form action="" method="post">
					<div class="enty_btn">
						<input type="submit" value="応募する" name="submit" class="btn_primary">
					</div>
				</form>
			</div>
		</div>
	</main>
	<?php require('footer.php');?>