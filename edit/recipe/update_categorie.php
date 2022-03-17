<?php
    session_start();

    require_once('../../class/db/Base.php');
    require_once('../../class/db/CreateRecipes.php');

  

    // カテゴリー・テーブルをＵpdateした後、confirm.phpへ戻るために$id変数を作る
    $id = $_SESSION['categories']['recipe_id'];
 
try {


    // カテゴリー＊テーブルへ// インスタンス生成
    $db_categories = new Categories(); 

    // データベースに接続するための文字列（DSN 接続文字列）
    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'UPDATE categories set categorie_name=:categorie_name, ';
    $sql .= 'users_id=:users_id ';
    $sql .= 'where id=:id';

    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);

    
    // SQL（更新）の実行
    $stmt->bindParam ( ":categorie_name", $_SESSION['categories']['categorie_name'], PDO::PARAM_STR );
    $stmt->bindParam ( ":users_id", $_SESSION['categories']['users_id'], PDO::PARAM_INT );
    $stmt->bindParam ( ":id", $_SESSION['categories']['id'], PDO::PARAM_INT );

    // sqlを実行
    $stmt->execute ();

   

    // 処理が完了したら（confirm.php）へリダイレクト
    header("Location: ./confirm.php?id=" . $id);
    exit;


} catch (PDOException $e) {
    echo 'categoriesのDBに接続できません: ',  $e->getMessage(), "\n";
    var_dump($e);
    echo $e->getMessage();
    exit;
}


