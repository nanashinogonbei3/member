<?php
session_start();



$id = $_POST['recipe_id'];


try {

    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT *
        FROM advices
        WHERE recipe_id = '" . $id . "'
        ";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);



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


//  フォームが空送信ならエラーチェックを走らせます
if (!empty($_POST['send'])) {


    if ($_POST['advice'] === '') {
        $error['advice'] = 'blank';
    }

    if (empty($error)) {

        $_SESSION['advice'] = $_POST;
    }

    // エラーが無ければ、インサートに遷移する
    header('Location: add_advice.php');
    exit();
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
    <link rel="stylesheet" href="css/stylesheet2.css">
    <!-- アコーディオン チェックボックス・リスト -->
    <link rel="stylesheet" href="css/stylesheet_a.css">

</head>

<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <span style="font-size:20px;color:green;">

                <dt class="title_font">アドバイスの編集画面</dt>
            </span>

            <!-- ログアウト -->
            <div class="div_logout"><input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../../logout/process.php'">
  
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
                        <dt class="p_font_rarge"><span style="font-size:18px">アドバイスの編集</span></dt>
                        <!-- 区切り線 -->
                        <div class="line"></div>
                        
                    </div>
                    <br>
                    <pre></pre><br>


                    <div class="categories_comprehensive">
                      

                        <!-- 左側 はじまり -->
                        <div class="div_width">


                            <form action="" method="POST">

                                <input type="hidden" name="member_id" value="<?php echo $_SESSION['member'] ?>">
                                <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">
                                <!-- アドバイス -->
                                <?php if (!empty($_POST['advice'])) { ?>
                                    <textarea class="textarea" name="advice" rows="8" cols="40" placeholder='レシピのアドバイスを入力' maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                                                        $_POST['advice'],
                                                                                                                                                        ENT_QUOTES
                                                                                                                                                    )); ?>"></textarea>
                                <?php } else { ?>
                                    <textarea class="textarea" name="advice" rows="8" cols="40" placeholder='レシピのアドバイスを入力' maxlength="255" value=""></textarea>
                                <?php } ?>

                                <br><br>
                                <dt><span style="font-size:13px; color:#555555">

                                </dt>
                                <div class="label">
                                    <table class="table">
                                        <tr>
                                            <input type="checkbox" value='<?php echo $list['id'] ?>'>
                                            登録します。
                                        </tr>
                                    </table>
                                </div>
                                <!-- ボタン -->
                                <div class="bottun3">
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
                            </form>
                        </div>

                    </div>

                    <!-- 表示欄 -->

                    <!-- カテゴリ登録の表示欄 -->

                    <div class="space3">

                        <table width="800px">
                            <thead>
                                <tr>

                                    <th>
                                        <dt class="wf-sawarabimincho">
                                    </th>
                                    <!-- ↓ID -->
                                    <th></th>
                                    <!-- ↓カテゴリ名 -->
                                    <th></th>
                                    <!-- ↓✅ -->
                                    <th></th>
                                    <!-- ↓削除btn -->
                                    <th></th>
                                    <!-- ↑アドバイス編集おわり -->
                                    <!-- ↓ID -->
                                    <th></th>
                                    <!-- ↓カテゴリ名 -->
                                    <th></th>
                                    <!-- ↓✅ -->
                                    <th></th>
                                    <!-- ↓削除btn -->
                                    <th></th>
                                    <!-- ↑アドバイス・材料の一口メモおわり -->
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($list as $key => $v) : ?>
                                    <tr>



                                        <td width="500px">
                                            <?php echo $v['advice'] ?>

                                            <!-- 編集 -->
                                            <form method="POST" action="update_advice.php">

                                                <input type="hidden" name="member_id" value="<?php echo $_SESSION['member'] ?>">
                                                <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">

                                                <!-- 登録したアドバイスの✅選択と編集 -->
                                                <input type="checkbox" name="id" value="<?php echo $v['id'] ?>">
                                                <p>
                                                    <textarea name="advice" rows="200" cols="150" placeholder='レシピのアドバイスを入力' maxlength="255" value=""> </textarea>
                                                    <input type="submit" class="update" value="update" name="edit" class="execution-btn">
                                            </form>
                                        <td>
                                            <!-- フォーム 登録カテゴリ削除 -->
                                            <form method="POST" action="action_advice.php">
                                                <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">
                                                <input type="hidden" name="is_deleted" value="1">
                                                <input type="hidden" name="id" value="<?php echo $v['id'] ?>">
                                                <input type="submit" value="Delete" name="del" class="execution-btn">
                                            </form>
                                            </p>
                                        </td>

                                    </tr>

                                <?php endforeach ?>
                            </tbody>
                        </table>
                        </td>
                        </td>
                    </div>
                </div>
                <div class="div_font_inline">
                    <!-- 戻る -->
                    <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

</body>

</html>