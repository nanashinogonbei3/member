<?php

session_start();


    try {

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';


        $dbh = new PDO($dsn, 'root', '');


        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'INSERT INTO billing_addresses (';
       
     
        $sql .= 'last_name,';
        $sql .= 'first_name,';
        $sql .= 'phone_number,';
        $sql .= 'post_number,';
        $sql .= 'member_id,';
        $sql .= 'address1,';
        $sql .= 'address2,';
        $sql .= 'address3,';
        $sql .= 'address4,';
        $sql .= 'address5';
        $sql .= ') values (';
        $sql .= ':last_name,';
        $sql .= ':first_name,';
        $sql .= ':phone_number,';
        $sql .= ':post_number,';
        $sql .= ':member_id,';
        $sql .= ':address1,';
        $sql .= ':address2,';
        $sql .= ':address3,';
        $sql .= ':address4,';
        $sql .= ':address5';
        $sql .= ')';

        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  

        $stmt = $dbh->prepare($sql);


        $stmt->bindValue(':first_name', $_SESSION['address']['first_name'], PDO::PARAM_STR);
        $stmt->bindValue(':last_name', $_SESSION['address']['last_name'], PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $_SESSION['address']['phone_number'], PDO::PARAM_STR);
        $stmt->bindValue(':post_number', $_SESSION['address']['post_number'], PDO::PARAM_STR_CHAR);
        $stmt->bindValue(':member_id', $_SESSION['address']['id'], PDO::PARAM_INT);
        $stmt->bindValue(':address1', $_SESSION['address']['address1'], PDO::PARAM_STR);
        $stmt->bindValue(':address2', $_SESSION['address']['address2'], PDO::PARAM_STR);
        $stmt->bindValue(':address3', $_SESSION['address']['address3'], PDO::PARAM_STR);
        $stmt->bindValue(':address4', $_SESSION['address']['address4'], PDO::PARAM_STR);
        $stmt->bindValue(':address5', $_SESSION['address']['address5'], PDO::PARAM_STR);
  

        $stmt->execute();


        // 直近でインサートしたIDを読み込んで変数に渡す。
        $billingAddressId = $dbh->lastInsertId();
        // PHP公式マニュアルURL
        // https://www.php.net/manual/ja/pdo.lastinsertid.php



        // **************
        
        // 中間テーブル（addressテーブル)にインサートする。
         $sql = 'INSERT INTO addresses(';
         $sql .= 'member_id,';
         $sql .= 'billing_addresses_id';
         $sql .= ') values (';
         $sql .= ':member_id,';
         $sql .= ':billing_addresses_id';
         $sql .= ')';
 
         $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  
 
         $stmt = $dbh->prepare($sql);
      
 
         // バインドする
         $stmt->bindValue(':member_id', $_SESSION['member'], PDO::PARAM_INT);
         $stmt->bindValue(':billing_addresses_id', $billingAddressId, PDO::PARAM_INT);
 
         $stmt->execute();




        header("Location:billing_address_lists.php?id=" . $_SESSION['member']);
        exit; 


    } catch (PDOException $e) {
            // echo 'Product_listsのDBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            echo $e->getMessage();
            exit;
    }

?>