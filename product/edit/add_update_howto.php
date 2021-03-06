<?php
        session_start(); 

        unset($_SESSION['errrMsg3']);
        unset($_SESSION['howtoLength']);

        $id = $_POST['id'];

        session_start();
        // 必要なファイルを読み込む
        require_once('../../class/db/Base.php');
        require_once('../../class/db/CreateRecipes.php');

        if(empty($_POST['howto_use'])) {
        
        // confirm.phpへリダイレクト
        header("Location: ./confirm.php?id=".$id); 
        exit;
    }

        // 制限値
        $limit = 700;
        //メッセージの変数を初期化
        $errMsg3 = '';
        // 入力された文字列の長さを取得する
        $howto_useLength = strlen($_POST['howto_use']); 
        // 商品説明が、制限値を超えたらエラーを表示する。

    if ($limit < $howto_useLength) {
        $errMsg3 = "使用方法は、700文字以内で入力してください。";
        $_SESSION['errrMsg3'] = $errMsg3;
        $_SESSION['howtoLength'] = $howto_useLength;
        // （confirm.php）へリダイレクト
        header("Location: ./confirm.php?id=".$id); 
        exit;
    }


    try {

        $db_product_lists= new Product_lists(); 

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        

        $sql = "UPDATE product_lists SET howto_use=:howto_use
        WHERE id=:id";

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );

        $stmt->bindParam ( ":howto_use", $_POST['howto_use'], PDO::PARAM_STR);
        $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();
                
        // 処理が完了したら（confirm.php）へリダイレクト
        header("Location: ./confirm.php?id=" . $_POST['id']);
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