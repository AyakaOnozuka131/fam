<header>
  <div class="l-content-lg header_inner">
    <h1 class="heading ff-quicksand">fam</h1>
    <div class="ham" id="js-ham">
      <span class="ham_top"></span>
      <span class="ham-bottom"></span>
      <span class="ff-oswald ham-menu"></span>
    </div>
    <nav>
      <div class="inner">
        <ul class="nav_list">
          <?php if(empty($_SESSION['user_id'])):?>
            <li class="nav_item"><a href="./index.php">TOPへ戻る</a></li>
            <li class="nav_item"><a href="./login.php">ログイン</a></li>
            <li class="nav_item"><a href="./signup.php">会員登録</a></li>
          <?php else:?>
            <li class="nav_item"><a href="./index.php">TOPへ戻る</a></li>
            <li class="nav_item"><a href="./logout.php">ログアウト</a></li>
            <li class="nav_item"><a href="./mypage.php">マイページ</a></li>
          <?php endif;?>
        </ul>
      </div>
    </nav>
    <div class="ham_mask"></div>
  </div>
</header>