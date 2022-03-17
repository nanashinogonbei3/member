<?php 


try {
 
    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';


    $dbh = new PDO($dsn,'root','');
    

    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

   
   
    if (isset($_POST["del"]) && $_POST['del'] == "1") {
        // レコードを削除する
        $sql = 'DELETE FROM materials WHERE id=:id';


        //SQL文を実行する準備をします。
        $stmt = $dbh->prepare($sql);
        

        $stmt->bindValue(':id', $_POST['id'],PDO::PARAM_INT);
   

        $stmt->execute();

        
  
        } else {
        
       
        
        $sql = 'UPDATE materials ';
        // 渡されたid で識別し、is_deleted が 1 or 0 かその状態で(SET セット）割り当てる。
        $sql .= 'SET is_deleted =:is_deleted ';
       
        $sql .= 'WHERE id=:id';
        
        
        //SQL文を実行する準備をします。
        $stmt = $dbh->prepare($sql);
        
        
        $stmt->bindValue(':id', $_POST["id"],PDO::PARAM_INT);
        $stmt->bindValue(':is_deleted', $_POST["is_deleted"],PDO::PARAM_INT);
        

        $stmt->execute();

        }

        // 処理が完了したら画面に遷移する
        header("Location: ./confirm.php?id=" .$_POST['recipe_id']);
        exit;
        

} catch (Exception $e) {
    echo 'DBに接続できませんでした: ',  $e->getMessage(), "\n";
    var_dump($e);

    exit;
}


?>


