<?php


    $id = $_POST['id'];



    session_start();
    // 必要なファイルを読み込む
    require_once('../../class/db/Base.php');
    require_once('../../class/db/CreateRecipes.php');

  
if (empty($_POST['amount']) || empty($_POST['coo']) || empty($_POST['price'])
    || empty($_POST['cost_price']) ) {

        // 何も入っていなければ（confirm.php）へリダイレクト
        header("Location: ./confirm.php?id=".$id); 
        exit;

} elseif(!empty($_POST['price']) AND !empty($_POST['cost_price']) 
        AND !empty($_POST['amount']) AND !empty($_POST['coo'])) {

    try {


        $db_product_lists= new Product_lists(); 


        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    

        $dbh = new PDO($dsn, 'root', '');
    
 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

        $sql = "UPDATE product_lists SET amount=:amount,
        coo=:coo, price=:price, cost_price=:cost_price WHERE id=:id";

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );

        
        $stmt->bindParam ( ":amount", $_POST['amount'], PDO::PARAM_STR );
        $stmt->bindParam ( ":coo", $_POST['coo'], PDO::PARAM_STR );
        $stmt->bindParam ( ":price", $_POST['price'], PDO::PARAM_INT );
        $stmt->bindParam ( ":cost_price", $_POST['cost_price'], PDO::PARAM_INT );
        $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();
           

        // 処理が完了したら（confirm.php）へリダイレクト
        header("Location: ./confirm.php?id=" . $_POST['id']);
    
        exit;


    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        var_dump($e);
        echo $e->getMessage();
        exit;
    }

}   
   

?>