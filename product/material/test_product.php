<?php
session_start();

require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');


// /product/material/serch_material3_multiple.phpの商品の検索結果（材料テーブルへINSERTボタン付き）
if (!empty($_SESSION['product'])) {
    $productList = $_SESSION['product'];
}

try {


    // 送信データを受け取る レシピId


    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $date = $dt->format('Y-m-d');

    //データに接続するための文字列
    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // リレーションしたテーブルに同名カラムがあった時の対処法
    // SELECT {カラム名} AS {変更するカラム名} FROM {テーブル名};
    // カルーセルで表示するレシピで使った商品をFETCHする。
    $sql = 'SELECT product_lists.id, product_lists.img, product_lists.price,
        product_lists.amount as product_amount, 
        product_lists.product_name, makers.names
        FROM product_lists
        INNER JOIN makers ON product_lists.maker_id = makers.id 
        WHERE
            product_lists.is_released = 1
        ';

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $product = $result->fetchAll(PDO::FETCH_ASSOC);
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>材料えらび</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />

    <link rel="stylesheet" href="css/style_recipe.css">
    <!-- カルーセルのCSS/javascriptjQuery/ -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/my_script.js"></script>
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        #carousel {
            /* 画像 古い紙 */
            background-image: url(../../top/css_img/paper.png);
        }
    </style>


</head>
</head>

<body>
    <!-- --------------------------------------------------------- -->
    <!-- カルーセル -->

    <!-- https://www.php.net/manual/ja/function.is-countable.php -->
    <?php

    is_countable($product);
    //配列の数が3以上あるか判定するためにカウントする。
    $cnt = count($product);


    ?>
    <!-- もし配列が3セット以上あれば、カルーセルを表示する。
つまり、材料のなかに商品の登録が3つ以上あれば、カルーセル表示させる。 -->
    <?php if (!empty($product) && $cnt > 3) { ?>

        <div class="div_carousel">

            <div id="carousel">
                <p id="carouselPrev"><img src="./images/prev3.png" alt="前へ" width="50px"></p>
                <p id="carouselNext"><img src="./images/next3.png" alt="次へ" width="50px"></p>
                <dt>&nbsp;&nbsp;</dt>

                <div id="carouselInner">

                    <?php foreach ($productList as $key => $v) : ?>

                        <ul class="column">
                            <!-- リンク先 商品詳細ページ -->
                            <a href="../../product/product_introduction.php?id=<?php echo $v['id'] ?>">
                                <img id="img" src="../../product/images/<?php echo $v['img'] ?>" alt=""></a>
                            <!-- 画像おわり  -->

                            <div><?php echo $v['product_name'] ?></div>


                            <!-- ボタン（カルーセル内’商品詳細’） -->
                            <br>
                            <div class="btn_carousel">
                                <input type="submit" id="button3" value="商品詳細" type=“button” class="order" onclick="
                location.href='../../product/product_introduction.php?id=<?php echo $v['id'] ?>'">

                            </div>
                            <div class="btn_carousel">
                                <!-- フォーム⓵ [材料に追加] -->
                                <form action="add_product_material.php" method="GET">
                                    <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">
                                    <input type="hidden" name="material_name" value="<?php echo $v['product_name'] ?>">
                                    <input type="hidden" name="product_id" value="<?php echo $v['id'] ?>">
                                    <!-- 材料テーブルの、product_idカラムにINSERT↑ -->
                                    <input type="hidden" name="parent_category_id" value="<?php echo $parent_category_id ?>">
                                    <!-- edit/recipe/confirm.php から送信した$_GET['parent_category_id']を
                        $_SESSION['parent_category_id']に渡したものを受取り、add_product_material.phpへ送る。-->


                                    <!-- 入力フォーム値  分量 -->
                                    <div class="inline_block">
                                        <div class="side_left">
                                            <input type="text" style="border:none" size='6' name="amount" placeholder='小さじ1' maxlength="255"></td>
                                        </div>
                                        <div class="side_right">
                                            <input type="submit" id="button" class="update" value="材料に追加" name="send" style="width: 111px;
                                  ">
                                        </div>
                                    </div>


                                    <!-- 戻るボタン -->

                                    <div class="back">
                                        <!-- 戻る -->
                                        <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">
                                        <!-- 戻るおわり -->
                                    </div>

                                </form>
                            </div>

                        </ul>


                    <?php endforeach ?>

                <!-- DIV carouselInnerおわり -->        
                </div>
                
            <!-- DIV carouselおわり -->           
            </div>
        <!-- DIV containerおわり -->
        <!-- カルーセルおわり -->    
        </div>
        
    <?php } else {
        //    なんにも<表示>しない
    } ?>

    </div>
    <!-- End hidari -->

    <!-- ---右----------------------------------------------- -->
    <!-- 一口レシピを表示/非表示をコントロールするjavaScript -->
    <script src="main.js"></script>

</body>

</html>