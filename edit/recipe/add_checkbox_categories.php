<?php
// ここで、ユーザープルダウン・カテゴリ・リストから選択した、レシピに登録するカテゴリを登録します。
session_start();



$recipeId = $_GET['recipe_id'];

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

        // MEMO
        //⓶chebox  複数選択なので、一旦今あるのをdelete文でカラムを削除して、新たにインサートし
        // 直すのが一般的。重複チェックしてインサートする方法はエラーが多いため、使用しない。
        //     1）中間テーブルのcategoryカラムの該当レコードを削除
        //     2）INSERT文を書く

        // DELETE文
        // レシピIDに属する全てのカテゴリIDを中間テーブルから一旦全て削除する
        // $sql = "DELETE FROM recipe_categories WHERE my_recipe_id = ".$recipeId." ";

        // $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $stmt = $dbh->prepare($sql);
    
        // $stmt->execute();

 

        // 中間テーブル（レシピカテゴリー）テーブルのインサートを行う
        $sql = 'INSERT recipe_categories (my_recipe_id, category_id)';
        $sql .='values(:my_recipe_id, :category_id)';

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );

      


        // 複数選択された場合foreachで回して配列入っている値を全てのバインドする
        foreach ($categoryId as $v) {

        $stmt->bindValue(':my_recipe_id', $recipeId, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $v, PDO::PARAM_INT); 

        // sqlを実行
        $stmt->execute ();
    
        }



        // 処理が完了したら$_SESSION['cat']を削除

        unset($_SESSION['cat']);
        header("Location: ./confirm.php?id='".$recipeId."'");
        exit;


    } catch (PDOException $e) {
        echo 'DBに接続できません: ',  $e->getMessage(), "\n";
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        echo $e->getMessage();
        exit;
    }
   

    ?>