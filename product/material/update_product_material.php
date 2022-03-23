<?php

session_start();




    try {

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    
        $dbh = new PDO($dsn, 'root', '');
    
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $sql = 'UPDATE materials SET recipe_id=:recipe_id ,material_name=:material_name,
        amount =:amount, product_id = :product_id WHERE id=:id';

        $stmt = $dbh->prepare ( $sql );
       

        $stmt->bindParam ( ":recipe_id", $_GET['recipe_id'], PDO::PARAM_INT );
        $stmt->bindParam ( ":material_name", $_GET['material_name'], PDO::PARAM_STR );
        $stmt->bindParam ( ":amount", $_GET['amount'], PDO::PARAM_STR );
        $stmt->bindParam ( ":product_id", $_GET['product_id'], PDO::PARAM_INT );
        $stmt->bindParam ( ":id", $_SESSION['id'], PDO::PARAM_INT );

        $stmt->execute ();
       
           
        // 処理が完了したらリダイレクト
        header("Location: ../../edit/recipe/confirm.php?id=" . $_GET['recipe_id']);
        exit;

    } catch (PDOException $e) {
        echo 'my_recipeのDBに接続できません: ',  $e->getMessage(), "\n";
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        echo $e->getMessage();
        exit;
    }
    

    ?>