<?php

session_start();




if (empty($_SESSION['cart'])) {
    header('Location: ./cart_show.php');
    exit;
}

try {


    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // メンバーテーブルから会員情報をFETCH()する 
    $sql = "SELECT * FROM members WHERE id= '" . $_SESSION['member'] . "' ";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);

    $member = $result->fetchall(PDO::FETCH_ASSOC);

    foreach ($member as $key => $v) {
        $member_postNo = $v['post_number'];
    }

    foreach ($member as $key => $v) {
        $first_name = $v['first_name'];
        $last_name = $v['last_name'];
        $address4 = $v['address4'];
    }



    // billing_addressesテーブルから会員情報をFETCH()する
    // 68行目 != membersテーブルの'電話番号' と同じ番号は除き
    // membersテーブルの人とbilling_addressesテーブル
    // にいる、同じ電話番号を持つ同一人物が重複表示されないようにする。
    // https://style.potepan.com/articles/25337.html
    $sql = "SELECT DISTINCT
    billing_addresses.id , billing_addresses.first_name, 
    billing_addresses.last_name, 
    billing_addresses.phone_number, billing_addresses.post_number,
    billing_addresses.address1, billing_addresses.address2, 
    billing_addresses.address3, billing_addresses.address4, 
    billing_addresses.address5
     FROM billing_addresses 
     JOIN members ON billing_addresses.member_id = members.id
     WHERE billing_addresses.phone_number != members.phone_number
     AND member_id= '" . $_SESSION['member'] . "' 
    ";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $billing = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($billing as $key => $v) {
        $billing_postNo = $v['post_number'];
    }
} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
}



