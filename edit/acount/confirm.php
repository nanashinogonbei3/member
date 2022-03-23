<?php
session_start();
// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');



// もしセッションが空なら、modify.php（会員登録変更画面）に差し戻される
// /member/resistration/new.php
// $_SESSION['   ']に、「内容が入っていない場合」に、true で、
// if 構文の内容の処理を実行します。つまり、入力画面を正しく実行せずに
// いきなり/rasitration/confirm.php を呼び出そうとした時つまり、
// 実行画面「DB登録画面」が呼び出された、という事になりますので、
// その場合強制的に、入力フォーム画面に戻ります。
// 22行目で決めた$_SESSION['personal']['///']が22～27行目の各セッションを
// 包括して$_SESSION['personal']に内容が入っていないのに、DB登録画面に飛んだ時に
// 強制的に、
if (!isset($_SESSION['members'])) {
    header('Location: ./modify.php');
    exit;
}

?>


<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>会員情報変更確認</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">

</head>



<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <p class="title_font">会員情報変更の確認</p>
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
                    <form action="update.php" method="POST" enctype="multipart/form-data">

                        <!-- インビジュアルPOST -->
                        <!-- 今ログイン中のID 'id' を隠して送る -->
                        <input type="hidden" name="id" <?php echo $_SESSION['member'] ?>>

                        <!-- フォーム1 氏名（苗字） -->
                        <p class="p_font_rarge">●氏名</p>
                        <p class="p_font_rarge">
                            <!-- htmlspecialchars は、安全に出力するための記述です -->
                            <?= (htmlspecialchars($_SESSION['members']['last_name'], ENT_QUOTES)); ?>


                            <!-- フォーム1 氏名（名前） -->
                        <p class="p_font_rarge">●名前</p>
                        <p class="p_font_rarge">
                            <?= (htmlspecialchars($_SESSION['members']['first_name'], ENT_QUOTES)); ?>



                            <!-- フォーム3ニックネーム -->
                        <p class="p_font_rarge">●ニックネーム</p>
                        <p class="p_font_rarge">
                            <dd><?= (htmlspecialchars($_SESSION['members']['nickname'], ENT_QUOTES)); ?></dd>
                        </p>


                        <!-- フォーム4アイコン画像 -->
                        <p class="p_font_rarge">●アイコン画像</P>
                        <?php if ($_SESSION['members']['icon_img'] !== '') : ?>
                            <img src="../../member_picture/<?php print(htmlspecialchars($_SESSION['members']['icon_img'], ENT_QUOTES)); ?>">
                        <?php endif; ?>

                        <!-- フォーム5   お電話番号 -->
                        <p class="p_font_rarge">●お電話番号</p>
                        <dd><?= (htmlspecialchars($_SESSION['members']['phone_number'], ENT_QUOTES)); ?></dd>
                        </p>





                        <p class="wf-sawarabimincho"></p>
                        <!-- 書き直すボタン -->

                        <input type="button" value='&laquo;&nbsp;書き直す' style="width: 115px; height: 25px" onclick="location.href='./modify.php?id=<?php echo $_SESSION['id'] ?> action=rewrite'" class="btn-border">
                        <!-- 送信ボタン -->
                        <input type="submit" id="submit" value="登録する" />



                    </div>
                    </form>
            </div>
        </div>
    </div>

</body>

</html>