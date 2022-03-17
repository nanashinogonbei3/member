<?php

session_start();




try {

$dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';


$dbh = new PDO($dsn, 'root', '');


$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = 'UPDATE members SET post_number=:post_number, address1=:address1, 
        address2=:address2, address3=:address3, address4=:address4,
        address5=:address5 
        WHERE id=:id';

    
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);

        
        // SQL（更新）の実行

        $stmt->bindParam ( ":post_number", $_SESSION['address']['post_number'], PDO::PARAM_STR_CHAR );
        $stmt->bindParam ( ":address1", $_SESSION['address']['address1'], PDO::PARAM_STR );
        $stmt->bindParam ( ":address2", $_SESSION['address']['address2'], PDO::PARAM_STR );
        $stmt->bindParam ( ":address3", $_SESSION['address']['address3'], PDO::PARAM_STR );
        $stmt->bindParam ( ":address4", $_SESSION['address']['address4'], PDO::PARAM_STR );
        $stmt->bindParam ( ":address5", $_SESSION['address']['address5'], PDO::PARAM_STR );
        $stmt->bindParam ( ":id", $_SESSION['member'], PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();


        // 処理が完了したら、
        header("Location: ./edit_address.php?id=" . $_SESSION['member']);
        
        exit;


    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        var_dump($e);
        echo $e->getMessage();
        exit;
    }

?>