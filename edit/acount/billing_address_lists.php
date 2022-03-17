<?php

session_start();

if (empty($_SESSION['member'])) {
    header('Location: ../../login/join.php');
    exit;
}

try {


    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // billing_addressesテーブルから会員情報をFETCH()する 
    $sql = "SELECT distinct 
    billing_addresses.id,
    billing_addresses.last_name, billing_addresses.first_name, billing_addresses.post_number,
    billing_addresses.address1, billing_addresses.address2, billing_addresses.address3,
    billing_addresses.address4, billing_addresses.address5
    FROM billing_addresses 
    WHERE member_id= '" . $_SESSION['member'] . "' 
    ";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}




// セッションに記録された時間が、今の時間よりも大きい、つまりログイン時間から
// 1時間以上たっていた場合,という意味
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    // （1時間が経過していたら、）ログアウトし、ログイン画面に遷移する
    $_SESSION['time'] = time();
    // 現在の時刻で上書きします。こうすることで、何か行動したことで上書きすることで
    // 最後の時刻から１時間を記録することができるようになる。 
} elseif ($_SESSION['member'] = []) {
    header('Location: ../../login/join.php');
    exit();
    // 更新時刻より１時間経過していなくとも、クッキーの削除でセッション情報が空になったら
    // ログイン画面に遷移する
} else {
    // 何か行動した更新時刻より１時間経過したら、自動的にログイン画面に遷移します
    header('Location: ../../login/join.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>別送のご住所一覧</title>

<!-- 郵便局JSONP URL -->
    <!-- https://into-the-program.com/javascript-get-address-zipcode-search-api/ -->
    <!-- 上記のライブラリを読み込んでJSONPが使用できるようにしておきます。 -->

    <!-- 郵便局API CSS -->
    <link rel="stylesheet" href="css/japan_post_num.css">
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/fetch-jsonp@1.1.3/build/fetch-jsonp.min.js"></script>

    
    <!-- 全体CSS -->
    <link rel="stylesheet" href="css/stylesheet6.css">

</head>

<body>

    <div class="div_p">
        <dt><span style="font-size:21px">別送のご住所一覧</span></dt>
        <!-- ログアウト -->
        <div class="div_logout1">
            <input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../../logout/process.php'">

        </div>

        <!-- マイページ -->
        <div class="div_logout1">
            <input type="button" value='マイページ' class="logout_btn" onclick="location.href='../../login/process.php'">

        </div>

    </div>
    <div class="block1">
        <div id="app">




            <table>
                <h2>別送のお届け先</h2>



                <form action="edit_other_address.php" method="POST">


                    <?php
                    foreach ($list as $key => $v) : ?>


                        <ul>
                            <li>
                                <input type="radio" name='id' value="<?php echo $v['id'] ?>">
                                <?php echo $v['last_name'] . $v['first_name'];
                                echo '  様 :   ';
                                echo $v['post_number'] . $v['address1'] . $v['address2'] . $v['address3'] . $v['address4'] . $v['address5'] . '<br>'; ?>
                            </li>
                        </ul>


                    <?php endforeach ?>

                    <input type="submit" name="send" value="確認" class="shop-order">

                </form>


            </table>




            <!-- キャンセル -->
            <br><br><br>
            <input type="button" value='キャンセル' class="re-order" onclick="location.href='../../login/process.php'">


            <!-- 戻る -->
            <input type="button" class="shop-order" onclick="location.href='./edit_address.php'" value="前のページに戻る">
            <?php if (!empty($_SESSION['cart'])) : ?>
                <input type="button" class="re-order" onclick="location.href='../../product/cart/cart_show.php'" value="購入手続きへ戻る">
            <?php endif ?>

        </div>

    </div>
    <!-- DIV block1おわり -->
    </div>


    <!-- 郵便局API JavaScript -->
    <script src="japan_post_num.js"></script>

</body>

</html>