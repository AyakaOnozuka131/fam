<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

//================================
// ログイン画面処理
//================================
// post送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります。');

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    //emailの形式・最大文字数チェック
    validEmail($email, 'email');
    validMaxLen($email, 'email');

    //パスワードの半角英数字・最大値・最小値チェック
    validHalf($pass, 'pass');
    validMaxLen($pass, 'pass');
    validMinLen($pass, 'pass');

    // 未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');

    if(empty($err_msg)){
        debug('バリデーションOKです。');

        try{
            $dbh = dbConnect();
            $sql = 'SELECT password,id FROM users WHERE email = :email';
            $data = array(':email' => $email);
            $stmt = queryPost($dbh, $sql, $data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            debug('クエリ結果の中身：'.print_r($result,true));

            // パスワード照合
            if(!empty($result) && password_verify($pass, array_shift($result))){
                debug('パスワードがマッチしました。');

                //ログイン有効期限（デフォルトを１時間とする）
                $sesLimit = 60*60;
                // 最終ログイン日時を現在日時に
                $_SESSION['login_date'] = time();

                if($pass_save){
                    debug('ログイン保持にチェックがあります。');
                    $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                }else{
                    debug('ログイン保持にチェックはありません。');
                    $_SESSION['login_limit'] = $sesLimit;
                }
                // ユーザーIDを格納
                $_SESSION['user_id'] = $result['id'];

                debug('セッション変数の中身：'.print_r($_SESSION,true));
                debug('マイページへ遷移します。');
                header("Location:mypage.php"); //マイページへ
            }else{
                debug('パスワードがアンマッチです。');
                $err_msg['common'] = MSG09;
            }
        }catch (Exception $e){
            error_log('エラー発生:'. $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php $siteTitle = 'ログイン画面';?>
<?php require('head.php');?>

<body>
    <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg_success');?>
    </p>

    <?php require('header.php');?>
    <main class="user_register">
        <div class="l-content-lg">
            <form action="" method="post">
                <h2 class="form_heading">ログイン</h2>
                <div class="form_container">
                    <div class="form_item">
                        <label for="email" class="">Email</label>
                        <input type="email" name="email">
                        <span class="separator"> </span>
                        <div class="area-msg"><?php errMsg('email');?></div>
                    </div>
                    <div class="form_item">
                        <label for="pass" class="">Password</label>
                        <input type="password" name="pass">
                        <span class="separator"></span>
                        <div class="area-msg"><?php errMsg('pass');?></div>
                    </div>
                    <div class="form_item">
                        <input type="checkbox" name="pass_save" id="pass_save">
                        <label for="pass_save" class="checkbox">次回ログインを省略する</label>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn" value="登録する">
                    </div>
                    <p class="pass_remind_text"><a href="./passRemindSend.php">パスワードをお忘れの方はこちら</a></p>
                </div>
            </form>
        </div>
    </main>
<?php require('footer.php');?>
