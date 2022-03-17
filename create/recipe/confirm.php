<?php
session_start();

// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');

// 送信データを受け取る /誰のレシピか？
// $_SESSION['member'];

try {

    if (empty($_GET['id'])) {
        // index.phpから送った、my_recipeテーブルのid

        header("Location: index.php");
    } else {
        // 送信データを受け取る レシピId
        $id = $_GET["id"];

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

        $is_released = $record['is_released'];
        $id = $record["id"];
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



        $sql2 = 'SELECT id,descriptions,p_img,p_recipe_id 
    FROM procedures WHERE p_recipe_id=' . $id . ' ';
        $sql2 .= 'ORDER BY created_date ASC';

        $stmt2 = $dbh->prepare($sql2);

        $stmt2->execute();

        $result2 = $dbh->query($sql2);

        $report = $result2->fetchAll(PDO::FETCH_ASSOC);


        //membersテーブルを読み込む
        // ログインのメンバー（index.phpから受信した$_GET['id']の代入変数$members_id）と紐づける
        $sql3 = "SELECT * FROM members WHERE id= '" . $_SESSION['member'] . "' ";
        

        $stmt3 = $dbh->prepare($sql3);

        $stmt3->execute();

        $result3 = $dbh->query($sql3);

        $group = $result3->fetchAll(PDO::FETCH_ASSOC);



        foreach ($group as $m) {
        }
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
    <title>レシピを公開する</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">

</head>




<body>

  

    <div class='div_p'>
        <p class="title_font">レシピの公開</p>

        <!-- みんなのレシピ -->
        <div class="div_login "><input type="button" value='みんなのレシピ' class="logout_btn" onclick="location.href='../../top/confirm.php'">
      
        </div>

    </div>

    <div class='inline_block_2'>


        <div class="comprehensive">


            <div class="block1">
                <div class="btn_migi">
                    <div class="btn_migi2">


                    <!-- レシピの公開・非公開をフォーム送信する -->
                    <form action="./update_r.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $id ?>">


                            <?php if ($is_released == 0) { ?>

                                <td><input type="radio" name="is_released" value="0" <?= $is_released == 0 ?> checked="checked"></td>
                                <td class="menu">未公開</td>
                                <td><input type="radio" name="is_released" value="1" <?= $is_released ?>></td>
                                <td class="menu">公開</td>

                            <?php } elseif ($is_released == 1) { ?>

                                <td><input type="radio" name="is_released" value="0" <?= $is_released == 0 ?>></td>
                                <td class="menu">未公開</td>
                                <td><input type="radio" name="is_released" value="1" <?= $is_released == 1 ?> checked="checked"></td>
                                <td class="menu">公開</td>

                            <?php } ?>


                            <!-- ”ボタンの、切り替え「レシピを公開」ボタン ⇔ 「非公開」ボタン を切り替えます" -->
                            <?php if ($record['is_released'] == 0) : ?>

                                <!-- 公開  ボタン （青色）-->
                                <input type="submit" name="pushed" value='レシピを公開' style=" background-color: #9ACFDD; width: 170px; height: 50px; color:gray; border:4px solid #CABAAB; ">

                            <?php else : ?>

                                <!-- 非公開  ボタン (緑色）-->
                                <input type="submit" name="pushed" value='非公開にする' style=" background-color: #EEEC00; width: 170px; height: 50px; color:gray; border:4px solid #CABAAB; ">

                            <?php endif ?>


                            <?php
                            // ボタンの後ろの、コメント表示
                            if ($record['is_released'] == 0) {

                                $no_published  =  'レシピ未公開';
                                echo $no_published;
                                

                            } else {

                                $published  =  'レシピ公開中';
                                echo $published;
                                
                            }
                            ?>

                    </div>


                    <!-- DIV トップの左側（画像） -->
                    <div class="div_hidari">
                        <!-- データベースからFETCH()した、レシピ名） -->
                        <p class="p_font_rarge">🔲<span>
                                <td><span style="color:green"><?php echo $recipe_name ?></span></td>


                                <!-- レシピを作った人 -->
                                <dt class="wf-sawarabimincho">レシピを作った人：<span style="color:green;font-size:135%;"><?php echo $m['nickname']; ?>さん</span></dt>


                                <div class="line"></div>
                                <!-- データベースからFETCH()した、  レシピID -->
                                <dt class="wf-sawarabimincho">レシピID:
                                    <span style="color:green"><?php echo $id ?></span>
                                </dt>



                                <!-- レシピを選びなおす ボタン -->
                                <input type="button" onclick=" 
                                alert('このページを離れていいですか？')
                                location.href='./index.php?id= action=rewrite'" value='レシピを選び直す' style=" background-color:#FFF587; width: 210px; height: 30px; color:gray; border:5px dashed #F2F0CE; ">


                                <div class="item_l">
                                    <!-- データベースからFETCH()した、完成画像 -->
                                    <span style="color:green">
                                        <p class="wf-sawarabimincho">完成画像</P>
                                        <img class="img" src="./images/<?php echo $complete_img
                                                                        ?>" width="auto" height="250px"></p>
                        <!-- データベースからFETCH()した、  作成日 -->
                        <dt class="wf-sawarabimincho">作成日：
                            <span style="color:green"><?php echo $created_date ?></span>
                        </dt>
                    </div>

                    <!-- データベースからFETCH()した、 ビデオ動画 -->

                    <div class="item_l">
                        <!-- 調理動画を別ウィンドウで再生させます。 -->
                        <!-- <img class="img" src="../../move/elefant.jpg" alt="象" width="50px" height="auto"> -->
                        <span style="color:green">
                            <p class="wf-sawarabimincho">調理動画</P>
                            <img class="img" src="./images/<?php echo $video ?>" width="250px" height="auto">
                    </div>
                <!-- DIV 左側おわり -->
                </div>
                


                <!-- DIV 右側はじまり -->
                <div class="div_migi">

                    <div class="div_w">

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

                     

                                </tr>
                            </tbody>
                        </table>

                        <!-- div_w -->
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
            <!-- div_w -->

            <!-- DIV 右側おわり -->
        
        </div>
        <!-- div class="comprehensive" おわり-->
    </div>
    


    <!-- ↓ ここから、Proceser 調理手順 -->
    <div class="inline_block_4">
        <p class="p_font_rarge">🔲調理手順</p>
        <dt>
            <!-- 材料テーブルのデータベースからFETCH()した  my_recipeテーブルのID -->
            <p><span style="color:green;font-size:13px">レシピID:<?php echo $id ?></span></p>
        </dt>




        <!-- 表示欄 -->

        <div class="parent">
            <!-- データの数だけ繰り返し -->
            <?php foreach ($report as $v) : ?>

                <div class="div_100p">
                    <div class="div_100">
                        <!-- 材料テーブルのデータベースからFETCH()した  調理手順のイメージ画像 -->
                        <img class="img" id="pimg" src="./pimg/<?php echo $v['p_img'] ?>"></p>

                    </div>
                    <div class="div_100">
                        <!-- 材料テーブルのデータベースからFETCH()した 調理説明  -->
                        <p><span style="color:green;font-size:13px">
                                <td><?php echo  $v['descriptions'] ?></td>
                            </span></p>

                    </div>
                </div>

            <?php endforeach ?>
        <!-- precent -->
        </div>
        


    </div>
    <!-- inline_block_4 -->
    </div>


</body>

</html>