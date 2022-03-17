<?php

session_start();



try {


    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //  冒頭の$_SESSION['id'] を代入した $id （商品id） をここで代入する
    $sql = "SELECT *
             FROM product_lists
            LEFT OUTER JOIN makers ON product_lists.maker_id = makers.id
            WHERE product_lists.id= '" . $_SESSION['id'] . "' ";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $list = $result->fetch(PDO::FETCH_ASSOC);

    $product_img = $list['img'];
    // exit;


} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}



?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ショッピングカート</title>

    <!-- 全体CSS -->
    <link rel="stylesheet" href="css/stylesheet5.css">

</head>

<body>



    <?php if (isset($_SESSION["cart"]) && count($_SESSION["cart"]) > 0) :  ?>

        <!-- <div class="div_1"> -->
        <table>
            <thead>
                <tr>
                    <th width="100px">商品ID</th>
                    <th width="300px"></th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>小計</th>
                </tr>
            </thead>


            <?php
            $sum = 0;

            ?>


            <tbody>
                <tr>

                    <!-- 購入手続きへボタン -->
                    <?php if (!empty($_SESSION['member'])) { ?>

                        <form method="GET" action="./delivery_registration_single_item.php?id">

                            <input type="hidden" name="client_id" value="<?php echo $_SESSION['member'] ?>">

                            <!-- カートの商品を表示 -->
                            <?php foreach ($_SESSION["cart"] as $key => $value) :  ?>
                                <input type="hidden" name="delete_id" value="<?php $value['id'] ?>">

                                <!-- 商品ID -->
                                <td><?= $value['id'] ?></td>

                                <td><img id="img3" src="../../product/images/<?= $value['img'] ?>"></td>
                                <!-- 商品名 -->
                                <td>
                                    <?= $value['product_name'] ?>
                                    <input type="hidden" name="product_name[]" value="<?= $value['product_name'] ?>">
                                </td>
                                <!-- 価格 -->
                                <td width="10%">
                                    <?= $value['price'] ?>円
                                    <input type="hidden" name="price[]" value="<?= $value['price'] ?>">
                                </td>
                                <!-- 注文個数 -->
                                <td>
                                    <?= $value['num'] ?>個
                                    <input type="hidden" name="num[]" value="<?= $value['num'] ?>">
                                </td>
                                <!-- 小計 -->
                                <td>
                                    <?php

                                    $sub_total = (int)$value['num'] * (int)$value['price'];
                                    echo $sub_total;
                                    ?>円
                                </td>

                                <!-- 合計金額 -->
                                <?php
                                //合計金額を出す。
                                $sum += $value['price'] * $value['num'];
                                ?>
                </tr>
                <input type="hidden" name="sub_total" value="<?php echo $sum ?>">

            <?php endforeach ?>
            <input type="submit" name="buy" value="購入手続きへ" class="shop-order" />
            </form>
            </tbody>
        </table>

        <p class='p'><?php echo "合計金額￥:" . $sum . "円" ?></p>



    <?php } elseif (empty($_SESSION['member'])) { ?>

        <!-- 未ログインの場合は、会員登録へ -->
        <input type="button" name="sent" value="ご購入手続きへ" class="shop-order" onclick="
            location.href='../../login/join.php'">
        <input type="hidden" name="order_id" value="<?php echo $value['id'] ?>">
        <input type="hidden" name="client_id" value="<?php echo $_SESSION['member'] ?>">
        <input type="hidden" name="num" value="<?php echo $value['num'] ?>">
    <?php } ?>


    <p><a href="cart_del_all.php" action="cart_del.php" target="_self" style="text-decoration:none;">カートを空にする</a></p>

    <!-- １個前へ戻る -->
    <div class="inlineBlock">
        <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">
        <!-- １個前へ戻るおわり -->

        <input type="button" value="商品一覧" class="re-order" onclick="
        location.href='../product_lists.php'">
    </div>

<?php endif ?>

</body>

</html>