<?php

session_start();


$id = $_SESSION['advice']['recipe_id'];



try {

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    
        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


        $sql = 'INSERT INTO advices(advice, recipe_id, member_id )';
        $sql .='values(:advice, :recipe_id, :member_id )'; 
  

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );


        $stmt->bindValue(':advice',$_SESSION['advice']['advice'],PDO::PARAM_STR);
        $stmt->bindValue(':recipe_id', $_SESSION['advice']['recipe_id'], PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $_SESSION['advice']['member_id'], PDO::PARAM_INT);
    

        // sqlを実行
        $stmt->execute ();

        // 処理が完了したら画面に遷移する
    
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