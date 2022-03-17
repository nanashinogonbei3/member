<?php

session_start();



if (empty($_SESSION['cart'])) {
    header('Location: ./cart_show.php');
    exit();
}


    try {

        // PHP rand() ランダムな数字を生成して、決済no.= 注文ナンバーを、カラムに挿入する。
        $settlement_no = rand();
   

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        // order_productsテーブル 中間テーブルにインサートする
        // product_lists テーブルとリレーションさせる。
        $sql = 'INSERT into order_products (';
        $sql .= 'members_id,';
        $sql .= 'product_lists_id,';
        $sql .= 'settlement_no,';
        $sql .= 'payment_method,';
        $sql .= 'product_name,';
        $sql .= 'price,';
        $sql .= 'num';
        $sql .= ') values (';
        $sql .= ':members_id,';
        $sql .= ':product_lists_id,';
        $sql .= ':settlement_no,';
        $sql .= ':payment_method,';
        $sql .= ':product_name,';
        $sql .= ':price,';
        $sql .= ':num';
        $sql .= ')';


        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  

        $stmt = $dbh->prepare($sql);



    foreach ($_SESSION['cart'] as $v) {


        // バインドする
        $stmt->bindValue(':members_id', $_SESSION['member'], PDO::PARAM_INT);

        $stmt->bindValue(':product_lists_id', $v['id'], PDO::PARAM_INT); 

        // 生成したランダムな数字 $rand変数を代入する
        $stmt->bindValue(':settlement_no', $settlement_no, PDO::PARAM_INT); 
        
        $stmt->bindValue(':payment_method', $_SESSION['payment']['method'], PDO::PARAM_STR);

        $stmt->bindValue(':product_name', $v['product_name'], PDO::PARAM_STR);

        $stmt->bindValue(':price', $v['price'], PDO::PARAM_INT);

        $stmt->bindValue(':num', $v['num'], PDO::PARAM_INT);

        $stmt->execute();

     } 

      
     
        // *****************************



        // ここで、orders_histories 中間テーブルにインサート
        // これにより、billing_addressesテーブルとorders_historiesテーブルがリレーションできる。
        // 配送先アドレスをショートカットで取り出せるようになる。
        $sql = 'INSERT INTO order_histories (';

        $sql .= 'settlement_no,';
        $sql .= 'billing_addresses_id,';
        $sql .= 'product_lists_id,';
        $sql .= 'member_id';
        $sql .= ') values (';
        $sql .= ':settlement_no,';
        $sql .= ':billing_addresses_id,';
        $sql .= ':product_lists_id,';
        $sql .= ':member_id';
        $sql .= ')';

        // orders_historiesテーブルの
        // インサート時に使うために予めセッションに渡して置いた値を
        // billing_addresses_idカラムにバインドする。

        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  

        $stmt = $dbh->prepare($sql);

        foreach ($_SESSION['cart'] as $v) {

        $stmt->bindValue(':settlement_no', $settlement_no, PDO::PARAM_INT);
        $stmt->bindValue(':billing_addresses_id', $_SESSION['billingaddressId'], PDO::PARAM_INT);
        $stmt->bindValue(':product_lists_id', $v['id'], PDO::PARAM_INT); 
        $stmt->bindValue(':member_id', $_SESSION['member'], PDO::PARAM_INT);


        $stmt->execute();

        }


 
        // DB登録処理完了後、セッション・カートを削除して、ページへリダイレクト
        foreach($_SESSION['cart'] as $key => $v) {  
           foreach($_SESSION['cart'] as $key => $o) {
                if ($v['id'] == $o['id'] ) {
                    unset($_SESSION['cart'][$key]);
                    unset($_SESSION['order']);
                    unset($_SESSION['payment']);
                    unset($_SESSION['delivery']);
                    unset($_SESSION["orderCustomerId"]);
                    unset($_SESSION['billingaddressID']);
                }              
             }
        }

        // order_itemsテーブル”order_id"の最後にインサートしたIDを
        // セッションに格納してthanks.phpへ渡す
        $_SESSION['message'] = 'お買い上げありがとうございました。';
        
        // thanks.phpで
        // 購入商品一覧を表示させるため、
        // $settlement_noをセッションに渡す。
        $_SESSION['settlement_no'] = $settlement_no;
        
     
        header("Location: ./thanks.php?id=");
        exit; 


    } catch (PDOException $e) {
            echo 'DBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            echo $e->getMessage();
            exit;
    }

?>