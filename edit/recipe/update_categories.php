<?php


    try {

            $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
            $date = $dt->format('Y-m-d');

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            $dbh = new PDO($dsn,'root','');

            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


            // カテゴリー・テーブルの更新
            $sql = 'UPDATE categories set categories_name=:categories_name,
            users_id=:users_id, parent_category_id=:parent_category_id
            where id=:id';

    
            $stmt = $dbh->prepare ( $sql );
    
            $stmt->bindParam ( ":categories_name", $_POST['categories_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":users_id", $_POST['users_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":parent_category_id", $_POST['parent_category_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );
    
            // // sqlを実行
            $stmt->execute ();

           

            // // 処理が完了したら（confirm.php）へリダイレクト
            header("Location: ./confirm.php?id=");
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