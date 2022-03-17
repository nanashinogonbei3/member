<?php

session_start();


try {


    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // メンバーテーブルから郵便番号情報を取り出す。
    $sql = "SELECT post_number FROM members WHERE id= '" . $_SESSION['member'] . "' ";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $post = $result->fetch(PDO::FETCH_ASSOC);
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


    <div class="div_1">

        <?php if (isset($_SESSION["cart"]) && count($_SESSION["cart"]) > 0) :  ?>

            <!-- <div class="div_1"> -->
            <table>
                <thead>
                    <tr>


                        <th width="100px">商品ID</th>
                        <th width="300px"></th>
                        <th>商品名</th>
                        <th>価格</th>
                        <th>注文数</th>
                        <th>小計</th>
                        <th></th>
                        <th></th>

                    </tr>
                </thead>


                <?php
                $sum = 0;

                ?>


                <tbody>
                    <tr>


                        <!-- 商品名と価格を表示させる -->

                        <?php foreach ($_SESSION["cart"] as $key => $v) :  ?>
                            <input type="hidden" name="delete_id" value="<?php $v['id'] ?>">


                            <!-- 商品ID(商品コード) -->
                            <td><?= $v['id'] ?></td>

                            <td><img id="img3" src="../../product/images/<?= $v['img'] ?>"></td>
                            <td>
                                <?= $v['product_name'] ?>
                                <input type="hidden" name="product_name" value="<?= $v['product_name'] ?>">
                            </td>
                            <td width="10%">
                                <?= $v['price'] ?>円
                                <input type="hidden" name="price" value="<?= $v['price'] ?>">
                            </td>
                            <td>
                                <?= $v['num'] ?>個
                                <input type="hidden" name="num" value="<?= $v['num'] ?>">

                                <!-- 数量の変更 -->
                                <form method="POST" action="cart_edit_num.php">
                                    <input type="hidden" name="id" value="<?php echo $v['id'] ?>">
                                    <input type="hidden" name="product_name" value="<?php echo $v['product_name'] ?>">
                                    <input type="hidden" name="num" value="<?php echo $v['num'] ?>">
                                    <input type="hidden" name="price" value="<?php echo $v['price'] ?>">
                                    <input type="hidden" name="img" value="<?php echo $v['img'] ?>">
                                    <input type="submit" value="数量の変更" class="shop-order" />
                                </form>

                            </td>


                            <!--  小計 「型を数字にキャストする＝(int)を入れると文字列を整数として返し、文字列非表示問題エラーが消える」
キャストとは、変数などのデータ型を別の型に変換すること -->
                            <td>
                                <?php $sub_total = (int)$v['num'] * (int)$v['price'];
                                echo $sub_total;
                                ?>円
                            </td>

                            <td>
                                <!-- カートから削除 -->
                                <form method="POST" action="cart_del.php">
                                    <input type="hidden" name="key" value="<?php echo $key; ?>" />
                                    <input type="submit" value="削除" class="del" />
                                </form>
                            </td>
                            <td>
                                <!-- 購入手続き -->
                                <?php if (!empty($_SESSION['member'])) { ?>
                                    <!-- ログイン中なら、購入手続きへ -->
                                    <form method="GET" action="delivery_registration_single_item.php">
                                        <input type="hidden" name="id" value="<?php echo $v['id'] ?>">
                                        <input type="hidden" name="product_name" value="<?php echo $v['product_name'] ?>">
                                        <input type="hidden" name="num" value="<?php echo $v['num'] ?>">
                                        <input type="hidden" name="price" value="<?php echo $v['price'] ?>">
                                        <input type="hidden" name="img" value="<?php echo $v['img'] ?>">
                                        <input type="submit" value="購入手続きへ" class="shop-order" />
                                    </form>
                            </td>
                        <?php } elseif (empty($_SESSION['member'])) { ?>
                            <!-- ログインしていなければ、ログイン画面へ遷移 -->

                            <!-- 会員登録へ -->
                            <input type="button" value="ご購入手続きへ" class="shop-order" onclick="
            location.href='../../login/join.php'">
                            <input type="hidden" name="id" value="<?php echo $v['id'] ?>">

                            <input type="hidden" name="num" value="<?php echo $v['num'] ?>">

                            <!-- membersテーブルに住所登録(post_number)がない場合 -->
                        <?php } elseif (empty($post)) { ?>
                            <!-- 郵便番号が登録されていなければ、住所登録画面へ遷移する -->
                            <input type="button" value="ご購入手続きへ" class="shop-order" onclick="
            location.href='../../edit/acount/confirm_address.php'">
                        <?php } ?>

                        <?php
                            //合計金額を出す。
                            $sum += $v['price'] * $v['num'];
                        ?>
                    </tr>

                    </form>

                <?php endforeach ?>

                </tbody>
            </table>

            <p class='p'><?php echo "合計金額 :" . $sum . "円" ?></p>


            <!-- カートを空にする"  -->

            <p><a href="cart_del_all.php" action="cart_del.php" target="_self" style="text-decoration:none;">カートを空にする</a></p>


        <?php endif ?>
        <!-- 商品一覧ボタン -->
        <br>
        <input type="button" value="商品一覧" class="re-order" onclick="
            location.href='../product_lists.php'">
        <div class="inlineBlock">
            <!-- カートの商品が２つ以上なら「一括購入」ボタンを表示させる。 -->
            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) >= 2) { ?>
                <!-- 複数購入 -->
                <form method="GET" action="multiple_cart_show.php">
                    <input type="hidden" name="order_id" value="<?php echo $value['id'] ?>">
                    <input type="hidden" name="client_id" value="<?php echo $_SESSION['member'] ?>">
                    <input type="hidden" name="num" value="<?php echo $v['num'] ?>">
                    <input type="hidden" name="sub_total" value="<?php echo $sub_total ?>">
                    <input type="hidden" name="img" value="<?php echo $v['img'] ?>">
                    <input type="submit" name="buy" value="一括購入する" class="shop-order" />
                </form>
            <?php } else {
                // 1個だけ、カートが空ならボタンは表示させない。
            } ?>
            <!-- 戻る -->

        </div>
        <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">
    </div>
    <!-- div_1おわり -->
</body>

</html>