<?php
session_start();




if (empty($_POST)) {
    header('Location: ./delivery_billing_address.php');
    exit;
}


try {

    // membersテーブルのidカラムのデータを受け取る。

        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

        $date = $dt->format('Y-m-d');

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // membersテーブルから一つ登録されている住所を取り出す
        $sql = "SELECT * FROM members WHERE members.id ='".$_SESSION['member']."'
        ";

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $member = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($member as $key => $v) {
            $memberPostNo = $v['post_number'];
        }



    if (!empty($_POST["billingid"])) {

        // billing_addressesテーブルから複数の別送の住所を取り出す
        $sql = "SELECT * FROM billing_addresses WHERE id= '" . $_POST["billingid"] . "'  ";

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $billing = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } 



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
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>お届け先の確認</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">
   

</head>

<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <p class="title_font">お届け先の確認</p>
        </div>


        <div class="comprehensive">

            <!--  新規会員登録の確認 -->
            <div class='inline_block_2'>

                <div class="inline_block_3">

                <div class="div_font_inline">
                    <p class="p_font_rarge">ご記入頂いた内容を確認して、よろしければ「登録」ボタンをクリックしてください</p>
                    <div class="line"></div>
                </div>


                <!-- DBへ接続しデータをインサートする add.php のDB挿入ファイルをaction=add.phpでファイルを指定する -->
            <form action="add_delivery_address.php" method="POST">


                <table>
                    <h2>お届け先</h2>
                    <div>



                <!-- delivery_registration_single_item.php からPOSTされたidのname属性が’memberid'を受け取ると、
                membersテーブルの住所が表示される 。念のためmembersテーブルの住所に郵便番号があることを条件に付加した。-->
                <!-- members テーブル -->
                <?php if (empty($_POST['billingid'] ) && !empty($memberPostNo)) : ?>

                    <?php 
                        foreach ($member as $key => $v) :  
                    ?>

                    <tr>
                        <!-- id をhiddenで送る . billing_addressesテーブルへインサートするため -->
                        <input type="hidden" name="memberid" value="<?php echo $v['id'] ?>">

                        <th>氏名：</th>
                        <td>
                            <?php echo $v['last_name'] ?>
                            <input type="hidden" name="last_name" value="<?php echo $v['last_name'] ?>">           
                        </td>

                    <tr>
                        <th>名前：</th>
                        <td>
                            <?php echo $v['first_name'] ?>
                            <input type="hidden" name="first_name" value="<?php echo $v['first_name'] ?>">
                            様
                        </td>
                    </tr>

                    <tr>
                        <th>お電話番号</th>
                        <td>
                            <?php echo $v['phone_number'] ?>
                            <input type="hidden" name="phone_number" value="<?php echo $v['phone_number'] ?>">    
                        </td>
                    </tr>
                    </table>




                    <table>

                    <tbody>
                        <tr>

                            <th>郵便番号</th>
                            <td>
                                <?php echo $v['post_number'] ?>
                                <input type="hidden" name="post_number" value="<?php echo $v['post_number'] ?>">
                            </td>
                        </tr>

                        <tr>
                            <th>都道府県</th>
                            <td>
                                <?php echo $v['address1'] ?>
                                <input type="hidden" name="address1" value="<?php echo $v['address1'] ?>">
                            </td>
                        </tr>

                        <tr>
                            <th>市区町村</th>
                            <td>
                                <?php echo $v['address2'] ?>
                                <input type="hidden" name="address2" value="<?php echo $v['address2'] ?>"> 
                            </td>
                        </tr>

                        <tr>
                            <th>町域</th>
                            <td>
                                <?php echo $v['address3'] ?>
                                <input type="hidden" name="address3" value="<?php echo $v['address3'] ?>"> 
                            </td>
                        </tr>

                        <tr>
                            <th>番地</th>
                            <td>
                                <?php echo $v['address4'] ?>
                                <input type="hidden" name="address4" value="<?php echo $v['address4'] ?>"> 
                            </td>
                        </tr>

                        <tr>
                            <th>建物名</th>
                            <td>
                            <?php if (!empty($v['address5'])) : ?>
                                <?php echo $v['address5'] ?>
                                <input type="hidden" name="address5" value="<?php echo $v['address5'] ?>">
                            <?php endif ?>
                            </td>
                        </tr>

                    </tbody>
                    </table>
                    <?php 
                    endforeach 
                    ?>

                <?php endif ?>

               


                <!-- delivery_registration_single_item.php からPOSTされたidのname属性が'billingid'を受け取ると、
                billing_addressesテーブルの住所が表示される。-->
                <!-- billing_addresses テーブル -->                 
                <!-- のちにbilling_addressesテーブルの住所をUPDATEする -->
                <?php if (!empty($billing)) : ?>

                    <?php foreach ($billing as $key => $v) ?>

                    <tr>
                        <!-- billing_addressesテーブルのidを渡す -->
                        <input type="hidden" name="billingid" value="<?php echo $v['id'] ?>">

                        <th>氏名：</th>
                        <td>
                        <?php echo $v['last_name'] ?>
                            <input type="hidden" name="last_name" value="<?php echo $v['last_name'] ?>">           
                        </td>

                    <tr>
                        <th>名前：</th>
                        <td>
                        <?php echo $v['first_name'] ?>
                            <input type="hidden" name="first_name" value="<?php echo $v['first_name'] ?>">
                            様
                        </td>
                    </tr>

                    <tr>
                        <th>お電話番号</th>
                        <td>
                        <?php echo $v['phone_number'] ?>
                            <input type="hidden" name="phone_number" value="<?php echo $v['phone_number'] ?>">    
                        </td>
                    </tr>
                </table>




                <table>
               
                    <tbody>
                        <tr>

                            <th>郵便番号</th>
                            <td>
                            <?php echo $v['post_number'] ?>
                                <input type="hidden" name="post_number" value="<?php echo $v['post_number'] ?>">
                            </td>
                        </tr>

                        <tr>
                            <th>都道府県</th>
                            <td>
                            <?php echo $v['address1'] ?>
                                <input type="hidden" name="address1" value="<?php echo $v['address1'] ?>">
                            </td>
                        </tr>

                        <tr>
                            <th>市区町村</th>
                            <td>
                            <?php echo $v['address2'] ?>
                                <input type="hidden" name="address2" value="<?php echo $v['address2'] ?>"> 
                            </td>
                        </tr>

                        <tr>
                            <th>町域</th>
                            <td>
                            <?php echo $v['address3'] ?>
                                <input type="hidden" name="address3" value="<?php echo $v['address3'] ?>"> 
                            </td>
                        </tr>

                        <tr>
                            <th>番地</th>
                            <td>
                            <?php echo $v['address4'] ?>
                                <input type="hidden" name="address4" value="<?php echo $v['address4'] ?>"> 
                            </td>
                        </tr>

                        <tr>
                            <th>建物名</th>
                            <td>
                            <?php echo $v['address5'] ?>
                                <input type="hidden" name="address5" value="<?php echo $v['address5'] ?>">
                            </td>
                        </tr>

                    </tbody>
                </table>
                <?php endif ?>



                    <!-- お届け先を選びなおす -->
                    <input type="button" class="shop-order" onclick="window.history.back();" value="お届け先を選びなおす">
                    <input type="submit" id="submit" class="re-order" value="お届け先を確定する" />



                </div>
            </form>


            </div>
        </div>
    </div>

</body>

</html>