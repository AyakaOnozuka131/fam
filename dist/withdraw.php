<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会ページ　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
if(!empty($_POST)){
    // 1.POST送信ありの場合
    debug('POST送信がありました。');
    // 2.DB接続前に例外処理
    try {
        // 3.DB接続
        $dbh = dbConnect();
        // 4.SQlの作成
        // 4-1.usersテーブルアップデート→delete_flgを1にする。user_idで条件指定（条件指定しないと、usersの人すべてがupdateされる）
        $sqlUsers = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id';
        // 4-2.pet
        $sqlPet = 'UPDATE pet SET delete_flg = 1 WHERE user_id = :u_id';
        // 4-3.likes
        $sqlLikes = 'UPDATE likes SET delete_flg = 1 WHERE user_id = :u_id';
        // 5.データの流し込み（user_idの虫食いを埋める）
        $data = array(':u_id' => $_SESSION['user_id']);
        // 6.クエリの実行
        $stmtUsers = queryPost($dbh, $sqlUsers, $data);
        $stmtPet = queryPost($dbh, $sqlPet, $data);
        $stmtLikes = queryPost($dbh, $sqlLikes, $data);
        // 7.クエリ成功ver(分岐)
        if($stmtUsers && $stmtPet){
        // 7-1.成功したら、session_destroy
            debug('クエリが成功しました。');
            session_destroy();
            debug('セッション変数の中身：'.print_r($_SESSION,true));
            // TOPページへ遷移させる
            debug('TOPページへ遷移します。');
            header("Location:index.php");
        }else{
            // 8.クエリ失敗ver
            debug('クエリが失敗しました。');
            // 8-1.エラーメッセージ　MSG07
            $err_msg['common'] = MSG07;
        }


    }catch (Exception $e){
        error_log('エラー発生:'.$e -> getMessage());
        $err_msg['common'] = MSG07;
    }

}

?>
<?php $siteTitle = '退会';?>
<?php require('head.php');?>

<body>

    <?php require('header.php');?>
    <main class="user_register">
        <div class="l-content-lg">
            <form action="" method="post">
                <h2 class="form_heading">退会</h2>
                <div class="form_container">
                <div class="area-msg">
                    <?php 
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                    <div class="btn-container btn-widthdraw">
                        <input type="submit" class="btn" value="退会する" name="submit">
                    </div>
                    <div class="back_link">
                        <a href="index.php">&lt;&lt;マイページへ戻る</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <?php require('footer.php');?>