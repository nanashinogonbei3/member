<?php
session_start();
// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');
// 右側から左に代入されている（./resistration/new.php 初頭で処理済）
// $a = 1;
// セッションは保存でき、POSTは一方通行で一度切り送っておわり
// パソコンがシャットダウンするまで保存できる
// ポストしたものをセッションに一時保存してadd.phpへ渡しDBにデータが保存ができる
// $_SESSION['personal']['last_name'] = $_POST['last_name'];
$_SESSION['members']['id'] = $_SESSION['id'];

if (!isset($_SESSION['id'])) {
    header('Location: ./modify.php');
    exit;
}


?>


<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>パスワード変更確認</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">
</head>


<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <p class="title_font">パスワードの変更確認</p>
        </div>


        <div class="comprehensive">

            <!--  新規会員登録の確認 -->
            <div class='inline_block_2'>

                <div class="inline_block_3">

                    <div class="div_font_inline">
                        <p class="p_font_rarge">ご記入頂いた内容でよろしければ「登録」ボタンをクリックしてください</p>
                        <div class="line"></div>
                    </div>
                    <!-- DBへ接続しデータをインサートする add.php のDB挿入ファイルをaction=add.phpでファイルを指定する -->
                    <form action="./update.php" method="POST">

                        <!-- インビジュアルPOST -->
                        <!-- 冒頭で代入した $id = $_GET['id'] (今ログイン中のID 'id' を隠して送る -->
                        <input type="hidden" name="id" <?php echo $_SESSION['members']['id'] ?>>


                        <!-- フォーム5   パスワード-->
                        <p class="p_font_rarge">●パスワード</p>
                        <dd><?= (htmlspecialchars($_SESSION['members']['password'], ENT_QUOTES)); ?></dd>
                        </p>



                        <p class="wf-sawarabimincho"></p>

                        <!-- 書き直すボタン -->
                        <input type="button" value='&laquo;&nbsp;書き直す' style="width: 115px; height: 25px" onclick="location.href='./modify.php?id=<?php echo $_SESSION['id'] ?> action=rewrite'" class="btn-border">
                        <!-- 登録ボタン -->
                        <input type="submit" id="submit" value="登録する" />



                </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>