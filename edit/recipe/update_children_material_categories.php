<?php

            session_start();



    try {

            $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

            $date = $dt->format('Y-m-d');

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            $dbh = new PDO($dsn,'root','');

            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


  
    
            // ユーザー定義の材料カテゴリー・テーブルの更新
            $sql = 'UPDATE material_categories SET 
            parent_category_id = :parent_category_id,
            recipe_id = :recipe_id,
            material_category_name =:material_category_name, 
            users_id=:users_id
            where id=:id';

    
            $stmt = $dbh->prepare ( $sql );
    
            $stmt->bindParam ( ":parent_category_id", $_POST['parent_category_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":recipe_id", $_POST['recipe_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":material_category_name", $_POST['material_category_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":users_id", $_POST['users_id'], PDO::PARAM_INT ); 
            $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );
    
            // sqlを実行
            $stmt->execute ();


            // 処理が完了したら（confirm.php）へリダイレクト
            header("Location: ./edit_parent_material.php?id=" .$_SESSION['recipe_id']);
            
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