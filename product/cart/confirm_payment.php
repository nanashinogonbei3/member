<?php
session_start();


// var_dump($_SESSION["orderCustomerId"]);

// // ----------------------------
// echo 'orderセッション';
// echo '<pre>';
// var_dump($_SESSION['orderCustomerId']);
// echo '</pre>';
// // -----------------------------
// echo 'cartセッション';
// echo '<pre>';
// var_dump($_SESSION['cart']);
// echo '</pre>';
// // -----------------------------
// exit;



// if (empty($_SESSION['payment'])) {
//     header('Location: ./payment_method.php');
//     exit;
// }





// セッションに記録された時間が、今の時間よりも大きい、つまりログイン時間から
// 1時間以上たっていた場合,という意味
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    // （1時間が経過していたら、）ログアウトし、ログイン画面に遷移する
    $_SESSION['time'] = time();
    // 現在の時刻で上書きします。こうすることで、何か行動したことで上書きすることで
    // 最後の時刻から１時間を記録することができるようになる。 
} elseif ($_SESSION['member'] = []) {
    header('Location: ../../login/join.php');
    exit();
    // 更新時刻より１時間経過していなくとも、クッキーの削除でセッション情報が空になったら
    // ログイン画面に遷移する
} else {
    header('Location: ../../login/join.php');
    exit();
    // 何か行動した更新時刻より１時間経過したら、自動的にログイン画面に遷移します
}

?>


<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>お届け先の確認</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">


</head>

<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <p class="title_font">お支払方法の確認</p>
        </div>


        <div class="comprehensive">

            <div class='inline_block_2'>

                <div class="inline_block_3">

                    <div class="div_font_inline">
                        <p class="p_font_rarge">ご確認して、よろしければ「登録」ボタンをクリックしてください</p>
                        <div class="line"></div>
                    </div>




                    <p class="p_font_rarge">●支払方法</p>
               
                        <?= (htmlspecialchars($_SESSION['payment']['method'], ENT_QUOTES)); ?>

                    <dt class="wf-sawarabimincho">
                    <!-- 支払方法を変更するボタン -->

                    <input type="button" value='&laquo;&nbsp;支払方法を変更する' style="width: 180px; height: 25px" onclick="location.href='./payment_method.php?id=<?php echo $_SESSION['id'] ?> action=rewrite'" class="btn-border">
                    </dt>
                    <br>

                    <!-- -------------------------------------------------- -->
                  
                    <?php if (!empty($_SESSION['cart'])) : ?>
                        
                <form action="order_middle_add.php" method="POST" enctype="multipart/form-data">

                            <input type="submit" id="submit" class="re-order" value="注文を確定する" />
                        <!-- </div> -->
                </form>


            <?php endif ?>
            </div>
        </div>
    </div>

</body>

</html>