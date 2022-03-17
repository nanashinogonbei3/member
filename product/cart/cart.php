<?php session_start();


try {


    if (empty($_SESSION['id'])) {
        header("Location: ./product_introduction.php");
    } else {

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
    }
} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}


if (!empty($_POST['add_cart'])) {
    if ($_POST['num'] === '') {
        $error['num'] = 'blank';
    }




    if (empty($error)) {

        $_SESSION['add'] = $_POST;
        header('Location: ./cart_add.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="jp">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>shop cart</title>
    <!-- 全体CSS -->
    <link rel="stylesheet" href="css/stylesheet8.css">

</head>
<meta charset="utf-8">



<?php
$product_name = '商品名';
$price = '価格';
$num = '注文数';
?>

<?php
if (!empty($_POST["submit"])) {

    header("Location: ../product_introduction_no_login.php?id=" . $_SESSION['id']);
    exit;
}
?>

<div class='div_1'>
    <table>
        <thead>
            <tr>
                <th>商品ID</th>
                <th></th>
                <th><?= $product_name ?></th>
                <th><?= $price ?></th>
                <th><?= $num ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>

            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= $_SESSION['id'] ?>">
                <input type="hidden" name="client_id" value=" ">

                <td><?= $list['id'] ?></td>
                <td>
                    <!-- 買い物カゴの商品画像 -->
                    <img id="img3" src="../../product/images/<?php echo $list['img'] ?>">
                    <input type="hidden" name="img" value="<?= $list['img'] ?>">
                </td>

                <td>
                    <!-- 商品名 -->
                    <?= $list['product_name'] ?>
                    <input type="hidden" name="product_name" value="<?= $list['product_name'] ?>">
                </td>

                <td>
                    <!-- 価格 -->
                    <?= $list['price'] ?>
                    <input type="hidden" name="price" value="<?= $list['price'] ?>">円
                </td>

                <td>
                    <!-- 個数 -->
                    <input type="text" class="input_count" name="num" style="width:2rem;">&nbsp;個
                    <?php if (isset($_POST['add_cart'])) : ?>
                        <?php if (!empty($error['num'])) : ?>
                            <p class="error">* 数量を入力してください</p>
                        <?php endif ?>
                    <?php endif ?>
                </td>

                <td>
                    <!-- カートに入れる -->
                    <input type="submit" name="add_cart" value='カートに入れる' class="shop-order">

                </td>

            </form>
            </tr>

        </tbody>
    </table>
    <p><a href="./cart_show.php" target="_self" style="text-decoration:none;">カートを見る</a></p>

    <!-- 戻る -->
    <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">



</div>



</body>

</html>