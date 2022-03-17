<?php

    session_start();


    if (empty($_GET['recipe_id'])) {
            
        header("Location: ./edit_recipe_subtitle.php?id=". $_GET['recipe_Id'] );

        exit;


} else {

  
    try{


            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
            $dbh = new PDO($dsn, 'root', '');
        
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // サブ・タイトル(sub_title)が無ければ、
        if (empty($_GET['sub_title'])) {

            $sql = "UPDATE recipe_subtitles SET comment=:comment, recipe_id=:recipe_id
            WHERE id=:id";

            $stmt = $dbh->prepare ( $sql );

            $stmt->bindParam ( ":comment", $_GET['comment'], PDO::PARAM_STR );
            $stmt->bindParam ( ":recipe_id", $_GET['recipe_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":id", $_GET['id'], PDO::PARAM_INT );

            // sqlを実行
            $stmt->execute ();

            // 処理が完了したらリダイレクト
            header("Location: ./edit_recipe_subtitle.php?id=". $_GET['recipe_id']);

            exit;


          // コメントが無ければ、サブタイトルだけをインサートする
        } elseif (empty($_GET['comment'])) {  
                     

            $sql = "UPDATE recipe_subtitles SET sub_title=:sub_title, recipe_id=:recipe_id
            WHERE id=:id";

            // SQL文を実行する準備
            $stmt = $dbh->prepare ( $sql );

            $stmt->bindParam ( ":sub_title", $_GET['sub_title'], PDO::PARAM_STR );
            $stmt->bindParam ( ":recipe_id", $_GET['recipe_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":id", $_GET['id'], PDO::PARAM_INT );
        


            // sqlを実行
            $stmt->execute ();
            

            // 処理が完了したらリダイレクト
            header("Location: ./edit_recipe_subtitle.php?id=". $_GET['recipe_id']);
            exit;


        // どちらも空で無ければ、ふたつとも両方インサートする    
        } else {  


            $sql = "UPDATE recipe_subtitles SET comment=:comment, sub_title=:sub_title,
            recipe_id=:recipe_id
            WHERE id=:id";

            // SQL文を実行する準備
            $stmt = $dbh->prepare ( $sql );

            $stmt->bindParam ( ":comment", $_GET['comment'], PDO::PARAM_STR );
            $stmt->bindParam ( ":sub_title", $_GET['sub_title'], PDO::PARAM_STR );
            $stmt->bindParam ( ":recipe_id", $_GET['recipe_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":id", $_GET['id'], PDO::PARAM_INT );
            

            // sqlを実行
            $stmt->execute ();
            

            // 処理が完了したら（confirm.php）へリダイレクト
            header("Location: ./edit_recipe_subtitle.php?id=". $_GET['recipe_id']);
            exit;

        }


    } catch (PDOException $e) {
        echo 'my_recipeのDBに接続できません: ',  $e->getMessage(), "\n";
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        exit;
    }

}


?>