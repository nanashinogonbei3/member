<?php
    session_start(); 

    unset($_SESSION['errrMsg2']);
    unset($_SESSION['efficLength']);

    $id = $_POST['id'];

    session_start();
    // 必要なファイルを読み込む
    require_once('../../class/db/Base.php');
    require_once('../../class/db/CreateRecipes.php');

    if(empty($_POST['efficacy']) ) {
        
        // （confirm.php）へリダイレクト
        header("Location: ./confirm.php?id=".$id); 
        exit;
    }

    // 制限値
    $limit = 700;
    //メッセージの変数を初期化
    $errMsg2 = '';
    // 入力された文字列の長さを取得する
    $efficacyLength = strlen($_POST['efficacy']); 
    // 商品説明が、制限値を超えたらエラーを表示する。

    if ($limit < $efficacyLength ) {
        $errMsg2 = "効能は、700文字以内で入力してください。";
        $_SESSION['errrMsg2'] = $errMsg2;
        $_SESSION['efficLength'] = $efficacyLength;
        // （confirm.php）へリダイレクト
        header("Location: ./confirm.php?id=".$id); 
        exit;
    }



    try {

        $db_product_lists= new Product_lists(); 

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        

        $sql = "UPDATE product_lists SET  
        efficacy=:efficacy
        WHERE id=:id";

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );

       
        $stmt->bindParam ( ":efficacy", $_POST['efficacy'], PDO::PARAM_STR );
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