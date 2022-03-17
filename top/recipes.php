<?php
session_start();

// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');



//送信データを受け取る レシピId
$id = $_GET['id'];

try {

    if (empty($id)) {

        header("Location: index.php");
    } else {


        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');

        //データに接続するための文字列
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM my_recipes WHERE id=" . $id;

        // 内部結合
        // SELECT * FROM my_recipes, materials WHERE my_recipes.id = materials.recipe_id;
        // 外部結合
        // 材料 が未登録の, レシピも表示が可能

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $record = $result->fetch(PDO::FETCH_ASSOC);



        $is_released = $record['is_released'];
        // 「公開済み判定カラム/=0;公開済み=1」に対して変数を作成し値を代入する


        $rcipeId = $record["id"];

        $recipe_name = $record["recipe_name"];
        $complete_img = $record["complete_img"];
        $cooking_time = $record["cooking_time"];
        $cost = $record["cost"];
        $how_many_servings = $record["how_many_servings"];
        $created_date = $record['created_date'];


        // recipesデータベースの切断
        $dbh = null;

        // recipesデータベースに再度接続

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $sql = 'SELECT * FROM materials WHERE recipe_id=' . $id . ' ';
        $sql .= 'ORDER BY created_date ASC';

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $list = $result->fetchAll(PDO::FETCH_ASSOC);


        // 調理手順テーブルの開始
        // データに接続するための文字列
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');


        $sql2 = 'SELECT id,descriptions,p_img,p_recipe_id FROM procedures WHERE p_recipe_id=' . $id . ' 
            ORDER BY created_date DESC';



        $stmt2 = $dbh->prepare($sql2);


        $stmt2->execute();


        $result2 = $dbh->query($sql2);


        $report = $result2->fetchAll(PDO::FETCH_ASSOC);

        foreach ($report as $v) {
            // echo $v['p_recipe_id'];
            $p_recipeId = $v['p_recipe_id'];
        }
        // exit;




        //membersテーブルを読み込む

        $sql3 = "SELECT my_recipes.id, members.nickname FROM my_recipes JOIN members ON my_recipes.members_id = members.id 
            WHERE my_recipes.id=" . $id;
        // ログインのメンバー（index.phpから受信した$_GET['id']の代入変数$members_id）と紐づける

        $stmt3 = $dbh->prepare($sql3);


        $stmt3->execute();


        $result3 = $dbh->query($sql3);


        $group = $result3->fetch(PDO::FETCH_ASSOC);

        $nickname = $group['nickname'];
    }
} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}


?>

<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>レシピの閲覧</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />

    <!-- ↑全体 -->
    <link rel="stylesheet" href="css/stylesheet8.css">


</head>


<!-- ボディ メイン -->

