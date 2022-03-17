<?php

session_start();
// 必要なファイルを読み込む
require_once('./class/db/Base.php');
require_once('./class/db/CreateRecipes.php');



    // マイレシピ＊テーブルへ// インスタンス生成
    $db_Myrecipe = new Myrecipes(); 

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
    
     
        // string $update_time, string $release_date, int $is_deleted, int $id_select)
        // マイレシピ*テーブルのレコードをアップデートするsql文
        $sql = 'update my_recipes set is_released=:is_released ';
        $sql .= 'where id=:id';
       
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);

        // SQL（更新）の実行
        $sql = "UPDATE my_recipes SET is_released=:is_released WHERE id=:id";

        $stmt = $dbh->prepare ( $sql );

        $stmt->bindParam ( ":is_released", $_POST['is_released'], PDO::PARAM_STR );
        $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();
           

        // 処理が完了したら（confirm.php）へリダイレクト
        header("Location: ./confirm.php?id=" . $_POST['id']);
        // confirm.php からmy_recipe のid で飛ばされた、$_POST['recipe_id']
        exit;


    } catch (PDOException $e) {
        echo 'my_recipeのDBに接続できません: ',  $e->getMessage(), "\n";
        var_dump($e);
        echo $e->getMessage();
        exit;
    }
    

 ?>