<?php

        session_start();


        // $_GET['id']は、連想配列に入っているので、foreachでグルグル回して取り出さなければならない。
        $parent_categoryId  = $_GET['id'];

        if(empty($_GET["recipe_id"])) {
                header("Location: ./edit_material_confirm.php?id=" .$_GET['recipe_id']);
                exit();

}

        try {
            // データベースに接続するための文字列（DSN 接続文字列）
            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';
        
            $dbh = new PDO($dsn, 'root', '');
        
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


   
    if (!empty($_GET["id"]) ) {

            $sql = "UPDATE materials SET recipe_id=:recipe_id, material_name=:material_name, 
            amount=:amount, parent_category_id=:parent_category_id
            WHERE id=:id";

        
            // SQL文を実行する準備
            $stmt = $dbh->prepare($sql);

            foreach($parent_categoryId as $v) {

            $stmt->bindParam ( ":recipe_id", $_GET['recipe_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":material_name", $_GET['material_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":amount", $_GET['amount'], PDO::PARAM_STR );
            $stmt->bindParam ( ":parent_category_id", $v, PDO::PARAM_INT );
            $stmt->bindParam ( ":id", $_GET['material_id'], PDO::PARAM_INT );

            // sqlを実行
            $stmt->execute ();

            }

       // もしも親の材料カテゴリー$_GET['id']が空だったら、削除のための上書きを行う。
    } elseif (empty($_GET["id"])) {


            $sql = "UPDATE materials SET recipe_id=:recipe_id, material_name=:material_name, 
            amount=:amount WHERE id=:id";

        
            // SQL文を実行する準備
            $stmt = $dbh->prepare($sql);


            $stmt->bindParam ( ":recipe_id", $_GET['recipe_id'], PDO::PARAM_INT );
            $stmt->bindParam ( ":material_name", $_GET['material_name'], PDO::PARAM_STR );
            $stmt->bindParam ( ":amount", $_GET['amount'], PDO::PARAM_STR );
            $stmt->bindParam ( ":id", $_GET['material_id'], PDO::PARAM_INT );
           

            // sqlを実行
            $stmt->execute ();

         

    }
            // 処理が完了したら（confirm.php）へリダイレクト
            header("Location: ./confirm.php?id=" . $_GET['recipe_id']);
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