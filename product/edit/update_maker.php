<?php

// このファイルでは、商品のメーカー製造会社の変更を目的としています




// 送信データを受け取る

$id = $_GET['pid'];
// 商品idをUPDATE文に代入するために、変数idを作成して代入する
// 受け取った、商品id 何の商品か？のidを、商品テーブルカラムと同じ$id = （idカラム）
// にいれる

$maker_id = $_GET['maker_id'];
// baindValue で使う

session_start();
// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');


    try {


        $db_product_lists= new Product_lists(); 


        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    

        $dbh = new PDO($dsn, 'root', '');
    
 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

 
        $sql = "UPDATE product_lists SET id=:id, maker_id=:maker_id WHERE id= '" . $id . "' ";
        echo $sql;
     
        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );


        $stmt->bindParam ( ":maker_id", $maker_id, PDO::PARAM_INT );
        $stmt->bindParam ( ":id", $id, PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();


            // 処理が完了したら（confirm.php）へリダイレクト
            header("Location: ./confirm.php?id=".$id); 
            exit;


    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        var_dump($e);
        exit;
    }
    

    ?>