<?php
session_start();


// ログイン情報が無ければ、ログイン画面にリダイレクト
if (empty($_SESSION['member'])) {
    header('Location: ../login/join.php');
    exit();
}
// 1ページの$list でFETCH ALL の表示数
define('max_view', 3);


try {


    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $sql = "SELECT DISTINCT 
    order_products.settlement_no, order_products.order_date,
    order_products.product_lists_id, order_products.product_name, 
    order_products.price, order_products.num, order_products.payment_method,
    product_lists.img
    FROM order_products
    LEFT JOIN product_lists ON product_lists.id = order_products.product_lists_id
    WHERE order_products.members_id = '" . $_SESSION['member'] . "'   
    ";



    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $list = $result->fetchAll(PDO::FETCH_ASSOC);


    $total_count = count($list);



    // ページ数= 全商品数/1ページの表示数
    // トータルページ数※ceilは小数点を切り上げる関数1.6⇒2
    $pages = ceil($total_count / max_view);



    //現在いるページのページ番号を取得
    if (!isset($_GET['page_id'])) {
        $now = 1;
    } else {
        $now = $_GET['page_id'];
    }



    // order_productsテーブルの、settlementカラムの重複しない値をカウントして、注文回数を出力する。
    $sql = 'SELECT COUNT(DISTINCT settlement_no ) FROM order_products WHERE members_id = '.$_SESSION['member'].'
 
    ';

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $countSettlement = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach ($countSettlement as $v) {
        $countSettlement = $v;
    }


  
   

    // ページネーションの1ページ目のsqlの処理・1ページ以外のsqlの処理
    //表示するページを取得するSQLを準備
    $select = $dbh->prepare("SELECT DISTINCT 
        order_products.settlement_no, order_products.order_date,
        order_products.product_lists_id, order_products.product_name, 
        order_products.price, order_products.num, order_products.payment_method,
        product_lists.img
        FROM order_products
        LEFT JOIN product_lists ON product_lists.id = order_products.product_lists_id
        WHERE order_products.members_id = '" . $_SESSION['member'] . "'
        ORDER BY order_products.id DESC LIMIT :start,:max "
    );
   
      

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

  


    // セッションに記録された時間が、今の時間よりも大きい、つまりログイン時間から
    // 1時間以上たっていた場合,という意味
    if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
        // （1時間が経過していたら、）ログアウトし、ログイン画面に遷移する
        $_SESSION['time'] = time();
        // 現在の時刻で上書きします。こうすることで、何か行動したことで上書きすることで
        // 最後の時刻から１時間を記録することができるようになる。 
    } elseif ($_SESSION['member'] = []) {
        header('Location: ../login/join.php');
        exit();
        // 更新時刻より１時間経過していなくとも、クッキーの削除でセッション情報が空になったら
        // ログイン画面に遷移する
    } else {
        header('Location: ../login/join.php');
        exit();
        // 何か行動した更新時刻より１時間経過したら、自動的にログイン画面に遷移します
    }
} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>購入履歴</title>
    <!-- 全体CSS -->
    <link rel="stylesheet" href="stylesheet5.css">
    <!-- ページネーション -->
    <link rel="stylesheet" href="style_paging3.css">
</head>

<body>


    <h2>ご購入履歴</h2>
    <!-- ---------------------------------------------- -->

    <!-- ページングCSS -->
    <div class="div_w5">
        <div class="flex">
            <ul class="bar">
                <li>
                    <span style="color:#000000;font-size:15px;font-weight:lighter">
                  <?php  foreach ($countSettlement as $v) { ?>
                        購入履歴は、全:<span style="color:green;font-size:24px"><?php echo $v ?></span>回です。</span>
                  <?php } ?>
                    &nbsp;&nbsp;

                    <?php if ($pages >= 2) { ?>
                        <!-- ページが２ページ以上あればページングを表示する -->
                        <?php
                        //ページネーションを表示    
                        if ($now > 1) {
                            // 1ページより大きいなら、「前へ」表示
                            echo '<a href="?page_id=', ($now - 1), '">  
                            <img src="../icon_img/pre.png"
                            alt="前へ" width="25" height="25" border="0">
                            </a>';
                        } else {
                            //  1ページよりも小さい＝ページが無い、場合は矢印は表示させない。
                        }
                        ?>

                        <?php
                        // 1 2 3 
                        for ($n = 1; $n <= $pages; $n++) {
                            if ($n == $now) {
                                // 現在表示されているページなら、リンクは付けない。
                                echo "<span style='padding: 5px;'>$now</span>";
                            } else {
                                echo "<a href='./shopping_history.php?page_id=$n' style='padding: 5px;'>$n</a>";
                                // それ以外のページの数字には、リンクを貼る
                                // hrefのリンクは、表示現在表示するリンクに修正して使うこと。
                            }
                        }
                        ?>

                        <?php
                        if ($now < $pages) {
                            // 表示ページが最終ページより小さいなら、「次へ」表示
                            echo '<a href="?page_id=', ($now + 1), '">  
                    <img src="../icon_img/next.png"
                        alt="次へ" width="25" height="25" border="0" margin-top:1px>
                    </a>';
                        }
                        ?>
                    <?php } elseif ($pages == 1) {
                        // ページ数が1なら、ページングは非表示。
                    } ?>
                </li>
            </ul>
        </div>
    </div>

    <!-- ---------------------------------------------- -->
    <!-- 購入商品履歴 表示欄 -->
    <div class="div_2">

        <!-- テーブル -->
        <?php if (!empty($data)) : ?>
        <table>
            <thead>
                <tr>
                    <th>注文時刻</th>
                    <th>注文番号</th>
                    <th>小計</th>
                    <th>お支払方法</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <?php foreach ($data as $key => $v) : ?>

                        <td><?= $v['order_date'] ?></td>
                        <td><?= $v['settlement_no'] ?></td>
                        <td><?= ($v['price']) * ($v['num']) ?>円</td>
                        <td><?= $v['payment_method'] ?></td>
                        <td>
                            <form action="shopping_history_product.php" method="POST">
                                <input type="hidden" name="settlement_no" value="<?= $v['settlement_no'] ?>">

                                <!-- ボタン -->
                                <div class="div_re-order">
                                    <input type="submit" class="re-order3" value="詳細">
                                </div>
                            </form>
                        </td>
                </tr>

            <?php endforeach ?>

            </tbody>
        </table>

        <?php endif ?>


        <!-- 商品一覧ボタン -->
        <br>
        <div class="inlineBlock">

            <input type="button" value="商品一覧" class="shop-order" onclick="
            location.href='../product/product_lists.php'" value='商品一覧'>


            <!-- マイページ -->
            <input type="button" value='マイページ' class="logout_btn" onclick="location.href='../login/process.php'">


            <!-- みんなのレシピ -->
            <input type="button" value='みんなのレシピ' class="logout_btn3" onclick="location.href='../top/confirm.php'">


            <!-- ログアウト -->
            <input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../logout/process.php'">


        <!-- DIV inlineBlock -->
        </div>

    <!-- DIV div_1 -->
    </div>


</body>

</html>