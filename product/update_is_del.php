<?php

// このファイルはインデックスの管理画面:商品アイテム一覧の商品を一時的に非表示にするためのものです。
// is_deleted=0の場合は、表示、is_deleted=1 の場合は非表示します。
// そして、商品一覧（公の画面上で、）WHERE is_deleted=0 の商品アイテムだけを一覧に表示させます
// is_deleted=1にした商品アイテムは、一時的に非表示になります。これを論理削除といい、
// 当該行が削除されたことをデータとして表現することで削除されたものとみなすこと
// （＝データは記憶媒体に残ったまま） 、一方で物理削除は完全に削除してしまう事です～♪



$id = $_POST['id'];
// 商品idをUPDATE文に代入するために、変数idを作成して代入する
// 受け取った、商品id 何の商品か？のidを、商品テーブルカラムと同じ$id = （idカラム）
// にいれる

$is_deleted = $_POST['is_deleted'];


session_start();



    try {


        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');


        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    

        $dbh = new PDO($dsn, 'root', '');
    
 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

 
        $sql = "UPDATE product_lists SET id=:id, is_deleted=:is_deleted WHERE id= '" . $id . "' ";
        echo $sql;
       
        // UPDATE product_lists SET id=:id, maker_id=:maker_id WHERE id= '18'
        // 商品id 18番 成功 この商品の、メーカーid をアップロードする

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );


        $stmt->bindParam ( ":is_deleted", $is_deleted, PDO::PARAM_INT );
        $stmt->bindParam ( ":id", $id, PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();


            // 処理が完了したら（confirm.php）へリダイレクト
            header("Location: ./index.php?pid=".$id); 
            exit;


    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        var_dump($e);
        // echo $e->getMessage();
        exit;
    }
    

    ?>