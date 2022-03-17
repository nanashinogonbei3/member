<?php

session_start();

if (empty($_SESSION['message'])) {
    header("Location: ../../login/join.php?id=");
    exit;
}


try {


    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $sql = "SELECT 
    order_products.id, order_products.product_lists_id, order_products.product_name,
    order_products.price, order_products.num, order_products.payment_method, order_products.product_name,
    order_products.settlement_no,
    product_lists.img
    FROM order_products
    LEFT JOIN product_lists ON product_lists.id = order_products.product_lists_id 
    WHERE order_products.settlement_no = '" . $_SESSION['settlement_no'] . "'
    AND order_products.members_id = '" . $_SESSION['member'] . "'
    ";


    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $list = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ショッピング</title>

    <!-- 全体CSS -->
    <link rel="stylesheet" href="css/stylesheet5.css">

</head>

<body>


    <div class="div_1">

        <?php if (!empty($list)) : ?>
            <h1>こちらの商品のご注文を頂きました。</h1>
            <!-- テーブル -->
            <table>
                <thead>
                    <tr>
                        <th>注文番号</th>
                        <th></th>
                        <th>商品ID</th>
                        <th>商品名</th>
                        <th>価格</th>
                        <th>注文数</th>
                        <th>小計</th>
                        <th>お支払方法</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <?php
                        $total = 0;
                        foreach ($list as $key => $v) : ?>
                            <!-- 商品ID(商品コード) -->
                            <td><?= $v['settlement_no'] ?></td>

                            <td><img id="img3" src="../../product/images/<?= $v['img'] ?>"></td>
                            <td><?= $v["product_lists_id"] ?></td>
                            <td>
                                <?= $v["product_name"] ?>
                            </td>
                            <td>
                                <?= $v['price'] ?>円
                            </td>
                            <td>
                                <?= $v['num'] ?>個
                            </td>
                            <td><?= ($v['price']) * ($v['num']) ?>円</td>
                            <td><?= $v['payment_method'] ?></td>
                            <?php
                            // $total = $total + ($v['price']*$v['num']);
                            // 自己代入 $x = $x + 1 => $x += 1 => 0 + 1 = 1 ,$x = 1, $x = $x + 1 => 1+1 = 2
                            $total += ($v['price'] * $v['num']);
                            ?>
                    </tr>

                    </form>

                <?php endforeach ?>

                <!-- 合計金額 -->
                <h2>お支払金額
                    <?php
                    echo $total;
                    ?>
                    円</h2>


                </tbody>
                <?php



                ?>
            </table>
        <?php endif ?>
        <!-- テーブルおわり -->

        <!-- もし’お買い上げありがとうございました。'メッセージがあれば、表示する -->


        <h2><?php echo $_SESSION['message']; ?></h2>

    </div>

    <div class="inlineBlock ">

        <div class="div_logout">
            <!-- to cart -->
            <p><a href="./cart_show.php" target="_self" style="text-decoration:none;">カートを見る</a></p>
            <!-- item_lists -->
            <input type="button" value='商品一覧' class="logout_btn" onclick="location.href='../product_lists.php'">
            <!-- to top -->
            <input type="button" value='トップ' class="logout_btn" onclick="location.href='../../top/confirm.php'">
        </div>
    <!-- DIV inlineBlockおわり -->
    </div>


</body>

</html>