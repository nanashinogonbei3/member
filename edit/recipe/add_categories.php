<?php

session_start();

// radio で✅した値をsql文のインサートを行う
// 受け取った中間テーブルrecipe_categoriesに必要なデータを変数に渡す
$myrecipeId = $_GET['recipe_id'];
$categoryId = $_GET['category_id'];



 try {

        //⓵radio update文 唯一無二なので、アップデートできる
        //⓶chebox  複数選択なので、一旦今あるのをdelete文でカラムを削除して、新たにインサートし直す
        //     1）中間テーブルのcategoryカラムの該当レコードを削除
        //     2）INSERT文を書く
        // Delete文  DELETE FROM recipe_categories WHERE category_id = 10; (例)
        // var_dump($_GET['id']);
        // $category_id = $_GET['id']; GETから受け取るカテゴリーidの変数をDELETE文に代入する
        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
        $dbh = new PDO($dsn, 'root', '');

        // DELETE文
        // レシピIDに属する全てのカテゴリIDを一旦全て削除する
        $sql = "DELETE FROM recipe_categories WHERE my_recipe_id = '".$myrecipeId."' ";

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $dbh->prepare($sql);
    
        $stmt->execute();

        // ⓶DELETEで中間テーブルを削除する
        // カテゴリーid 1    中間テーブル     レシピid 20 ✖ ➡  カテゴリーid 5   
        // カテゴリーid 3    中間テーブル     レシピid 20
        // 
        // レシピid 20 に紐づいている カテゴリーid 3には影響がないので安全これが一般的な方法


        // ⓶中間テーブル・レシピ・カテゴリーテーブルのインサートを行う

        $sql = 'INSERT recipe_categories (my_recipe_id, category_id)';
        $sql .='values(:my_recipe_id, :category_id)';


        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );


        $stmt->bindValue(':my_recipe_id', $categoryId, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', $myrecipeId, PDO::PARAM_STR);


        // sqlを実行
        $stmt->execute ();
            


    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        echo $e->getMessage();
        exit;
    }
   

    ?>