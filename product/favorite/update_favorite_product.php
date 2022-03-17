<?php
    session_start(); 


    $id = $_POST['favorite_product_id'];


    if(empty($_POST['favorite_product_id']) ) {

        header("Location: ../product_introduction.php?id=".$id); 
        exit;
    }

    try {

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        

        $sql = "UPDATE favorite_products SET  
        favorite_product_id=:favorite_product_id, members_id=:members_id,
        is_completed=:is_completed
        WHERE favorite_product_id=:favorite_product_id
        AND members_id=:members_id
        ";

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );

       
        $stmt->bindParam ( ":favorite_product_id", $_POST['favorite_product_id'], PDO::PARAM_INT );
        $stmt->bindParam ( ":members_id", $_POST['members_id'], PDO::PARAM_INT );
        $stmt->bindParam ( ":is_completed", $_POST['is_completed'], PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();
    
        // 処理が完了したら（product_introduction.php）へリダイレクト
        header("Location: ../product_introduction.php?id=".$id); 
        exit;


    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        echo $e->getMessage();
        exit;
    }



?>