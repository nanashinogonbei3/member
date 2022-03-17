<?php

session_start();


    try {

            $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));

            $date = $dt->format('Y-m-d');

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            $dbh = new PDO($dsn,'root','');

            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


    
            // ユーザー定義の材料カテゴリー・テーブルの更新
            $sql = 'UPDATE advices SET 
            advice = :advice,
            recipe_id = :recipe_id,
            member_id =:member_id 
            where id=:id';

    
            $stmt = $dbh->prepare ( $sql );

    
            $stmt->bindParam ( ":advice", $_POST['advice'], PDO::PARAM_STR );
            $stmt->bindParam ( ":recipe_id", $_POST['recipe_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":member_id", $_POST['member_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );
    
            // // sqlを実行
            $stmt->execute ();

   

            header("Location: ./confirm.php?id= ".$id." " );
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