<?php
session_start();
require('../dbconnect.php');


try {

    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT * FROM members ';
    $sql .= 'ORDER BY created_date ASC';

    $stmt = $dbh->prepare($sql);

    $stmt->execute();
} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}



?>

<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>新規会員登録完了（登録処理済み）</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">

</head>



<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <p class="title_font">会員登録完了</p>
        </div>


        <div class="comprehensive">

            <!--  新規会員登録の登録処理 -->
            <div class='inline_block_2'>

                <div class="inline_block_3">

                    <!-- データベースに登録したテキストデータと画像でーたを出力表示する -->

                    <div class="div_font_inline">
                        <p class="p_font_rarge">会員登録が正常に行われました</p>
                        <div class="line"></div>
                    </div>

                    <!-- アイコン画像 -->
                    <p class="p_font_rarge">🔲アイコン画像</p>
                    <?php if ($_SESSION['personal']['icon_img'] !== '') : ?>
                        <img src="../member_picture/<?php print(htmlspecialchars($_SESSION['personal']['icon_img'], ENT_QUOTES)); ?>">
                    <?php endif; ?>

                    <!-- 氏名（苗字） -->
                    <p class="p_font_rarge">🔲氏名</p>
                    <p><?php print(htmlspecialchars($_SESSION['personal']['last_name'])); ?></p>


                    <!-- 氏名（名前） -->
                    <p class="p_font_rarge">🔲氏名</p>
                    <p><?php print(htmlspecialchars($_SESSION['personal']['first_name'])); ?></p>


                    <!-- レシピ・ノートID (email)  -->
                    <p class="p_font_rarge">🔲ID (email)</p>
                    <p><?php print(htmlspecialchars($_SESSION['personal']['members_id'])); ?></p>

                    <!-- ニックネーム -->
                    <p class="p_font_rarge">🔲ニックネーム</p>
                    <p><?php print(htmlspecialchars($_SESSION['personal']['nickname'])); ?></p>

                    <!-- 電話番号 -->
                    <p class="p_font_rarge">🔲お電話番号</p>
                    <p><?php print(htmlspecialchars($_SESSION['personal']['phone_number'])); ?></p>

                    <!-- Password -->
                    <p class="p_font_rarge">🔲パスワード</p>
                    <p class="p_font_rarge">【パスワードは出力されません】</p>

                    <!-- うまくデータが渡されなかったら、エラー表示します -->
                    <!-- <?php ?> -->
                    <!-- <p class="p_font_rarge">その投稿は削除されたか、URLが間違えています</p> -->
                    <!-- <?php  ?> -->




                    <!-- ログイン　ボタン -->
                    <div class="div_font_inline">
                        <input type="button" value='ログイン画面' style="width:20%;padding:10px;font-size:15px;" onclick="location.href='../login/join.php'" class="btn-border">
                    </div>
                </div>

            </div>
        </div>
    </div>




</body>

</html>