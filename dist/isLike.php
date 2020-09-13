<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================
if(isset($_POST['petId']) && isset($_SESSION['user_id']) && isLogin()){
  debug('POST送信があります。');
  $p_id = $_POST['petId'];
  debug('ペットのID'.$p_id);

  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM likes WHERE pets_id = :p_id AND user_id = :u_id';
    $data = array(':p_id'=> $p_id, ':u_id'=>$_SESSION['user_id']);

    $stmt = queryPost($dbh, $sql, $data);

    $resultCount = $stmt->rowCount();
    debug($resultCount);

    // レコードが１件でもある場合
    if(!empty($resultCount)){
      // レコードを削除する
      $sql = 'DELETE FROM likes WHERE pets_id = :p_id AND user_id = :u_id';
      $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
      $stmt = queryPost($dbh, $sql, $data);
    }else{
      $sql = 'INSERT INTO likes (pets_id, user_id, create_date) VALUES (:p_id, :u_id, :date)';
      $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh, $sql, $data);
    }

  }catch(Exception $e){
    error_log('エラー発生'.$e->getMessage());
  }
}
debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>