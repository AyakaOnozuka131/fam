<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再設定　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

// 1.POST送信があるかどうか
if(!empty($_POST)){
    debug('POST送信があります。');
    debug('POST情報'.print_r($_POST,true));
    // 2.変数にPOST情報を代入
    $email = $_POST['email'];
    // 3.未入力チェック

    validRequired($email, 'email');

    if(empty($err_msg)){
        // 4.バリデーション
        validEmail($email, 'email');
        validMaxLen($email, 'email');

        if(empty($err_msg)){
            debug('バリデーションOKです。');
            // 5.例外処理
            try{
                $dbh = dbConnect();
                $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
                $data = array(':email' => $email);
                // 6.クエリ
                $stmt = queryPost($dbh, $sql, $data);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                debug('クエリの中身'.print_r($result, true));

                // 7.EmailがDBにあるかどうか
                if($stmt && array_shift($result)){
                    debug('クエリ成功。DB登録あり。'.print_r(array_shift($result)));
                    $_SESSION['msg_success'] = SUC07;

                // 8.認証キーを作成
                $auth_key = makeRandKey();
                debug('認証キー'.print_r($auth_key, true));

                // 9.メールを作成
                $from  = 'info@fam.com';
                $to = $email;
                $subject = '【パスワード再発行認証】｜FAM';
                $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：http://localhost:8001/passRemindRecieve.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります

認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
http://localhost:8001/passRemindSend.php

////////////////////////////////////////
fam
////////////////////////////////////////
EOT;
                // メール送信
                sendMail($from, $to, $subject, $comment);
                // 10.認証に必要な情報をセッションに詰める
                $_SESSION['auth_key'] = $auth_key;
                $_SESSION['auth_email'] = $email;
                $_SESSION['auth_key_limit'] = time()+(60*30); //現在時刻より30分後のUNIXタイムスタンプを入れる
                debug('セッション変数の中身：'.print_r($_SESSION,true));
                // 11.認証キーページへ遷移
                header("Location:passRemindRecieve.php");
            }else{
                debug('クエリに失敗したかDBに登録のないEmailが入力されました。');
                $err_msg['common'] = MSG07;
            }

            }catch (Exception $e) {
                error_log('エラー発生'. $e-> getMessage());
                $err_msg['common'] = MSG07;
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
                <p class="form_sub_heading">パスワードを忘れてしまった場合、以下から再設定することが可能です。<br>
                    登録時に使用したメールアドレスを入力してください。</p>
                <div class="form_container">
                    <div class="area-msg">
                        <?php errMsg('common'); ?>
                    </div>
                    <div class="form_item">
                        <label for="email" class="">Email</label>
                        <input type="email" name="email" value="<?php echo getFormData('email');?>">
                        <span class="separator"></span>
                        <div class="area-msg">
                            <?php errMsg('email'); ?>
                        </div>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn" value="送信する">
                    </div>
                    <p class="mypage_text"><a href="./mypage.php">&lt;&lt;マイページへ戻る</a></p>
                </div>
            </form>
        </div>
    </main>

    <?php require('footer.php');?>