<?php
        session_start();


        // カテゴリー・テーブルをＵpdateした後、confirm.phpへ戻るために$id変数を作る
        $id = $_SESSION['categories']['my_recipe_id'];
  
       
try{

   
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
       
        $dbh = new PDO($dsn,'root','');

        // categoriesテーブルをインサートする
        $sql = 'insert into categories (';
        $sql .= 'categories_name,';
        $sql .= 'users_id';
        $sql .= ') values (';
        $sql .= ':categories_name,';
        $sql .= ':users_id';
        $sql .= ')';

        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':categories_name', $_SESSION['categories']['categories_name'], PDO::PARAM_STR);
        $stmt->bindValue(':users_id', $_SESSION['categories']['users_id'], PDO::PARAM_INT);
  
        // recipe_categoriesテーブルからFETCHした値をリレーションする共通値を持たせるidカラムにバインドする

        $stmt->execute();

        // カテゴリーIDを取得する
         // PHP公式マニュアルURL
        // https://www.php.net/manual/ja/pdo.lastinsertid.php

   
        $categoryId = $dbh->lastInsertId();
       
  

        // 中間テーブル recipe_categoriesテーブルのインサート文
        $sql = 'insert into recipe_categories (';
        $sql .= 'my_recipe_id,';
        $sql .= 'category_id';
        $sql .= ') values (';
        $sql .= ':my_recipe_id,';
        $sql .= ':category_id';
        $sql .= ')';

        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当てを行う
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':my_recipe_id', $id, PDO::PARAM_INT);


        // SQLを実行する
        $stmt->execute();
     


        // 処理が完了したら（confirm.php）へリダイレクト
        header("Location: ./confirm.php?id=");
        exit;


} catch (PDOException $e) {
    echo 'categoriesのDBに接続できません: ',  $e->getMessage(), "\n";
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
    echo $e->getMessage();
    exit;
}
 

?>