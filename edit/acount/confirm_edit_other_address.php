<?php
session_start();



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
    // 何か行動した更新時刻より１時間経過したら、自動的にログイン画面に遷移します
} else {
    header('Location: ../../login/join.php');
    exit();
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
    <link rel="stylesheet" href="stylesheet2.css">


</head>

<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <p class="title_font">お届け先の確認</p>
        </div>


        <div class="comprehensive">

            <!--  新規会員登録の確認 -->
            <div class='inline_block_2'>

                <div class="inline_block_3">

                    <div class="div_font_inline">
                        <p class="p_font_rarge">ご記入頂いた内容を確認して、よろしければ「登録」ボタンをクリックしてください</p>
                        <div class="line"></div>
                    </div>
                    <!-- DBへ接続しデータをインサートする add.php のDB挿入ファイルをaction=add.phpでファイルを指定する -->
                    <form action="update_billing_address.php" method="POST" enctype="multipart/form-data">



                        <!-- フォーム6 届け先氏名 -->
                        <p class="p_font_rarge">●届け先氏名</p>
                        <dd><?= (htmlspecialchars($_SESSION['address']['last_name'], ENT_QUOTES)); ?></dd>
                        </p>

                        <!-- フォーム7 届け先名前 -->
                        <p class="p_font_rarge">●届け先名前</p>
                        <dd><?= (htmlspecialchars($_SESSION['address']['first_name'], ENT_QUOTES)); ?></dd>
                        </p>

                        <!-- フォーム8 連絡先 -->
                        <p class="p_font_rarge">●連絡先TEL</p>
                        <dd><?= (htmlspecialchars($_SESSION['address']['phone_number'], ENT_QUOTES)); ?></dd>
                        </p>


                        <!-- フォーム1 氏名（郵便番号） -->
                        <p class="p_font_rarge">●郵便番号</p>
                        <p class="p_font_rarge">
                            <!-- htmlspecialchars は、安全に出力するための記述です -->
                            <?= (htmlspecialchars($_SESSION['address']['post_number'], ENT_QUOTES)); ?>


                            <!-- フォーム1 住所1 -->
                        <p class="p_font_rarge">●住所1</p>
                        <p class="p_font_rarge">
                            <!-- htmlspecialchars は、安全に出力するための記述です -->
                            <?= (htmlspecialchars($_SESSION['address']['address1'], ENT_QUOTES)); ?>


                            <!-- フォーム2 住所2 -->
                        <p class="p_font_rarge"></p>
                        <p class="p_font_rarge">●住所2</p>
                        <dd><?= (htmlspecialchars($_SESSION['address']['address2'], ENT_QUOTES)); ?></dd>
                        </p>


                        <!-- フォーム3 住所3 -->
                        <p class="p_font_rarge">●住所3</p>
                        <p class="p_font_rarge">
                            <dd><?= (htmlspecialchars($_SESSION['address']['address3'], ENT_QUOTES)); ?></dd>
                        </p>


                        <!-- フォーム4 住所4 -->
                        <p class="p_font_rarge">●住所4</P>
                        <dd><?= (htmlspecialchars($_SESSION['address']['address4'], ENT_QUOTES)); ?></dd>
                        </p>

                        <!-- フォーム5 住所5 -->
                        <p class="p_font_rarge">●住所5</p>
                        <dd><?= (htmlspecialchars($_SESSION['address']['address5'], ENT_QUOTES)); ?></dd>
                        </p>



                        <p class="wf-sawarabimincho"></p>
                        <!-- 書き直すボタン -->

                        <input type="button" value='&laquo;&nbsp;書き直す' style="width: 115px; height: 25px" onclick="location.href='./address.php?id=<?php echo $_SESSION['id'] ?> action=rewrite'" class="btn-border">
                        <!-- 送信ボタン -->
                        <input type="submit" id="submit" value="登録する" />



                    </div>
                    </form>

            </div>
        </div>
    </div>

</body>

</html>