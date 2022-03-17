<?php

session_start();



if (empty($_SESSION['recipe'])) {
    // もし（ $_SESSION['recipe'] ）が空だったら、./index.php へリダイレクト
    header("Location: ./index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>あたらしいレシピの確認</title>

    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!--  -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet3.css">

</head>

<body>



    <!-- 入力された文字の表示フォーム  -->

    <body>


        <!-- POST は同じ値を別画面にはおくれないのでセッションに値を渡す -->
        <form method="POST" action="add.php" enctype="multipart/form-data">



            <div class="div_p">my recipes</div>

            <div class='inline_block_2'>
                <div class="parent">



                    <div class="div_hidari">
                        <!-- div 左側 -->




                        <!-- 入力確認1 メンバーID -->
                        <p class="kokoro">メンバーID<br /></p>
                        <div class="div_inlin">
                            <p><span style="background: linear-gradient(transparent 87%, #92FCFC 87%);"><?php echo $_SESSION['recipe']['members_id'] ?></span></s /p>
                        </div>


                        <!-- 入力確認1 レシピ名 -->
                        <p class="kokoro">レシピネーム<br /></p>
                        <div class="div_inlin">
                            <p><span style="background: linear-gradient(transparent 87%, #92FCFC 87%);"><?php echo $_SESSION['recipe']['recipe_name'] ?></span></s /p>
                        </div>


                        <!-- 入力確認2 調理時間 -->
                        <p class="wf-sawarabimincho">調理時間<br /></p>
                        <div class="div_inlin">
                            <p><span style="box-shadow: 0px -6px 5px -5px #92FCFC inset,0px 8px 4px -8px #92FCFC;"><?= $_SESSION['recipe']['cooking_time'] ?>分</span></p>
                        </div>


                        <!-- 入力確認3 コスト -->
                        <p class="kokoro">コスト</p>
                        <div class="div_inlin">
                            <p><span style="background: linear-gradient(transparent 87%, #92FCFC 87%);">￥<?= $_SESSION['recipe']['cost'] ?></span></p>
                        </div>

                        <!-- 入力確認4 何人分 -->
                        <p class="wf-sawarabimincho">何人分</p>
                        <div class="div_inlin">
                            <p><span style="background: linear-gradient(transparent 87%, #92FCFC 87%);"><?php echo $_SESSION['recipe']['how_many_servings'] ?>人分</span></p>
                        </div>


                    </div>

                    <div class="div_migi">
                        <!-- div 右側 -->


                        <!-- 入力確認5  作成日 -->

                        <p class="wf-sawarabimincho">作成日</p>
                        <div class="div_inlin">
                            <p><span style="background: linear-gradient(transparent 87%, #92FCFC 87%);"><?= $_SESSION['recipe']['created_date'] ?></span></p>
                        </div>

                        <!-- 入力確認6  画像 -->

                        <p class="wf-sawarabimincho">イメージ画像</p>
                        <div>
                            <img class="img" src="./images/<?php echo $_SESSION['recipe']['complete_img'] ?>" width="350px" height="auto" alt="イメージ">

                            <!-- エラー -->
                            <?php if (!empty($error['image'])) : ?>
                                <p class="error">* 写真などは「.gif」または「.jpg」「.png」の画像を指定してください</p>
                            <?php endif ?>


                        </div>

                        <!-- 入力確認6  動画 -->

                        <p class="wf-sawarabimincho">調理動画</p>



                        <!-- 戻るボタン -->
                        <div class='btn'>
                            <dt><input type="button" value='書き直す' onclick="location.href='./index.php'" class="btn-border"></dt>
                            <!-- 登録 ボタン DBへ登録する -->
                            <dt>データベースに登録してよろしいですか？<input type="submit" name="upload" value="登録" class="btn-border" /></dt>
                        </div>
                    </div>
                    <!-- div inlineblock 2 -->
                </div>

                <!-- div comprehensive -->
            </div>

        </form>


    </body>

</html>