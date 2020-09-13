<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
// ログイン認証
require('auth.php');

// 画面表示用データ取得
//================================
$u_id = $_SESSION['user_id'];
// DBからペットのデータを取得
$petData = getMyPets($u_id);
// DBから連絡掲示板データを取得
$bordData = getMyMsgsAndBord($u_id);
// DBからお気に入りデータを取得
$likeData = getMyLike($u_id);

debug('取得したペットのデータ：'.print_r($petData,true));
debug('取得した連絡掲示板のデータ：'.print_r($bordData,true));
debug('取得したお気に入りのデータ：'.print_r($likeData,true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php $siteTitle = 'マイページ';?>
<?php require('head.php');?>

<body>

  <?php require('header.php');?>

  <p id="js-show-msg" style="display:none;" class="msg-slide">
    <?php echo getSessionFlash('msg_success'); ?>
  </p>

  <main class="myPage_contents">
    <h2 class="main_heading">マイページ</h2>
    <div class="l-content-sm myPage_contents_inner">
      <div class="main_column">
        <div class="mypage_block">
          <div class="block_head">
            <h2 class="heading">登録した里親募集</h2>
          </div>
          <div class="block_content">
            <?php
              if(!empty($petData)):
                foreach($petData['data'] as $key => $val):
            ?>
            <div class="block_item">
              <a href="./registPet.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>">
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
                  <img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt="<?php echo sanitize($val['name'])?>">
                </div>
                <div class="item_text">
                  <h3 class="list_heading"><?php echo sanitize($val['name'])?></h3>
                  <p class="list_text">
                    <?php 
                      $comment = sanitize($val['comment']);
                      if(mb_strlen($comment,'UTF-8') > 20){
                        $text = mb_substr($comment,0,16,'UTF-8');
                          echo $text.'…';
                      }else{
                        echo $comment;
                      }
                    ?>
                  </p>
                </div>
              </a>
            </div>
            <?php
              endforeach;
            endif;
            ?>
          </div>
        </div>

        <div class="mypage_block">
          <div class="block_head">
            <h2 class="heading">連絡掲示板一覧</h2>
          </div>
          <table class="table">
            <thead>
              <tr class="head_table">
                <th class="head_day">最新送信日時</th>
                <th class="head_msg">メッセージ</th>
              </tr>
            </thead>
            <tbody>
             <?php
              if(!empty($bordData)){
                foreach($bordData as $key => $val){
                  if(!empty($val['msg'])){
                    $msg = array_shift($val['msg']);
             ?>
                 <tr>
                    <td><?php echo sanitize(date('Y.m.d H:i:s',strtotime($msg['send_date']))); ?></td>
                    <td><a href="msg.php?m_id=<?php echo sanitize($val['id']); ?>"><?php echo mb_substr(sanitize($msg['msg']),0,40); ?>...</a></td>
                </tr>
             <?php
                  }else{
             ?>
                 <tr>
                    <td>--</td>
                    <td><a href="msg.php?m_id=<?php echo sanitize($val['id']); ?>">まだメッセージはありません</a></td>
                </tr>
              <?php
                  }
                }
              }
            ?>
            </tbody>
          </table>
        </div>
        <div class="mypage_block">
            <div class="block_head">
              <h2 class="heading">お気に入り一覧</h2>
            </div>
            <div class="block_content">
              <?php
                if(!empty($likeData)):
                  foreach($likeData as $key => $val):
              ?>
              <div class="block_item">
                <a href="./petDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id']: '?p_id'.$val['id'];?>">
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
                    <img src="<?php echo showImg(sanitize($val['pic1']))?>" alt="<?php echo $val['name'];?>">
                  </div>
                  <div class="item_text">
                    <h3 class="list_heading"><?php echo sanitize($val['name'])?></h3>
                    <p class="list_text">
                      <?php 
                        $comment = sanitize($val['commment']);
                        if(mb_strlen($comment,'UTF-8') > 20){
                          $text = mb_substr($comment,0,16,'UTF-8');
                            echo $text.'…';
                        }else{
                          echo $comment;
                        }
                      ?>
                    </p>
                  </div>
                </a>
              </div>
              <?php
                endforeach;
              endif;
              ?>

            </div>
          </div>
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