<?php
session_start();
// 親の材料カテゴリー（material_parent_categoriesテーブル）にユーザーが作った材料カテゴリーを追加できるシステム


$id = $_SESSION['recipe_id'];


if (!empty($_GET['id'])) {
    $id = $_GET['id'];
}


try {

    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // 親カテゴリーの一覧をプルダウン選択できるように表示
    $sql = 'SELECT id, materials_parent_category_name
            FROM material_parent_categories
            WHERE id = 8
            ';
    // ユーザー定義のレコード・id=8のみブラウザに表示させる。

    $stmt = $dbh->prepare($sql);

    //SQLを実行します。
    $stmt->execute();

    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($list as $v) {
        $v;
        $parent_category_id = $v['id'];
        $materials_parent_category_name = $v['materials_parent_category_name'];
    }


    // ログインユーザー作成した材料カテゴリーだけを取り出し
    // 親の材料カテゴリー編集とDELETEを行う

    $sql = "SELECT material_categories.id,
            material_categories.material_category_name,
            material_categories.parent_category_id, material_categories.is_deleted,
            material_categories.users_id, material_parent_categories.materials_parent_category_name
            FROM material_categories
            LEFT JOIN material_parent_categories ON material_categories.parent_category_id
            = material_parent_categories.id
            WHERE material_categories.users_id = '" . $_SESSION['member'] . "'
            AND material_categories.recipe_id = '" . $_SESSION['recipe_id'] . "'
            ";
    // ユーザーが作ったカテゴリ名、材料一口メモ、このレシピだけのを表示する

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $material_category = $result->fetchAll(PDO::FETCH_ASSOC);

 


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


// sendボタンが押されたら
// この場合は、form action="" にリンク先の'edit_mycategory.php'は書かないで
if (!empty($_POST['send'])) {

    //  エラーチェックを走らせます
    if ($_POST['parent_category_id'] === '') {
        $error['parent_category_id'] = 'blank';
    }

    if (empty($error)) {

        $_SESSION['material_category'] = $_POST;
    }

    // エラーが無ければ、インサートに遷移する
    header('Location: add_parent_material_category.php');
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
    <!-- 全体 -->
    <link rel="stylesheet" href="css/stylesheet2.css">
    <!-- アコーディオン チェックボックス・リスト -->
    <link rel="stylesheet" href="css/stylesheet_a.css">

</head>

<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <span style="font-size:20px;color:green;">

                <dt class="title_font">新規材料カテゴリー作成</dt>
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
                        <dt class="p_font_rarge"><span style="font-size:18px">材料カテゴリーの作成</span></dt>
                        <!-- 区切り線 -->
                        <div class="line"></div>
                        
                    </div>

                    <div class="categories_comprehensive">
                        

                        <!-- 左側 はじまり -->
                        <div class="div_width">


                            <form action="" method="POST">
                                <input type="hidden" name="parent_category_id" value="<?php echo $parent_category_id ?>">
                                <input type="hidden" name="users_id" value="<?php echo $_SESSION['member'] ?>">
                                <!-- ↑ログイン・ユーザーID -->
                                <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">

                                <!-- フォーム1 カテゴリー名の入力を行う -->

                                <input id='child_category' type="text" name="material_category_name" size="35" placeholder='カテゴリー名を入力' <?php if (!empty($_POST['material_category_name'])) : ?> maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                                                                                                                            $_POST['material_category_name'],
                                                                                                                                                                                                                            ENT_QUOTES
                                                                                                                                                                                                                        )); ?>">
                            <?php endif ?>

                            <!-- error -->
                            <?php if ($_SESSION['parent_category_id'] === 'blank') : ?>
                                <p class="error">*チェックボックスにチェックを入れてください</p>
                            <?php endif ?>

                            <dt><span style="font-size:13px; color:#555555">

                            </dt>
                            <div class="label">

                                <input id="acd-check1" class="acd-check" type="checkbox">

                                <?php foreach ($list as $key => $v) : ?>

                                    <table class="table">
                                        <tr>
                                            <input type="checkbox" name="parent_category_id[]" value='<?php echo $v['id'] ?>'>

                                            登録します。

                                        </tr>
                                    </table>
                                <?php endforeach ?>
                                <!-- </label> -->
                            </div>

                            


                            <tr></tr>
                            <!-- ボタン -->
                            <div class="bottun3">
                                <!-- 新規カテゴリー送信ボタン -->
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

                    <!-- ↑区切り線 -->

                    <!-- 表示欄 -->

                    <!-- カテゴリ登録の表示欄 -->

                    <div class="space3">
                        <p>🔲このレシピのカテゴリー</p>
                        <?php if (!empty($material_category)) { ?>

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
                                        <!-- ↑カテゴリ名編集おわり -->
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
                                    <?php foreach ($material_category as $key => $v) { ?>
                                        <tr>



                                            <!-- 1 -->

                                            <td width="500px">
                                                <?php echo $v['material_category_name'] ?>

                                                <!-- 編集 -->

                                                <form method="POST" action="update_children_material_categories.php">
                                                    <input type="hidden" name="parent_category_id" value="<?php echo $v['parent_category_id'] ?>">
                                                    <!-- ↑ parent_category_idの隠し送信 -->
                                                    <input type="hidden" name="users_id" value="<?php echo $_SESSION['member'] ?>">
                                                    <!-- ↑ ユーザーidの隠し送信 -->
                                                    <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">

                                                    <!-- 登録したカテゴリ名の✅選択と編集 -->
                                                    <input type="checkbox" name="id" value="<?php echo $v['id'] ?>">
                                                    <!--  カテゴリー名 -->
                                                    <p><input type="text" name="material_category_name" size="24" value="<?php echo $v['material_category_name'] ?>">
                                                        
                                                        <!--  編集 ボタン -->
                                                        <input type="submit" class="update" value="update" name="edit" class="execution-btn">
                                                    </p>

                                                </form>
                                            </td>
                    </div>

                

                    <td>
                        <!-- フォーム 登録カテゴリ削除 -->
                        <form method="POST" action="update_is_del_material_children_category.php">
                            <input type="hidden" name="is_deleted" value="1" <?= $v['is_deleted'] == 1 ?>>
                            <!-- ↑is_deleted == 1 （論理削除）の値をPOSTで隠して渡す -->
                            <input type="hidden" name="id" value="<?php echo $v['id'] ?>">
                            <!-- ↑カテゴリー・テーブルのidを隠して渡す -->
                            <!-- 削除 Delete ボタン -->
                            <input type="submit" value="Delete" name="del" class="execution-btn">
                    </td>

                    </form>
                    </tr>

                <?php } ?>
                </tbody>
                </table>
            <?php } else {
                            echo '<dt>カテゴリーは未登録です</dt>';
                        } ?>
                </div>

                <div class="div_font_inline">


                    <!-- 戻る -->
                    <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">
                  

                </div>
            <!-- 入力フォーム緑枠終わり -->            
            </div>
            
           
        </div>
    </div>
    </div>
    </div>

</body>

</html>