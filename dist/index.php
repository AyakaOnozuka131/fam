<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　一覧ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// カレントページのGETパラメータを取得
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
// カテゴリー
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
// ソート順
$status = (!empty($_GET['s_id'])) ? $_GET['s_id'] : '';
// パラメータに不正な値が入っているかチェック
if(!is_int((int)$currentPageNum)){
	error_log('エラー発生:指定ページに不正な値が入りました');
	header("Location:index.php");
}
// 表示件数
$listSpan = 9;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan);
// DBから商品データを取得
$dbProductData = getProductList($currentMinNum, $category, $status);

// DBからカテゴリデータを取得
$dbCategoryData = getCategory();
// DBからステータスデータを取得
$dbStatusData = getStatus();


debug('現在のページ：'.$currentPageNum);

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php $siteTitle = '一覧ページ';?>
<?php require('head.php');?>
<body>

<?php require('header.php');?>
	<main class="main_contents">
		<div class="l-content-lg main_contents_inner">
			<div class="main_column">
				<div class="main_list">

					<?php
					foreach($dbProductData['data'] as $key => $val):
					?>
					
					<article class="main_list_item">
						<a href="./petDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id'.$val['id'] : '?p_id='.$val['id']; ?>">
							<div class="tag <?php if($val['status_id'] == '1'){echo 'recruiting';}elseif($val['status_id'] == '2'){echo 'decision';}else{echo 'finish';}?>">
								<?php 
									if($val['status_id'] == '1'){
										echo '里親募集中';
									}elseif($val['status_id'] == '2'){
										echo '一時停止中';
									}else{echo '里親決定';}
								?>
							</div>
							<div class="img">
								<img src="<?php echo sanitize($val['pic1']); ?>" alt="<?php echo sanitize($val['name']); ?>">
							</div>
							<div class="item_text">
								<h3 class="list_heading"><?php echo sanitize($val['name']);?></h3>
								<p class="list_text">
									<?php 
									$comment = sanitize($val['comment']);
									if (mb_strlen($comment,'UTF-8') > 20){
										$text = mb_substr($comment, 0, 16,'UTF-8');
										echo $text.'…';
									}else{
										echo $comment;
									}?>
								</p>
							</div>
						</a>
					</article>
					<?php
						endforeach;
					?>

				</div>
				<?php pagination($currentPageNum, $dbProductData['total_page']);?>
			</div>

			<div class="side_column">
			<form name="" method="get">
				<h2 class="heading">検索する</h2>
				<div class="side_search_box">
					<p class="side_search_text">種類</p>
					<div class="main_select">
						<select name="c_id" id="" class="">
							<option value="<?php if(getFormData('c_id',true) == 0 )?>"<?php { echo 'selected' ;}?>>選択してください</option>
							<?php
								foreach($dbCategoryData as $key => $val):
							?>
							<option value="<?php echo $val['id'] ?>" <?php if(getFormData('c_id',true) == $val['id'] ){ echo 'selected'; }?>>
								<?php echo $val['name']?>
							</option>
							<?php
								endforeach;
							?>
						</select>
					</div>
				</div>
				<div class="side_search_box">
					<p class="side_search_text">募集状況</p>
					<div class="main_select">
						<select name="s_id" id="" class="">
							<option value="<?php if(getFormData('s_id',true) == 0 )?>" <?php { echo 'selected' ;}?>>選択してください</option>
							<?php
								foreach($dbStatusData as $key => $val):
							?>
								<option value="<?php echo $val['id'] ?>" <?php if(getFormData('s_id',true) == $val['id'] ){ echo 'selected'; }?>>
									<?php echo $val['name']?>
								</option>
							<?php
								endforeach;
							?>
						</select>
					</div>
				</div>
				<input type="submit" value="検索">
				</form>
			</div>

		</div>
	</main>
	<?php require('footer.php');?>