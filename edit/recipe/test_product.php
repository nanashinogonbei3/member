<?php
session_start();

require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');




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
        product_lists.amount as product_amount, materials.amount, 
        product_lists.product_name,
        makers.names
        FROM product_lists
        JOIN materials ON product_lists.id = materials.product_id
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
    <title>レシピ・ノート</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <!-- 全体 -->
    <link rel="stylesheet" href="./css/css/style_recipe.css">

    <!-- カルーセルのCSS/javascriptjQuery/ -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/my_script.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <!-- 一口レシピ用javascript用 CSS -->
    <link rel="stylesheet" href="css/stylesheet.css">



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

                    <?php foreach ($product as $v) : ?>

                        <ul class="column">
                            <!-- リンク先 商品詳細ページ -->
                            <li><a href="../../product/product_introduction.php?id=<?php echo $v['id'] ?>">
                                    <img id="img" src="../../product/images/<?php echo $v['img'] ?>" alt=""></a></li>
                            <!-- 画像おわり  -->
                            <li>
                                <?php echo $v['product_name'] ?><br>
                            </li>
                            <li>
                                <!-- ボタン（カルーセル内’商品詳細’） -->
                                <div class="btn_carousel">
                                    <button id="carousel_btn" type=“button” class="order" onclick="
                    location.href='../../product/product_introduction.php?id=<?php echo $v['id'] ?>'">商品の詳細</button>
                                </div>
                            </li>
                        </ul>

                    <?php endforeach ?>


                </div>
                <!-- DIV carouselInnerおわり -->

            </div>
            <!-- DIV carouselおわり -->
        </div>
        <!-- DIV containerおわり -->
        <!-- カルーセルおわり -->
    <?php } else {
        //    なんにも<表示>しないデス。
    } ?>
<!-- End hidari -->
    </div>
    

    <!-- 一口レシピを表示/非表示をコントロールするjavaScript -->
    <script src="main.js"></script>
    
</body>

</html>