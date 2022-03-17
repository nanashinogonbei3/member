<?php



$id = $_POST['id'];

session_start();
// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');


    try {


        $db_product_lists= new Product_lists(); 


        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    

        $dbh = new PDO($dsn, 'root', '');
    
 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

        $sql = "UPDATE product_lists SET is_released=:is_released WHERE id=:id";

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );

        // $stmt->bindParam ( ":maker_id", $_POST['maker_id'], PDO::PARAM_INT );
        $stmt->bindParam ( ":is_released", $_POST['is_released'], PDO::PARAM_INT);
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
    

    ?>