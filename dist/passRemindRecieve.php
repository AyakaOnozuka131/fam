<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再設定　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証はなし（ログインできない人が使う画面だから）

// 0.セッションに認証キーがあるかどうか判定
if(empty($_SESSION['auth_key'])){
    // なければ認証キーページへリダイレクト
    heaer("Location:passRemindSend.php");
}

//================================
// 画面処理
//================================

// 1.POST送信があるかどうか
if(!empty($_POST)){
    debug('POST送信があります。');
    debug('POST情報：'.print_r($_POST,true));
    // 2.変数に認証キーを代入
    $auth_key = $_POST['token'];

    // 3.未入力チェック
    validRequired($auth_key, 'token');

    if(empty($err_msg)){
        debug('未入力チェックOK。');
        // 4.バリデーション
        // 4-1.固定長チェック
        validLength($auth_key, 'token');
        // 4-2.半角チェック
        validHalf($auth_key, 'token');

        if(empty($err_msg)){
            debug('バリデーションOK。');

            // 認証キーが相違ないかチェック
            if($auth_key !== $_SESSION['auth_key']){
                $err_msg['common'] = MSG15;
            }
            // 認証キーの有効期限が過ぎていないかチェック
            if(time() > $_SESSION['auth_key_limit']){
                $err_msg['common'] = MSG16;
            }

            if(empty($err_msg)){
                debug('認証OKです。');

                // パスワードの再発行
                $pass = makeRandKey();
                debug('パスワードの再発行'.print_r($pass, true));

                // 5.例外処理
                try{
                    $dbh = dbConnect();
                    $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
                    $data = array(':email' => $_SESSION['auth_email'], ':pass' => password_hash($pass, PASSWORD_DEFAULT));

                    // 6.クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);
                    // 7.メールを送信
                    if($stmt){
                        debug('クエリ成功しました。');
                        $from = 'info@fam.com';
                        $to = $_SESSION['auth_email'];
                        $subject = '【パスワード再発行完了】｜fam';
                        $comment = <<<EOT
本メールアドレス宛にパスワードの再発行を致しました。
下記のURLにて再発行パスワードをご入力頂き、ログインください。

ログインページ：http://localhost:8001/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードのご変更をお願い致します

////////////////////////////////////////
ウェブカツマーケットカスタマーセンター
URL  http://webukatu.com/
E-mail info@webukatu.com
////////////////////////////////////////
EOT;
                        sendMail($from, $to, $subject, $comment);
                    // 8.セッション削除
                    session_unset();
                    // 9.セッションにメッセージを詰める
                    $_SESSION['msg_success'] = SUC03;
                    debug('セッション変数の中身：'.print_r($_SESSION,true));

                    header("Location:login.php"); //ログインページへ

                    }else{
                        debug('クエリに失敗しました。');
                        $err_msg['common'] = MSG07;
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
<?php $siteTitle = 'パスワード再設定';?>
<?php require('head.php');?>

<body>

<?php require('header.php');?>
    <main class="user_register">
        <div class="l-content-lg">
            <form action="" method="post">
                <h2 class="form_heading">パスワードの再設定</h2>
                <p class="form_sub_heading">ご指定のメールアドレスにお送りした【パスワード再発行認証】メール内にある<br>「認証キー」をご入力ください。</p>
                <div class="form_container">
                    <div class="area-msg">
                        <?php errMsg('common');?>
                    </div>
                    <div class="form_item">
                        <label for="token" class="">認証キー</label>
                        <input type="text" name="token" value="<?php echo getFormData('token');?>">
                        <span class="separator"></span>
                        <div class="area-msg">
                            <?php errMsg('token');?>
                        </div>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn" value="再発行する">
                    </div>
                    <p class="mypage_text"><a href="./passRemindSend.php">&lt;&lt; パスワード再発行メールを再度送信する</a></p>
                </div>
            </form>
        </div>
    </main>

    <?php require('footer.php');?>