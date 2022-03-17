<?php 
session_start();

$id = $_POST['recipe_id'];


$id= $_SESSION['advice']['recipe_id'];

try {
 
    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';


    $dbh = new PDO($dsn,'root','');
    

    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

   
   
    if (isset($_POST["del"])) {
          
            $sql = 'DELETE FROM advices WHERE id=:id';

            //SQL文を実行する準備をします。
            $stmt = $dbh->prepare($sql);

            $stmt->bindValue(':id', $_POST['id'],PDO::PARAM_INT);

            $stmt->execute();
        
  
        } else {

            $sql = 'UPDATE materials ';
        
            $sql .= 'WHERE id=:id';
            
            //SQL文を実行する準備をします。
            $stmt = $dbh->prepare($sql); 
            
            $stmt->bindValue(':id', $_POST["id"],PDO::PARAM_INT);

            $stmt->execute();

        }

        // 処理が完了したら画面に遷移する
        header("Location: ./confirm.php?id= ".$id." " );
        exit;
        

} catch (Exception $e) {
    echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
    echo $e->getMessage();
    exit;
}
