<?php
// このファイルでは、商品（カルダモンetc...）を別小窓ウィンドウから選択したものを、
// 材料にリンクして追加する、目的のファイルです。materialsテーブルにINSERTします。
session_start();


 try {

    if (!empty($_GET['parent_category_id'])) {

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
    
        $dbh = new PDO($dsn, 'root', '');


        if (!empty($_GET["recipe_id"]) ) {

            // カテゴリーテーブルのインサートを行う
            $sql = 'INSERT materials (recipe_id, parent_category_id, material_name, amount, product_id)';
            $sql .='values(:recipe_id, :parent_category_id, :material_name, :amount, :product_id)';

            // SQL文を実行する準備
            $stmt = $dbh->prepare ( $sql );

            $stmt->bindValue(':recipe_id',$_GET["recipe_id"],PDO::PARAM_INT);
            $stmt->bindValue(':parent_category_id',$_GET['parent_category_id'],PDO::PARAM_INT);
            $stmt->bindValue(':material_name', $_GET["material_name"],PDO::PARAM_STR);
            $stmt->bindValue(':amount',$_GET["amount"],PDO::PARAM_STR);
            $stmt->bindValue(':product_id',$_GET["product_id"],PDO::PARAM_INT);

            // sqlを実行
            $stmt->execute ();
          
    
            // セッションに保存した、parent_category_idを削除しておく。
            $_SESSION['parent_category_id'] = '';

            // 処理が完了したら画面に遷移する
            header("Location: ../../edit/recipe/confirm.php?id=" .$_GET['recipe_id']);
            exit;
    

        } else {
            // recipe_idが無ければ、
            header("Location: ../../edit/recipe/confirm.php?id=" .$_GET['recipe_id']);
            exit;

        }


      //  もしも $_GET['parent_category_id']が無ければ、
    } elseif (empty($_GET['parent_category_id'])) {

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
            $dbh = new PDO($dsn, 'root', '');


        if (!empty($_GET["recipe_id"]) ) {

            // カテゴリーテーブルのインサートを行う
            $sql = 'INSERT materials (recipe_id, material_name, amount, product_id)';
            $sql .='values(:recipe_id, :material_name, :amount, :product_id)';

            // SQL文を実行する準備
            $stmt = $dbh->prepare ( $sql );

            $stmt->bindValue(':recipe_id',$_GET["recipe_id"],PDO::PARAM_INT);
            $stmt->bindValue(':material_name', $_GET["material_name"],PDO::PARAM_STR);
            $stmt->bindValue(':amount',$_GET["amount"],PDO::PARAM_STR);
            $stmt->bindValue(':product_id',$_GET["product_id"],PDO::PARAM_INT);

            // sqlを実行
            $stmt->execute ();
          
    
            // セッションに保存した、parent_category_idを削除しておく。
            $_SESSION['parent_category_id'] = '';

            // 処理が完了したら画面に遷移する
            header("Location: ../../edit/recipe/confirm.php?id=" .$_GET['recipe_id']);
            exit;
    

        } else {
            // recipe_idが無ければ、
            header("Location: ../../edit/recipe/confirm.php?id=" .$_GET['recipe_id']);
            exit;

        }


    }


} catch (PDOException $e) {
    echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
    echo $e->getMessage();
    exit;
}
   

    ?>