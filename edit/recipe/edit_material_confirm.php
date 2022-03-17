<?php
session_start();


// (処理が完了したら画面に遷移する前に add_product_material.php)
// 材料の親カテゴリーを削除する
$_SESSION['parent_category_id'] = '';

// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');


$material_id = $_GET['id'];

try {


    if (empty($_GET['id'])) {

        header("Location: ./confirm.php");
        exit;
    } else {

        // 送信データを受け取る レシピId
        $id = $_GET["recipe_id"];

        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');

        //データに接続するための文字列
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $sql = "SELECT * FROM my_recipes WHERE id=" . $id;

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $record = $result->fetch(PDO::FETCH_ASSOC);



        //  親材料カテゴリににない、子供カテゴリーだけが表示できる。
        $sql = 'SELECT materials.id, materials.material_name, materials.amount, 
            materials.parent_category_id,
            material_parent_categories.materials_parent_category_name, material_categories.material_category_name,
            material_cat_products.material_category_id
            FROM materials
            left outer JOIN material_parent_categories ON materials.parent_category_id = 
            material_parent_categories.id   
            left outer JOIN material_categories ON materials.parent_category_id =
            material_categories.id
            left outer JOIN material_cat_products ON material_cat_products.material_category_id =
            material_parent_categories.id
            left outer JOIN product_lists ON product_lists.id =
            material_cat_products.product_id 
            WHERE materials.recipe_id = ' . $id . ' AND materials.id = ' . $material_id . '
            ';

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $list = $result->fetchAll(PDO::FETCH_ASSOC);

        foreach ($list as $v) {

            $material_name = $v['material_name'];
            $amount = $v['amount'];
        }



        // ８は、「ユーザー定義」材料カテゴリーなので表示から省く。「id=8の”ユーザー”定義」は、ユーザー材料カテゴリー追加画面でのみ使う。
        $sql = "SELECT id, materials_parent_category_name
            FROM material_parent_categories
            WHERE id <= 8
            ";


        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $count = $result->fetchAll(PDO::FETCH_ASSOC);


        // 「*インドのおかず」などのユーザー定義カテゴリーをmaerial_categoriesテーブルから引っ張り出す・表示させるためのFETCH
        // ユーザーが作ったレシピIDに付随した子供材料カテゴリーIDだけを表示する
        $sql = "SELECT material_categories.id, material_categories.material_category_name
            FROM material_categories
            WHERE material_categories.users_id = '" . $_SESSION['member'] . "'
            AND material_categories.recipe_id = '" . $id . "'
            ";


        $stmt = $dbh->prepare($sql);


        $stmt->execute();

        $result = $dbh->query($sql);

        $children = $result->fetchAll(PDO::FETCH_ASSOC);
    }


    // セッションに記録された時間が、今の時間よりも大きい、つまりログイン時間から
    // 1時間以上たっていた場合,という意味
    if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
        // （1時間が経過していたら、）ログアウトし、ログイン画面に遷移する
        $_SESSION['time'] = time();
        // 現在の時刻で上書きします。こうすることで、何か行動したことで上書きすることで
        // 最後の時刻から１時間を記録することができるようになる。 
    } elseif ($_SESSION['member'] = '') {
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


?>

<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>材料の編集</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <!-- 全体 -->
    <link rel="stylesheet" href="css/stylesheet8.css">
    <!-- アコーディオン  -->
    <link rel="stylesheet" href="css/stylesheet_a.css">
    <!-- 一口レシピ用javascript用 CSS -->
    <link rel="stylesheet" href="css/stylesheet.css">

    <style>
        input[id="button"] {
            float: right;
            margin-right: 16px;

        }
    </style>
</head>


<body>


    <div class='div_p'>
        <dt class="wf-sawarabimincho"><span style="font-size:18px;color:green">
                <span style="font-size:21px"><?php echo $record['recipe_name']; ?></span><span style="font-size:16px;color:#000000">の材料</span></dt>


        <!-- ログアウト -->
        <div class="div_logout"><input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../../logout/process.php'">

        </div>
        <!-- マイページ -->
        <div class="div_logout"><input type="button" value='マイページ' class="logout_btn" onclick="location.href='../../login/process.php'">

        </div>
        <!-- みんなのレシピ -->
        <div class="div_logout">
            <input type="button" value='みんなのレシピ' class="logout_btn" onclick="location.href='../../top/confirm.php'">

        </div>


        <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">

    </div>

    <td>

        <!-- フォーム⓸ [商品名で検索🔍] -->
        <div class="listserch">
            <form action="../../product/material/serch_material5_multiple.php" method="GET" onclick="open_preview();" width=600px heiht=500px>
                <?php
                $_SESSION['recipe_id'] = $_GET['recipe_id'];
                $_SESSION['id'] = $_GET['id'];
                ?>

                <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">
                <input type="hidden" name="id" value="<?php echo $_SESSION['id'] ?>">
                <!-- materials.id -->

                <input type="hidden" name="parent_category_id" id="material_category" value='<?php echo $parent_category_id ?>'>


                <!-- ボタン -->
                <div class="product_select">
    <td>

    </td>
    <td>
        <input type="text" name="product_name" style="border:none" size='11' name="product_name" placeholder='商品名:クミン' maxlength="255">
        <input id="material_category" type="submit" value="商品から選ぶ" name="send">
    </td>

    </div>
    <!-- id="button"  -->

    </form>
    </div>
    <!-- formおわり -->
    </td>
    </tr>
    </tbody>
    </table>



    <!-- 親カテゴリー ✅-->
    <div class="div_edit_r">
        <dt>※材料カテゴリーを外したい場合は、空欄のラジオボタンを選択してください。</dt>

        <!-- アコーディオンバーはじまり -->
        <!-- 親の材料カテゴリを選ぶ✅ボックス -->


        <div class="div_w">


            <input id="acd-check7" class="acd-check" type="checkbox">
            <label class="acd-label" for="acd-check7">
                <!-- 親・材料カテゴリー -->
                材料カテゴリー
            </label>
            <div class="acd-content">


                <!-- フォーム/親の材料カテゴリーと材料をインサートする -->
                <form action="update_materials.php" method="GET">
                    <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">
                    <input type="hidden" name="material_id" value="<?php echo $material_id ?>">
                    <!-- ↑材料id の隠し送信 どの材料か？判別 -->

                    <!-- 親の材料カテゴリー階層を、カテゴリー・テーブルへインサートする -->
                    <dt>●材料カテゴリー</dt>

                    <?php foreach ($count as $v) : ?>
                        <table class="table">
                            <tr>
                                <td><input id="form1" type="radio" name="id[]" value='<?php echo $v['id'] ?>'></td>
                                <td><?php echo $v['materials_parent_category_name'] ?></td>
                            </tr>
                        </table>
                    <?php endforeach ?>

                    <!-- 子供の材料カテゴリー -->
                    <?php foreach ($children as $key => $v) : ?>
                        <table class="table">
                            <tr>
                                <td><input type="radio" name="id[]" value='<?php echo $v['id'] ?>'></td>
                                <td><?php echo $v['material_category_name'] ?></td>
                            </tr>
                        </table>
                    <?php endforeach ?>

                    <!-- 親カテゴリー選択リセットボタン -->
                    <div class="btn">
                        <input type="reset" value="リセット" class="btn-border">
                        <br>


                    </div>
            </div>

        </div>

        <!-- 子供材料カテゴリー作成おわり -->
        <div class="div_w_under">






            <table width="700px">
                <thead>
                    <tr>
                        <th>
                            <dt class="wf-sawarabimincho">
                        </th>
                        <th>
                            <dt class="wf-sawarabimincho">
                        </th>
                        <th width="45px">
                            <dt class="wf-sawarabimincho"><span style="color:green;font-size:16px"><?php echo $material_name; ?></span>
                        </th>
                        <th>
                            <dt class="wf-sawarabimincho"><span style="color:green;font-size:16px"><?php echo $amount; ?>
                        </th>
                        <th>


                        </th>
                    </tr>
                </thead>

                <!-- 材料入力フォーム -->
                <tbody>
                    <tr>
                        <td>

                        </td>

                        <!-- 隠し送信 Hidden レシピIDデータベースからFETCH()した   -->

                        <td>
                            <dt class="wf-sawarabimincho">
                        </td>

                        <!-- 入力フォーム値  材料名 -->

                        <td>

                            <input type="text" size='11' name="material_name" placeholder='牛乳' maxength="255">
                        </td>

                        <!-- 入力フォーム値  分量 -->
                        <td width="20px"><input type="text" size='8' name="amount" placeholder='1ℓ' maxlength="255"></td>

                        <td>
                            <!-- 追加ボタン -->
                            <input type="submit" value="Update" id="mySubmit" class="material_add_btn">

                        </td>
        </div><br>



        <!-- 編集おわり -->
        </tbody>
        </table>
        </form>


    </div>




</body>

</html>