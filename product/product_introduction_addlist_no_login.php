<?php
session_start();

require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');

// serch_recipe_multiple class="php"の検索結果をセッションに渡す。
$productUsedRecipeName = $_SESSION['product_used_recipeName'];

// どの商品アイテムか？
$id = $_GET['id'];

// 1ページの$list でFETCH ALL の表示数
define('max_view', 6);


try {

    $id = $_GET['id'];

    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

    $date = $dt->format('Y-m-d');

    //データに接続するための文字列
    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    // my_sqlのpassword
    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //  冒頭で作った変数の$id = $_GET['id'] 選んだ商品id をここで代入する
    $sql = "SELECT *
             FROM product_lists
            LEFT OUTER JOIN makers ON product_lists.maker_id = makers.id
            WHERE product_lists.id= '" . $id . "' ";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $list = $result->fetch(PDO::FETCH_ASSOC);

    if (!empty($productUsedRecipeName)) {
        // ページング
        // ここから、マイレシピのページングの処理
        $total_count = count($productUsedRecipeName);


        // ページ数= 全商品数/1ページの表示数
        $pages = ceil($total_count / max_view);
        // トータルページ数※ceilは小数点を切り上げる関数1.6⇒2
        // echo $pages;
        // exit;

        //現在いるページのページ番号を取得
        if (!isset($_GET['page_id'])) {
            $now = 1;
        } else {
            $now = $_GET['page_id'];
        }


        // ページネーションの1ページ目のsqlの処理・1ページ以外のsqlの処理
        //表示するページを取得するSQLを準備
        $select = $dbh->prepare("SELECT distinct *
              FROM materials
              INNER JOIN product_lists ON materials.product_id = product_lists.id
              INNER JOIN my_recipes ON materials.recipe_id = my_recipes.id
              WHERE product_id = '" . $id . "' ORDER BY my_recipes.id DESC LIMIT :start,:max ");

        if ($now == 1) {
            //1ページ目の処理
            $select->bindValue(":start", $now - 1, PDO::PARAM_INT);
            $select->bindValue(":max", max_view, PDO::PARAM_INT);
        } else {
            //1ページ目以外の処理
            $select->bindValue(":start", ($now - 1) * max_view, PDO::PARAM_INT);
            $select->bindValue(":max", max_view, PDO::PARAM_INT);
        }
        //実行し結果を取り出しておく
        $select->execute();
        $data = $select->fetchAll(PDO::FETCH_ASSOC);
        // ページングおわり
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
    <title>商品紹介ページ</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <!-- 全体 -->
    <link rel="stylesheet" href="css/stylesheet8.css">
    <!-- ページネーション -->
    <link rel="stylesheet" href="css/style_paging3.css">
    

</head>

<body>

    <div class='div_p'>
        <dt class="title_font">商品詳細ページ</dt>
        <!-- ボタン -->
        <!-- 商品の編集(管理者用) -->
        <?php if (!empty($_SESSION['member'])) : ?>
            <?php if ($_SESSION['member'] == 104) : ?>
                <div class="div_logout1">
                    <input type="button" value='商品の編集' class="logout_btn" onclick="location.href='./edit/confirm.php?id=<?php echo $id  ?>'">
                    
                </div>
            <?php endif ?>
        <?php endif ?>
        <!-- マイページ -->
        <div class="div_logout1">
            <input type="button" value='マイページ' class="logout_btn" onclick="location.href='../login/process.php'">
            
        </div>
        <!-- 商品一覧ボタン -->
        <div class="div_logout2">
            <input type="button" value="商品一覧" class="re-order" onclick="
                location.href='./product_lists.php'" value='商品一覧'>
        </div>

    </div>
    <!-- div_pおわり -->
    <div class="comprehensive">
        <div class='inline_block_6'>


            <div class="block2">
                <div class="item_0">
                    <!-- データベースからFETCH()した、商品名） -->
                    <dt class="wf-sawarabimincho">
                    <dt class="p_font_rarge"><span style="color:green"><?php echo $list['product_name'] ?></span></dt>
                    </dt>
                    <div>
                        <!-- フォーム 商品名からレシピを検索します。 -->
                        <form action="./serch_recipe_multiple.php" method="GET">
                            <input type="hidden" name="product" value="<?php echo $list['product_name'] ?>">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                    </div>
                    <!-- ボタン -->
                    <div class="div_re-order">
                        <input type="submit" class="re-order3" value="<?php echo $list['product_name'] ?>を使ったレシピです。">
                    </div>
                    </form>


                    <!-- データベースからFETCH()した、商品ID -->
                    <dt class="wf-sawarabimincho">商品ID：
                        <span style="color:green"><?php echo $list['id'] ?></span>
                    </dt>

                    <!-- データベースからFETCH()した、商品説明 -->
                    <dt class="wf-sawarabimincho">
                        <span style="color:green"><?php echo $list['describes'] ?></span>
                    </dt>

                </div>

            </div>
        </div>
        </form>
        <!-- フォームおわり -->


        <div class="item_6">
            <!-- 戻る -->
            <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">
            <!-- 戻るおわり -->
            <?php if (!empty($_SESSION['errMessage'])) { ?>
                <span style="color:gray">
                    <?php if (empty($data)) {
                        echo $list['product_name'] . 'が' . $_SESSION['errMessage'];
                    } ?>
                </span>
            <?php } ?>


            <!-- DIV 右側はじまり -->


            <div class="div_edit_r6">


                <div class="comprehensive">

                    <div class="shop ">
                        <!-- "商品を買い物に入れる"ボタン -->
                        <input type="button" class="re-order" value='買い物カゴに入れる' onclick="location.href='./cart/cart.php'" style="width: 180px;">
                    </div>
                    <div class="div_img">
                        <!-- データベースからFETCH()した、商品画像 -->
                        <img id="img3" src="./images/<?php echo $list['img'] ?>">
                    </div>


                </div>

            </div>
            <!-- edit_6おわり -->


            <!-- フォームおわり -->


            <!-- div class="comprehensive" おわり-->


        </div>
        <!-- div_precent -->

        <?php if (!empty($productUsedRecipeName)) { ?>
            <!-- 商品を使ったレシピがあれば -->
            <!-- ページングCSS -->
            <div class="div_w5">
                <div class="flex">
                    <ul class="bar">
                        <li>
                            <span style="color:#000000;font-size:15px;font-weight:lighter">
                                全レシピ数:<span style="color:green;font-size:24px"><?php echo $total_count ?></span>品</span>
                            &nbsp;&nbsp;

                            <?php if ($pages >= 2) { ?>
                                <!-- ページが２ページ以上あればページングを表示する -->
                                <?php
                                //ページネーションを表示    
                                if ($now > 1) {
                                    // 1ページより大きいなら、「前へ」表示
                                    echo '<a href="?id=', ($id), '&page_id=', ($now - 1), '">  
                 <img src="../icon_img/pre.png"
                 alt="前へ" width="25" height="25" border="0">
                </a>';
                                } else {
                                    //  1ページよりも小さい＝ページが無い、場合は矢印は表示させない。
                                }
                                ?>

                                <?php
                                // 1 2 3 と、表示するページの数を$pagesを今回は使わず、1 2 3 4 5 と、'5'つにする。
                                for ($n = 1; $n <= $pages; $n++) {
                                    if ($n == $now) {
                                        echo "<span style='padding: 5px;'>$now</span>";
                                    } else {
                                        echo "<a href='./product_introduction_addlist_no_login.php?id=$id&page_id=$n' 
                                style='padding:5px;
                              '
                                '>$n</a>";
                                        // hrefのリンクは、表示現在表示するリンクに修正して使うこと。
                                    }
                                }
                                ?>

                                <?php
                                if ($now < $pages) {
                                    // 表示ページが最終ページより小さいなら、「次へ」表示
                                    echo '<a href="?id=', ($id), '&page_id=', ($now + 1), '">  
                    <img src="../icon_img/next.png"
                        alt="次へ" width="25" height="25" border="0" margin-top:1px>
                    </a>';
                                }
                                ?>
                            <?php } elseif ($pages == 1) {
                                // ページ数が1なら、ページングは非表示。
                            } ?>
                        <?php } else {
                        // 検索結果が無ければ非表示
                    } ?>

                        </li>
                    </ul>


                </div>

            </div>



            <!-- ページネーション囲むDIVおわり -->

            <!-- 商品を使ったレシピ一覧表示欄 -->
            <div class="inline_block_7">
                <div class="parent">
                    <?php if (!empty($data)) { ?>
                        <?php foreach ($data as $v) : ?>
                            <div class="item_2">

                                <span style="color:green;font-size:24px">
                                    <dt class="wf-sawarabimincho">
                                        <!-- データベースからFETCH()した、レシピ名 -->
                                        ■<?php echo $v['recipe_name'] ?>
                                    </dt>
                                </span>
                                <!-- FETCH()した、商品を使用したレシピ画像 -->
                                <a href="../edit/recipe/release_recipe3.php?id=
        <?php echo $v['id'] ?>">
                                    <dt><img id="recipe" src="../create/recipe/images/<?php echo $v['complete_img'] ?>" alt=""></dt>
                                </a>
                                <?php echo $v['id'] ?>
                            </div>
                        <?php endforeach ?>
                    <?php } else {
                    } ?>

                </div>
            </div>
    </div>

    </div>
    <!-- inlin_block_7おわり -->
    </div>



    <!-- compresive -->
    </div>
    <!-- inline_block_6 -->
    </div>


    <script src="./js/movepage.js"></script>
</body>

</html>