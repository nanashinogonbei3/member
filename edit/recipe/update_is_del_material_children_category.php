<?php

session_start();
// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');


// このファイルは、”一時的に” ユーザー登録のカテゴリーを非表示に論理削除するファイルです
// is_deleted = 1 のPOSTを受け取ることで、if(empty($v['is_deleted']) で、id_deleted = 0 だけレシピを表示させます
// 追加のレコードid_deletedがテーブルに追加できなかったので、後述、物理削除で処理しました。


    try {
        // データベースに接続するための文字列（DSN 接続文字列）
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    
        // PDOクラスのインスタンスを作る
        // 引数は、上記のDSN、データベースのユーザー名、パスワード
        // XAMPPの場合はデフォルトでパスワードなし、MAMPの場合は「root」
        $dbh = new PDO($dsn, 'root', '');
    
        // エラーが起きたときのモードを指定する
        // 「PDO::ERRMODE_EXCEPTION」を指定すると、エラー発生時に例外がスローされる
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

    // 論理削除
    if(isset($_POST["del"])) {
   

        // 物理削除（完全削除）
        $sql = 'DELETE FROM material_categories WHERE id=:id';


        //SQL文を実行する準備をします。
        $stmt = $dbh->prepare($sql);
        
        $stmt->bindValue(':id', $_POST['id'],PDO::PARAM_INT);
        
        $stmt->execute();
    
    
    } else {


        $sql = 'UPDATE material_categories';
    
        $sql .= 'WHERE id=:id';
        
        
        //SQL文を実行する準備をします。
        $stmt = $dbh->prepare($sql);
        
        
        $stmt->bindValue(':id', $_POST["id"],PDO::PARAM_INT);
    
    
        $stmt->execute();
    
   
        }

        // 処理が完了したら（confirm.php）へリダイレクト
        header("Location: ./edit_parent_material.php?id=" .$_SESSION['recipe_id']);
        exit;



    } catch (PDOException $e) {
        echo 'my_recipeのDBに接続できません: ',  $e->getMessage(), "\n";
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        echo $e->getMessage();
        exit;
    }
    

    ?>