<body>

    <!-- <div class='inline_block_1'> -->

    <div class='div_p'>
        <p class="title_font">レシピの紹介</p>

        <!-- みんなのレシピ -->
        <div class="div_login ">
            <input type="button" value='みんなのレシピ' class="logout_btn" onclick="location.href='./confirm.php'">
        </div>




        <!-- アコーディオン検索 -->
        <div class="div_logout"><input type="button" value='レシピ検索' class="logout_btn" onclick="location.href='./acodion.php' ">

        </div>
        <!-- div_pおわり -->
    </div>


    <div class='inline_block_2'>


        <div class="comprehensive">


            <div class="block1">
                <div class="btn_migi">
                    <div class="btn_migi2">


                        <!-- フォーム -->
                        <form action="./update_r.php" method="post" enctype="multipart/form-data">



                    </div>





                    <!-- DIV トップの左側（画像） -->
                    <div class="div_hidari">
                        <br>
                        <!-- データベースからFETCH()した、レシピ名） -->
                        <dt class="p_font_rarge">🔲<span>
                                <td><span style="color:green"><?php echo $recipe_name ?></span></td>


                                <!-- レシピを作った人 -->
                        <dt class="wf-sawarabimincho">レシピを作った人：<span style="color:green;font-weight:bold;font-size: 150%;"><?php echo $nickname ?></span>さん</dt>


                        <div class="line"></div>
                        <!-- データベースからFETCH()した、  レシピID -->
                        <dt class="wf-sawarabimincho">レシピID：
                            <span style="color:green"><?php echo $id ?></span>



                            <!-- レシピを編集する ボタン -->
                            <input type="button" onclick=" 
                                                alert('このページを離れていいですか？')
                                                location.href='../edit/recipe/confirm.php?id=<?php echo $id ?>'" value='レシピを編集する' style=" background-color:#FFF587; width: 210px; height: 30px; color:gray; border:5px dashed #F2F0CE; ">
                            <!-- レシピを戻る ボタン -->
                            <input type="button" onclick=" 
                                               
                                                location.href='../top/login_recipe.php?id=<?php echo $id ?>'" value='戻る' style=" background-color:#FFF587; width: 70px; height: 30px; color:gray; border:5px dashed #F2F0CE; ">
                        </dt>


                        <div class="item_l">
                            <!-- データベースからFETCH()した、完成画像 -->
                            <span style="color:green">
                                <dt class="wf-sawarabimincho"></dt>
                                <img id="img" src="../create/recipe/images/<?php echo $complete_img
                                                                            ?>" alt="この画像は未登録です。" width="450px"></p>
                                <!-- データベースからFETCH()した、  作成日 -->
                                <dt class="wf-sawarabimincho">作成日:
                                    <span style="color:green"><?php echo $created_date ?></span>
                                </dt>
                        </div>

                        <!-- DIV 左側おわり -->
                    </div>






                    <!-- DIV 右側はじまり -->
                    <div class="div_migi">

                        <div class="div_w">
                            <!-- 入力フォーム 材料入力 -->
                            <form action="add_m.php" method="POST">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>
                                                <dt class="wf-sawarabimincho">調理時間
                                            </th>
                                            <th>
                                                <dt class="wf-sawarabimincho">材料費
                                            </th>
                                            <th>
                                                <dt class="wf-sawarabimincho">何人分
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <!-- データベースからFETCH()した、 調理時間 -->
                                            <td><span style="color:green"><?php echo $cooking_time ?></span>分</dt>
                                            </td>

                                            <!-- データベースからFETCH()した、 材料費 -->
                                            <td><span style="color:green"><?php echo $cost ?></span>円</dt>
                                            </td>

                                            <!-- データベースからFETCH()した、 何人分 -->
                                            <td><span style="color:green"><?php echo $how_many_servings ?></span>人分</dt>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="clear_both">

                                </div>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>
                                                <dt class="wf-sawarabimincho">レシピID
                                            </th>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <th>
                                                <dt class="wf-sawarabimincho">材料名
                                            </th>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </td>
                                            <th>
                                                <dt class="wf-sawarabimincho">分量
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 材料入力フォーム -->
                                        <tr>
                                            <!-- 隠し送信 Hidden レシピIDデータベースからFETCH()した   -->
                                            <input type="hidden" name="recipe_id" value="<?php echo $id ?>">

                                            <!-- 材料テーブルのデータベースからFETCH()した  レシピID -->
                                            <td></span></td>

                                            <!-- 入力フォーム値 材料名 -->
                                            <td></td>

                                            <!-- 入力フォーム値  分量 -->
                                            <td></td>

                                            <td>
                                                <!-- 追加ボタン -->

                                            </td>

                                        </tr>
                                    </tbody>
                                </table>


                        </div>

                        <div class="div_w_under">

                            <table>
                                <thead>
                                    <tr>
                                        <th>
                                            <dt class="wf-sawarabimincho">
                                        </th>
                                        <th>
                                            <dt class="wf-sawarabimincho">
                                        </th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <tr>
                                        <!-- データの数だけ繰り返し -->
                                        <?php foreach ($list as $v) : ?>
                                <tbody>
                                    <tr>
                                        <!-- 材料テーブルのデータベースからFETCH()した  レシピID -->
                                        <td><span style="color:green;font-size:13px"><?php echo $v['recipe_id'] ?></span></td>
                                        <!-- 材料テーブルのデータベースからFETCH()した 　材料名 -->
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td><span style="color:green;font-size:13px"><?php echo  $v['material_name'] ?></span></td>

                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <!-- 材料テーブルのデータベースからFETCH()した  分量 -->
                                        <td><span style="color:green;font-size:13px"><?php echo $v['amount'] ?></span></td>


                        </div>
                        </tr>
                        </tbody>
                    <?php endforeach ?>
                    </tr>
                    </tbody>

                    </table>
                    </form>
                    </div>
                </div>


                <!-- DIV 右側おわり -->
            </div>
            <!-- div class="comprehensive" おわり-->
        </div>








        <!-- フォーム送信 -->

        <form action="add_p.php" method="post" enctype="multipart/form-data">


            <div class="inline_block_4">
                <p class="p_font_rarge">🔲調理手順</p>
                <dt>
                    <!-- 材料テーブルのデータベースからFETCH()した  my_recipeテーブルのID -->
                    <?php
                    if (!empty($p_recipeID)) { ?>
                        <span style="color:green;font-size:13px">レシピID:<?php echo $p_recipeId ?></span>
                </dt>
            <?php } else {

                        $error = 'このレシピの調理手順の登録はありません。';
                        echo $error;
                    } ?>
            <!-- 表示欄 -->

            <div class="parent">
                <!-- データの数だけ繰り返し -->
                <?php foreach ($report as $v) : ?>


                    <!-- ↓ ここから、Proceser 調理手順 -->



                    <div class="div_100p">
                        <div class="div_100">
                            <!-- 材料テーブルのデータベースからFETCH()した  調理手順のイメージ画像 -->
                            <img class="img" id="pimg" src="../create/recipe/pimg/<?php echo $v['p_img'] ?>"></p>

                        </div>
                        <div class="div_100">
                            <!-- 材料テーブルのデータベースからFETCH()した 調理説明  -->
                            <p><span style="color:green;font-size:13px">
                                    <td><?php echo  $v['descriptions'] ?></td>
                                </span></p>

                        </div>
                    </div>

                <?php endforeach ?>
        </form>
    </div>
    <!-- precent -->
    </form>

    </div>
    <!-- inline_block_4 -->
    </div>


</body>

</html>