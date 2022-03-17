<?php
    session_start();
    // レシピID
    $id = $_GET['id'];



try {



    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // レシピのサブタイトル・コメントを表示する
    $sql = "SELECT id, recipe_id, sub_title, comment FROM recipe_subtitles WHERE recipe_id= '" . $id . "' ";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $subtitle = $result->fetchAll(PDO::FETCH_ASSOC);




    // メンバーズ・テーブルに接続する
    $sql = 'SELECT members.id FROM members WHERE id = ' . $_SESSION['member'] . ' ';

    $stmt = $dbh->prepare($sql);

    //SQLを実行します。
    $stmt->execute();

    $member = $stmt->fetch(PDO::FETCH_ASSOC);


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


} catch (PDOException $e) {
    echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
    echo $e->getMessage();
    exit;
}


    // sendボタンを押下した時エラーチェックを走らせる
    if (isset($_POST['send'])) {

        if ($_POST['id'] == '') {
            $error['cheked'] = 'blank';
        }



        if (empty($error)) {

            $_SESSION['recipe'] = $_POST;



            // エラーが無ければ、インサートに遷移する
            header('Location: add_subtitle.php');
            exit();
        }
    }


        // ログイン時間から１時間たってたらログイン画面に戻る。

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
    <title>新規カテゴリー作成</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <!-- 全体 -->
    <link rel="stylesheet" href="css/stylesheet2.css">
    <!-- アコーディオン チェックボックス・リスト -->
    <link rel="stylesheet" href="css/stylesheet_a.css">

</head>

