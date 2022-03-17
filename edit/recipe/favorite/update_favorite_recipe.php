<?php
session_start(); 


$id = $_POST['favorite_recipe_id'];


if(empty($_POST['favorite_recipe_id']) ) {

    header("Location: ../release_recipe.php?id=".$id); 
    exit;
}

    try {

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        

        $sql = "UPDATE favorite_recipes SET  
        favorite_recipe_id=:favorite_recipe_id, members_id=:members_id,
        is_completed=:is_completed
        WHERE favorite_recipe_id=:favorite_recipe_id
        AND members_id =:members_id
        "; 

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );

       
        $stmt->bindParam ( ":favorite_recipe_id", $_POST['favorite_recipe_id'], PDO::PARAM_INT );
        $stmt->bindParam ( ":members_id", $_POST['members_id'], PDO::PARAM_INT );
        $stmt->bindParam ( ":is_completed", $_POST['is_completed'], PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();

                
        // 処理が完了したら（release_recipe.php）へリダイレクト
        header("Location: ../release_recipe.php?id=".$id); 
        exit;


    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        echo $e->getMessage();
        exit;
    }