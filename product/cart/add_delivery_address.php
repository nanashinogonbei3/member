<?php

session_start();



if (empty($_SESSION['cart'])) {
    header('Location: ./cart_show.php');
    exit();
}


    try {

        // PHP rand() ランダムな数字を生成して、決済no.= 注文ナンバーを、カラムに挿入する。
        $settlement_no = rand();
        // ***

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // product/cart/delivery_registration_single_item.php で
    // members テーブルの情報が送られてきたら、（billing_addressesテーブルに登録されてない住所）という振り分けで  
    // 別送住所登録のbilling_address テーブルに新規登録する。
    // empty($_POST['id]) は、billing_addressテーブルのidが送信されなかった、つまりbilling_addressesテーブル
    // にない住所であるという認識です。
  if (!empty($_POST['memberid'])) {

    // 別送住所にインサートする。
    $sql = 'INSERT INTO billing_addresses (';
    
    if (!empty($_POST['address5'])) {
    
        $sql .= 'member_id,';
        $sql .= 'last_name,';
        $sql .= 'first_name,';
        $sql .= 'phone_number,';
        $sql .= 'post_number,';
        $sql .= 'address1,';
        $sql .= 'address2,';
        $sql .= 'address3,';
        $sql .= 'address4,';
        $sql .= 'address5';
        $sql .= ') values (';
        $sql .= ':member_id,';
        $sql .= ':last_name,';
        $sql .= ':first_name,';
        $sql .= ':phone_number,';
        $sql .= ':post_number,';
        $sql .= ':address1,';
        $sql .= ':address2,';
        $sql .= ':address3,';
        $sql .= ':address4,';
        $sql .= ':address5';
        $sql .= ')';

    } else {
        $sql .= 'member_id,';
        $sql .= 'last_name,';
        $sql .= 'first_name,';
        $sql .= 'phone_number,';
        $sql .= 'post_number,';
        $sql .= 'address1,';
        $sql .= 'address2,';
        $sql .= 'address3,';
        $sql .= 'address4';
  
        $sql .= ') values (';
        $sql .= ':member_id,';
        $sql .= ':last_name,';
        $sql .= ':first_name,';
        $sql .= ':phone_number,';
        $sql .= ':post_number,';
        $sql .= ':address1,';
        $sql .= ':address2,';
        $sql .= ':address3,';
        $sql .= ':address4';
      
        $sql .= ')';



    }


        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );

     
        

        $stmt->bindValue(':member_id', $_SESSION['member'], PDO::PARAM_INT);
        $stmt->bindValue(':first_name', $_POST['first_name'], PDO::PARAM_STR);
        $stmt->bindValue(':last_name', $_POST['last_name'], PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $_POST['phone_number'], PDO::PARAM_STR);
        $stmt->bindValue(':post_number', $_POST['post_number'], PDO::PARAM_STR_CHAR);
        $stmt->bindValue(':address1', $_POST['address1'], PDO::PARAM_STR);
        $stmt->bindValue(':address2', $_POST['address2'], PDO::PARAM_STR);
        $stmt->bindValue(':address3', $_POST['address3'], PDO::PARAM_STR);
        $stmt->bindValue(':address4', $_POST['address4'], PDO::PARAM_STR);

        if (!empty($_POST['address5'])) {
        $stmt->bindValue(':address5', $_POST['address5'], PDO::PARAM_STR);
        }

        $stmt->execute();


        // billing_addressesテーブルの注文IDを取得する
        // PHP公式マニュアルURL
        // https://www.php.net/manual/ja/pdo.lastinsertid.php
        $billingaddressId = $dbh->lastInsertId();
  
        // billingaddressId をのちに、order_middle_add.php のorders_historiesテーブルの
        // インサート時に使うのでセッションに渡しておく
        $_SESSION['billingaddressId'] = $billingaddressId;
    
    

        // ここでaddresses テーブルにインサート
        // コリにより、billing_addressesテーブルとaddressesテーブルがリレーションできる。
        $sql = 'INSERT INTO addresses (';

        $sql .= 'billing_addresses_id,';
        $sql .= 'member_id';
        $sql .= ') values (';
        $sql .= ':billing_addresses_id,';
        $sql .= ':member_id';
        $sql .= ')';


        
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':billing_addresses_id',$billingaddressId, PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $_SESSION['member'], PDO::PARAM_INT);


        $stmt->execute();


        // インサートが終わったら、支払方法へリダイレクトする。
        header("Location: ./payment_method.php?id=");
        exit; 
    



        //  billing_addressesテーブルのid  が送られてきたら、すでにbilling_addressesテーブルに登録のある
        //  住所であるということで更新します。
        //  データを更新する。
} elseif (!empty($_POST['billingid'])) {

        // オーダー履歴テーブルへインサートする
        $sql = 'UPDATE billing_addresses SET 
            member_id = :member_id,
            last_name = :last_name,
            first_name =:first_name,
            phone_number = :phone_number,
            post_number = :post_number,
            address1 = :address1,
            address2 = :address2,
            address3 = :address3,
            address4 = :address4,
            address5 = :address5 
            where id=:id';

    
            $stmt = $dbh->prepare ( $sql );

            
            $stmt->bindParam ( ":member_id", $_SESSION['member'], PDO::PARAM_STR );
            $stmt->bindParam ( ":last_name", $_POST['last_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":first_name", $_POST['first_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":phone_number", $_POST['phone_number'], PDO::PARAM_STR );
            $stmt->bindParam ( ":post_number", $_POST['post_number'], PDO::PARAM_STR_CHAR );
            $stmt->bindParam ( ":address1", $_POST['address1'], PDO::PARAM_STR );
            $stmt->bindParam ( ":address2", $_POST['address2'], PDO::PARAM_STR );
            $stmt->bindParam ( ":address3", $_POST['address3'], PDO::PARAM_STR );
            $stmt->bindParam ( ":address4", $_POST['address4'], PDO::PARAM_STR );
            $stmt->bindParam ( ":address5", $_POST['address5'], PDO::PARAM_STR );
            $stmt->bindParam ( ":id", $_POST['billingid'], PDO::PARAM_INT );
    

            $stmt->execute();


      

            // billingaddressId をのちに、order_middle_add.php のorders_historiesテーブルの
            // インサート時に使うのでセッションに渡しておく
            $_SESSION['billingaddressId'] = $_POST['billingid'];



            // インサートが終わったら、支払方法へリダイレクトする。
            header("Location: ./payment_method.php?id=");
            exit; 
            }




    } catch (PDOException $e) {
            echo 'DBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            echo $e->getMessage();
            exit;
    }

?>