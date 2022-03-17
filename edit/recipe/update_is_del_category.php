<?php

// このファイルは、”一時的に” ユーザー登録のカテゴリーを非表示にするファイルです

// is_deleted = 1 のPOSTを受け取ることで、if(empty($v['is_deleted']) で、id_deleted = 0 だけレシピを表示させます
$id = $_POST['recipe_id'];


session_start();
// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');





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
    
     
   
        // カテゴリーのis_deleted（論理削除カラム）のレコードをアップデートするsql文
        $sql = 'UPDATE categories SET is_deleted=:is_deleted ';
        $sql .= 'where id=:id';
       
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);

        // SQL（更新）の実行
        $sql = "UPDATE categories SET is_deleted=:is_deleted WHERE id=:id";

        $stmt = $dbh->prepare ( $sql );

        $stmt->bindParam ( ":is_deleted", $_POST['is_deleted'], PDO::PARAM_STR );
        $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();
           

        // 処理が完了したら画面に遷移する
        header("Location: ./confirm.php?id='".$id."'");
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