<bod>

    <div class='inline_block_1'>

        <div class='div_p'>
            <span style="font-size:20px;color:green;">

                <dt class="title_font">レシピのサブタイトルをつける</dt>
            </span>

            <!-- ログアウト -->
            <div class="div_logout"><input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../../logout/process.php'">

            </div>
            <!-- マイページ -->
            <div class="div_logout"><input type="button" value='マイページ' class="logout_btn" onclick="location.href='../../login/process.php'">

            </div>
            <!-- みんなのレシピ -->
            <div class="div_logout"><input type="button" value='みんなのレシピ' class="logout_btn" onclick="location.href='../../top/confirm.php'">

            </div>
            <!-- div_p おわり -->
        </div>



        <div class="comprehensive">

            <div class='inline_block_2_2'>

                <div class="inline_block_3_2">


                    <div class="div_font_inline">
                        <dt class="p_font_rarge"><span style="font-size:18px">サブタイトルの作成</span></dt>
                        <!-- 区切り線 -->
                        <div class="line"></div>

                    </div>
                    <br><br>


                    <div class="categories_comprehensive">


                        <!-- 左側 はじまり -->
                        <div class="div_width">



                        </div>


                    <!-- div_hidari おわり -->
                    </div>



                    <form action="add_subtitle.php" method="GET">
                        <!-- レシピIDの隠し送信 -->
                        <input type="hidden" name="recipe_id" value="<?php echo $id ?>">



                        <!-- サブ・タイトル -->
                        <dt class="wf-sawarabimincho">
                            <span style="font-size:16px;">


                                <!--1. レシピのサブタイトル入力フォーム -->
                                <?php if (!empty($_POST['sub_title'])) { ?>
                                <input id='child_category' type="text" name="sub_title" size="35" placeholder='サブタイトルを入力する' maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                                                            $_POST['sub_title'],
                                                                                                                                                            ENT_QUOTES
                                                                                                                                                        )); ?>">
                            </span>
                        </dt>
                    <?php } else { ?>
                        <input id='child_category' type="text" name="sub_title" size="35" placeholder='サブタイトルを入力する' maxlength="255" value="">
                        </span></dt>
                    <?php } ?>



                    <!-- 説明文 -->
                    <dt><span style="font-size:13px; color:#555555">
                            レシピのヒンディー語を日本語で解釈します</span></dt>
                    <br>



                    <!-- コメントの入力フォーム -->

                    <!-- レシピの説明や、思い入れ、好きなことを自由にコメントを入力します！ -->
                    <?php if (!empty($_POST['comment'])) { ?>
                        <textarea class="textarea" name="comment" rows="8" cols="40" placeholder='レシピを紹介するコメントを入力してください' maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                                                    $_POST['comment'],
                                                                                                                                                    ENT_QUOTES
                                                                                                                                                )); ?>"></textarea>
                    <?php } else { ?>
                        <textarea class="textarea" name="comment" rows="8" cols="40" placeholder='レシピを紹介するコメントを入力してください' maxlength="255" value=""></textarea>
                    <?php } ?>




                    <br>


                    <div class="label">


                        <!-- 1 -->

                        <table class="table">

                        </table>

                        </label>
                    </div>
                    <br>




            
                    <div class="bottun5">
                        <!-- 送信ボタン -->
                        <dt><input type="submit" class="update" value="登録" name="send" style="width: 60px;
                            color: #4F5902;
                            height: 33px;
                            font-size: 16px;
                            border-radius: 2px;
                            border: none;
                            background-color: #E9C8A7;
                            background-color: #D9CC1E
                            ">
                        </dt>
                        <br>



                    </form>

                </div>


                <!-- 表示欄 -->


                <?php if (!empty($subtitle) || !empty($comment)) { ?>
                    <!-- 登録の表示欄 -->


                    <div class="space3">
                        <p>🔲このレシピのサブタイトル</p>

                        <table width="880px">
                            <thead>
                                <tr>
                                    <th>
                                        <dt class="wf-sawarabimincho">
                                    </th>

                                    <th></th>

                                    <th></th>

                                    <th></th>

                                    <th></th>

                                    <!-- ↑コメント・おわり -->
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                foreach ($subtitle as $key => $v) { ?>

                                    <!-- 編集フォーム レシピのサブタイトル 編集 はじまり -->
                                    <td width="500px">

                                        <form method="GET" action="update_recipe_subtitle.php">

                                            <input type="hidden" name="recipe_id" value="<?php echo $id ?>">
                                            <!-- ↑レシピIDの隠し送信 -->
                                            <tr>
                                                <td width="210px">
                                                    <input type="checkbox" name="id" value="<?php echo $v['id'] ?>"><?php echo $v['sub_title'] ?>
                                                    <p><input type="text" name="sub_title" size="24">
                                                        <!-- value="< echo $v['sub_title'] ?>"  -->
                                                        <!-- ↑もしも、元の編集前の表示をしたいときはこれを入れる。 -->

                                                        <!--  編集 ボタン -->
                                                        <input type="submit" class="update" value="編集する" name="edit" class="execution-btn">
                                                    </p>





                                                </td>
                                                <!-- レシピのサブタイトル 編集 おわり -->



                                                <!-- 編集フォーム レシピのコメントの編集 はじまり -->
                                                <td width="550px">
                                                    <input type="checkbox" name="id" value="<?php echo $v['id'] ?>"><?php echo $v['comment'] ?>
                                                    <p><textarea class="textarea-comment" name="comment" size="30" value="<?php echo $v['comment'] ?>"></textarea>
                                                        <!--  編集 ボタン -->
                                                        <input type="submit" class="update" value="編集する" name="edit" class="execution-btn">
                                                    </p>
                                        </form>
                                    </td>
                                    <td>
                                        <!-- 削除 -->
                                        <form method="POST" action="action_subtitle.php">
                                            <input type="hidden" name="is_deleted" value="1">
                                            <input type="hidden" name="id" value="<?php echo $v['id'] ?>">
                                            <input type="submit" value="Delete" name="del" class="execution-btn">
                                        </form>
                                    </td>
                                    </tr>


                                <?php } ?>

                            <?php } else { ?>

                                <?php echo 'サブタイトル、コメントのご登録はありません'; ?>
                                <!-- 区切り線 -->
                                <div class="line3"></div>

                            <?php } ?>

                            </tbody>
                        </table>


                        <div class="div_font_inline">

                            <!-- キャンセルボタンを押下したら、元の画面に戻る -->
                            <input type="button" class="re-order" onclick=" 
                location.href='./confirm.php?id=<?php echo $id ?> '" value='戻る'>
                        </div>



                    </div>



             <!-- 入力フォーム緑枠終わり -->
            </div>



        </div>
    </div>
    </div>

    </body>

</html>