if (isset($_POST['kakunin'])) {
    // エラーチェック項目：
    if ($_POST['post_number'] === '') {
        $error['post_number'] = 'blank';
    }
    if ($_POST['address4'] === '') {
        $error['address4'] = 'blank';
    }
    if ($_POST['last_name'] === '') {
        $error['last_name'] = 'blank';
    }
    if ($_POST['first_name'] === '') {
        $error['first_name'] = 'blank';
    }
    if ($_POST['phone_number'] === '') {
        $error['phone_number'] = 'blank';
    }

    // 入力にエラーが無ければ、次の会員登録確認画面に遷移する
    if (empty($error)) {

        $_SESSION['address'] = $_POST;


        header('Location: ../../edit/acount/confirm_address.php');
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
    <title>ご購入手続き</title>

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
        <dt><span style="font-size:21px">送り先ご住所</span></dt>
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



            <?php if (!empty($member_postNo)) { ?>

                <form action="confirm_delivery_address.php" method="POST">

                    <h3>ご自宅のご住所へ送る</h3>
                    <dt>
                        <!-- 住所（〒番号）登録があれば -->


                        <?php foreach ($member as $key => $v) : ?>


                            <input type="radio" name="memberid" value="<?php echo $v['id'] ?>">
                            <?php echo $v['last_name'] . $v['first_name'] . " 様：  " . $v['post_number'] . $v['address1'] . $v['address2'] . $v['address3'] . $v['address4'] . $v['address5'] ?>
                    </dt>
                    <br>

                    <input type="hidden" name="last_name" value="<?php echo $v['last_name'] ?>">
                    <input type="hidden" name="first_name" value="<?php echo $v['first_name'] ?>">
                    <input type="hidden" name="phone_number" value="<?php echo $v['phone_number'] ?>">
                    <input type="hidden" name="post_number" value="<?php echo $v['post_number'] ?>">
                    <input type="hidden" name="address1" value="<?php echo $v['address1'] ?>">
                    <input type="hidden" name="address2" value="<?php echo $v['address2'] ?>">
                    <input type="hidden" name="address3" value="<?php echo $v['address3'] ?>">
                    <input type="hidden" name="address4" value="<?php echo $v['address4'] ?>">
                    <input type="hidden" name="address5" value="<?php echo $v['address5'] ?>">

                <?php endforeach ?>


                <!-- ************************* -->


                <!-- 別送の住所登録があれば -->
                <?php if (!empty($billing)) : ?>


                    <h3>購入履歴から住所を選ぶ</h3>


                    <?php foreach ($billing as $key => $v) : ?>


                        <input type="radio" name="billingid" value="<?php echo $v['id'] ?>">
                        <?php echo $v['last_name'] . $v['first_name'];
                        echo '  様 :   ';
                        echo $v['post_number'] . $v['address1'] . $v['address2'] . $v['address3'] . $v['address4'] . $v['address5'] . '<br>'; ?>

                        <input type="hidden" name="last_name" value="<?php echo $v['last_name'] ?>">
                        <input type="hidden" name="first_name" value="<?php echo $v['first_name'] ?>">
                        <input type="hidden" name="phone_number" value="<?php echo $v['phone_number'] ?>">
                        <input type="hidden" name="post_number" value="<?php echo $v['post_number'] ?>">
                        <input type="hidden" name="address1" value="<?php echo $v['address1'] ?>">
                        <input type="hidden" name="address2" value="<?php echo $v['address2'] ?>">
                        <input type="hidden" name="address3" value="<?php echo $v['address3'] ?>">
                        <input type="hidden" name="address4" value="<?php echo $v['address4'] ?>">
                        <input type="hidden" name="address5" value="<?php echo $v['address5'] ?>">


                    <?php endforeach ?>

                <?php endif ?>

                <div class="btn">
                    <input type="reset" value="リセット" class="btn-border">
                </div>

                <br>
                <input type="submit" name="kakunin" value="確認" class="shop-order">

                <br>

                </form>

                <!-- メインの登録住所がなければ住所登録を行う -->
            <?php } elseif (empty($member_postNo)) { ?>



                <br>

                <?php if (!empty($billing_postNo)) : ?>
                    <input type="button" value='ご登録の別送宛に送る' class="shop-order" onclick="location.href='./delivery_billing_address.php'">
                <?php endif ?>


                <form action="" method="POST">

                    <table>
                        <h3>●お届け先氏名</h3>


                        <tr>


                            <th>氏名：</th>
                            <td>
                                <input type="text" name="last_name" value="">
                                <?php if (!empty($error['last_name'])) : ?>
                                    <p class="error">* お届け先氏名を入力してください</p>
                                <?php endif ?>
                            </td>


                        </tr>

                        <tr>
                            <th>名前：</th>
                            <td>
                                <input type="text" name="first_name" value="">
                                <?php if (!empty($error['first_name'])) : ?>
                                    <p class="error">* お届け先のお名前を入力してください</p>
                                <?php endif ?>
                            </td>


                        </tr>

                        <tr>
                            <th>お電話番号</th>
                            <td>
                                <input type="text" name="phone_number" value="">
                                <?php if (!empty($error['phone_number'])) : ?>
                                    <p class="error">* お届け先の連絡先電話番号を入力してください</p>
                                <?php endif ?>
                            </td>

                        </tr>
                    </table>
                    <table>

                        <!-- ---------------------------------------------------------- -->
                        <h3>●お届け先のご住所を入力してください。</h3>
                        <tbody>
                            <tr>

                                <th>郵便番号</th>
                                <td>
                                    <input id="input" class="zipcode" type="text" size="18" name="post_number" value="" placeholder="例)812-0012">
                                    <button id="search" type="button">住所検索</button><input type="button" value="〒郵便番号検索" class="post-no-serch" onclick="window.open('//www.post.japanpost.jp/zipcode/','view');" rel="noopener noreferrer">
                                    <input type="reset" value="リセット">
                                    <p id="error"></p>

                                    <!-- もしPOSTされた時に -->
                                    <?php if (!empty($error['post_number'])) : ?>
                                        <p class="error">* 郵便番号を入力してください</p>
                                    <?php endif ?>
                                </td>


                            </tr>

                            <tr>
                                <th>都道府県</th>
                                <td><input id="address1" type="text" name="address1" value=""></td>
                            </tr>

                            <tr>
                                <th>市区町村</th>
                                <td><input id="address2" type="text" name="address2" value=""></td>
                            </tr>

                            <tr>
                                <th>町域</th>
                                <td><input id="address3" type="text" name="address3" value=""></td>
                            </tr>

                            <tr>
                                <th>番地</th>
                                <td>
                                    <input id="address4" type="text" name="address4" value="">

                                    <!-- もし番地が未入力でPOSTされたらエラーを表示する。 -->
                                    <?php if (!empty($error['address4'])) : ?>
                                        <p class="error">* 番地を入力してください</p>
                                    <?php endif ?>
                                </td>
                            </tr>

                            <tr>
                                <th>建物名</th>
                                <td><input id="address5" type="text" name="address5" value=""></td>
                            </tr>




                        </tbody>
                    </table>




                    <tr>

                    </tr>

                    <tr>

                        <tbody>
                    </tr>

                    </tbody>
                    </table>



                    <br>
                    <td>

                        <div>
                            <input type="submit" name="kakunin" value="確認" class="shop-order">
                            <br>
                    </td>
                    <br>
                </form>



            <?php } ?>


            <br>
            <!-- 戻る -->
            <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">




        </div>
    <!-- DIV block1おわり -->
    </div>


    <script src="js/japan_post_num.js"></script>


</body>

</html>