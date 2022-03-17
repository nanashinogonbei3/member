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

    // メンバーテーブルから会員情報をFETCH()する 
    $sql = "SELECT * FROM members WHERE id= '" . $_SESSION['member'] . "' ";

    $stmt = $dbh->prepare($sql);

    $stmt->execute();

    $result = $dbh->query($sql);


    $list = $result->fetch(PDO::FETCH_ASSOC);

    // 〒を変数に代入する。
    foreach ($list as $key => $value) {
        if ($key === 'post_number') {
            $post_number = $value;
            // echo "私の郵便番号は、". "$post_number"."です。";
        }
        if ($key === 'last_name') {
            $last_name = $value;
        }
        if ($key === 'first_name') {
            $first_name = $value;
        }
        if ($key === 'phone_number') {
            $phone_number = $value;
        }
        if ($key === 'address1') {
            $address1 = $value;
        }
        if ($key === 'address2') {
            $address2 = $value;
        }
        if ($key === 'address3') {
            $address3 = $value;
        }
        if ($key === 'address4') {
            $address4 = $value;
        }
        if ($key === 'address5') {
            $address5 = $value;
        }
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


    // 入力にエラーが無ければ、次の会員登録確認画面に遷移する
    if (empty($error)) {

        $_SESSION['address'] = $_POST;
        header('Location: ./confirm_other_address.php');
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
    <title>ご住所の登録</title>
    <link rel="stylesheet" href="japan_post_num.css">
    <!-- 全体CSS -->
    <script src="https://cdn.jsdelivr.net/npm/fetch-jsonp@1.1.3/build/fetch-jsonp.min.js"></script>

    <!-- 郵便局JSONP URL -->
    <!-- https://into-the-program.com/javascript-get-address-zipcode-search-api/ -->
    <!-- 上記のライブラリを読み込んでJSONPが使用できるようにしておきます。 -->
    
    <!-- 全体CSS -->
    <link rel="stylesheet" href="stylesheet6.css">

</head>

<body>

    <div class="div_p">
        <dt><span style="font-size:21px">ご住所の登録</span></dt>
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

            <form action="" method="POST">
                <!-- membersテーブルのid -->
                <input type="hidden" name="id" value="<?php echo $_SESSION['member'] ?>">
                <br>


              
                <table>
                    <h3>●お届け先氏名</h3>
                    <div>

                    <?php if (!empty($post_number)) { ?>
                        <input type="button" value='登録の住所を編集' class="shop-order" onclick="location.href='./edit_address.php'">
                    <?php } else { ?>
                        <input type="button" value='ご自宅の住所を登録' class="shop-order" onclick="location.href='./address.php'">
                    <?php } ?>

                    <tr>

                        <th>氏名：</th>
                        <td>
                            <input id="last_name" type="text" name="last_name" value="">
                        </td>
                    </tr>

                    <tr>
                        <th>名前：</th>
                        <td>
                            <input id="first_name" type="text" name="first_name" value="">
                        </td>
                    </tr>

                    <tr>
                        <th>お電話番号</th>
                        <td>
                            <input id="phone_number" type="text" name="phone_number" value="">
                        </td>
                    </tr>
                </table>
             
                <table>
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
                            <td>
                                <input id="address1" type="text" name="address1" value="">
                            </td>
                        </tr>

                        <tr>
                            <th>市区町村</th>
                            <td>
                                <input id="address2" type="text" name="address2" value="">
                            </td>
                        </tr>

                        <tr>
                            <th>町域</th>
                            <td>
                                <input id="address3" type="text" name="address3" value="">
                            </td>
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
                            <td>
                                <input id="address5" type="text" name="address5" value="">
                            </td>
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
                </td>

            </form>
            <!-- 戻る -->
            <input type="button" class="re-order" onclick="location.href='./edit_address.php'" value="前のページに戻る">
       
        </div>

    </div>
    <!-- DIV block1おわり -->
    </div>
    

    <script src="japan_post_num.js"></script>


</body>

</html>