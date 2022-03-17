<?php

session_start();




// カートの中が空ならば、カートに戻る
if (empty($_SESSION['cart'])) {
    header('Location: ./cart_show.php');
    exit();
}


if (isset($_POST['pay'])) {
    // ラジオボタンの場合、空送信しても空であるという情報は遅れない。
    // なので$_POST['method']事態が存在しなくなるので、===''でなく!issetで設定する。 
    if (!isset($_POST['method'])) {
        $error['method'] = 'blank';
    }

    if (empty($error)) {

        $_SESSION['payment'] = $_POST;
        echo 'お支払方法が選択されました。';
        // exit;
        header('Location: ./confirm_payment.php');
        exit();
    }
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
    header('Location: ../../login/join.php');
    exit();
    // 何か行動した更新時刻より１時間経過したら、自動的にログイン画面に遷移します
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お支払方法</title>

    <!-- 郵便局のAPI〒番号から住所検索機能 -->

    <script src="https://cdn.jsdelivr.net/npm/fetch-jsonp@1.1.3/build/fetch-jsonp.min.js"></script>
    <!-- https://into-the-program.com/javascript-get-address-zipcode-search-api/ -->
    <!-- 上記のライブラリを読み込んでJSONPが使用できるようにしておきます。 -->

    <!-- 郵便局APIのcss -->
    <link rel="stylesheet" href="css/japan_post_num.css">

    <!-- 全体CSS -->
    <link rel="stylesheet" href="css/stylesheet6.css">

</head>

<body>

    <div class="div_p">
        <dt><span style="font-size:21px">お支払方法</span></dt>
        <!-- ログアウト -->
        <div class="div_logout1">
            <input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../../logout/process.php'">
            <!-- /member/logout/process.php -->
        </div>

        <!-- マイページ -->
        <div class="div_logout1">
            <input type="button" value='マイページ' class="logout_btn" onclick="location.href='../../login/process.php'">
            <!-- /member/logout/process.php -->
        </div>

    </div>
    <div class="block1">
        <div id="app">

            <!-- フォーム -->
            <form action="" method="POST">


                <!-- 注文したorder_cartテーブルのid -->
                <table>
                    <h3>●お支払方法をお選びください</h3>



                    <tr>
                        <?php if (!empty($error['method'])) : ?>
                            <p class="error">* お支払方法をお選びください</p>
                        <?php endif ?>
                    </tr>


                    <tr>
                        <th>1.</th>
                        <td>
                            <dt><input type="radio" name="method" value="クレジットカード" id="クレジットカード">
                                クレジットカード</dt>
                        </td>
                    </tr>

                    <tr>
                        <th>2.</th>
                        <td>
                            <dt><input type="radio" name="method" value="振込用紙">振込用紙（後払い）</dt>
                        </td>
                    </tr>

                    <tr>
                        <th>3.</th>
                        <td>
                            <dt><input type="radio" name="method" value="銀行振込">銀行振込</dt>
                        </td>
                    </tr>

                    <tr>
                        <th>4.</th>
                        <td>
                            <dt><input type="radio" name="method" value="代引き">代引き</dt>
                        </td>
                    </tr>

                    <tr>
                        <th>5.</th>
                        <td>
                            <dt><input type="radio" name="method" value="デビットカード">デビットカード</dt>
                        </td>
                    </tr>

                    <tr>
                        <th>6.</th>
                        <td>
                            <dt><input type="radio" name="method" value="コンビニ払い">コンビニ払い</dt>
                        </td>
                    </tr>
                </table>

                <div class="btn">
                    <input type="reset" value="リセット" class="btn-border">
                </div>
                <br>
                <div class="inlineBlock">

                    <input type="submit" name="pay" value="確認" class="shop-order">

            </form>

            <!-- 戻る -->
            <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">

        </div>
    </div>
    <!-- DIV block1おわり -->
    </div>


    <script src="js/japan_post_num.js"></script>


</body>

</html>